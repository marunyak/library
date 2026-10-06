<?php
class AndyLib{

  protected $db;

  function __construct(){

  }

  /**
     * Return factorial of number.
     * Results above 20! do not fit in an integer and are returned as float.
     * @param integer $number The main number, must be >= 0
     * @param integer $count  Accumulator for the recursion
     * @throws InvalidArgumentException When $number is negative
  */

  public function factorl($number, $count = 1) {
    $number = (int) $number;
    if ($number < 0) throw new InvalidArgumentException('Factorial is not defined for negative numbers');
    if ($number === 0) return $count;
    return $this->factorl($number - 1, $count * $number);
  }

  /**
     * Return unique id (32 hex chars, cryptographically random).
  */

  public function unqID() {
    return bin2hex(random_bytes(16));
  }

  /**
     * Return month.
     * @param integer $month Number of month
  */

  public function getMonth($month) {
    $month = intval($month);
    $monthes = [1 => 'January',2 => 'February',3 => 'March',
                4 => 'April',  5 => 'May',6 => 'June',7 => 'July',
                8 => 'August', 9 => 'September',10 => 'October',
                11 => 'November', 12 => 'December'];
    if(array_key_exists($month,$monthes)) return $monthes[$month];
    else return false;
  }

  /**
     * Return zodiak.
     * @param string $birthday 'Day.Month', e.g. '5.03' or '05.3'
     * @return string|false Sign name or false for an invalid date
  */

  public function zodiak($birthday) {
    $parts = explode('.', (string) $birthday);
    if (count($parts) < 2 || !ctype_digit($parts[0]) || !ctype_digit($parts[1])) return false;
    $day   = (int) $parts[0];
    $month = (int) $parts[1];
    // 2000 is a leap year, so 29.2 is accepted.
    if (!checkdate($month, $day, 2000)) return false;

    // Start date ('Day.Month') of each sign; a sign lasts until the next one starts.
    $zodiac = ['21.1'  => 'Водолей',  '19.2'  => 'Рыбы',     '21.3'  => 'Овен',
               '21.4'  => 'Телец',    '21.5'  => 'Близнецы', '22.6'  => 'Рак',
               '23.7'  => 'Лев',      '24.8'  => 'Дева',     '24.9'  => 'Весы',
               '24.10' => 'Скорпион', '23.11' => 'Стрелец',  '22.12' => 'Козерог'];
    $date   = $month * 100 + $day;
    $result = 'Козерог';
    foreach ($zodiac as $start => $sign) {
      [$startDay, $startMonth] = explode('.', $start);
      if ($date >= (int) $startMonth * 100 + (int) $startDay) $result = $sign;
    }
    return $result;
  }

  /**
     * Return your name of browser.
     * @param string|null $browser User agent, defaults to $_SERVER['HTTP_USER_AGENT']
  */

  public function getBrowser($browser = null) {
    $browser = (string) ($browser ?? ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    // Order matters: Edge and Opera UAs contain "Chrome", Chrome UAs contain "Safari".
    if(strpos($browser, "Edg")          !== false) return "Microsoft Edge";
    if(strpos($browser, "OPR")          !== false ||
       strpos($browser, "Opera")        !== false) return "Opera";
    if(strpos($browser, "Firefox")      !== false ||
       strpos($browser, "FxiOS")        !== false) return "Mozilla Firefox";
    if(strpos($browser, "Chrome")       !== false ||
       strpos($browser, "CriOS")        !== false) return "Google Chrome";
    if(strpos($browser, "MSIE")         !== false ||
       strpos($browser, "Trident")      !== false) return "Internet Explorer";
    if(strpos($browser, "Safari")       !== false) return "Safari";
    return "Неизвестный";
  }

  /**
     * Print var_dump output in a readable, HTML-safe form.
     * @param mixed $var Take any information
  */

  public function dump($var) {
    ob_start();
    var_dump($var);
    echo '<pre>'.htmlspecialchars(ob_get_clean(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</pre>';
  }

  /**
     * Return paths of all files with the given name, searching recursively.
     * @param string $file Name of file
     * @param string $dir  Path of directory
     * @return string[]
  */

  public function findFile($file, $dir) {
    $found = [];
    $dir_hndl = @opendir($dir);
    if ($dir_hndl === false) return $found;
    while (($name = readdir($dir_hndl)) !== false) {
      if ($name == '.' || $name == '..') continue;
      $path = $dir.'/'.$name;
      if (is_dir($path) && !is_link($path)) $found = array_merge($found, $this->findFile($file, $path));
      else if (is_file($path) && $file === $name) $found[] = $path;
    }
    closedir($dir_hndl);
    return $found;
  }

  /**
     * Return paths of all directories with the given name, searching recursively.
     * @param string $dirr Name of directory
     * @param string $dir  Path of directory
     * @return string[]
  */

  public function findDirectory($dirr, $dir) {
    $found = [];
    $dir_hndl = @opendir($dir);
    if ($dir_hndl === false) return $found;
    while (($name = readdir($dir_hndl)) !== false) {
      if ($name == '.' || $name == '..') continue;
      $path = $dir.'/'.$name;
      if (!is_dir($path) || is_link($path)) continue;
      if ($dirr === $name) $found[] = $path;
      $found = array_merge($found, $this->findDirectory($dirr, $path));
    }
    closedir($dir_hndl);
    return $found;
  }

  /**
     * Show Message.
     * @param array $arr    where $key is name of class, $val is value of div
     * @param bool  $escape Escape $val; pass false only for trusted HTML
  */

  public function showMsg($arr, $escape = true) {
    $body = '';
    if (!empty($arr)) {
      foreach($arr as $key => $val) {
        $content = $escape ? $this->escape($val) : $val;
        $body .= '<div class="'.$this->escape($key).'">'.$content.'</div>';
      }
      $callback  = <<<HTML
        <div class="header"></div>
          <div class="body">
              {$body}
          </div>
        <div class="footer"></div>
HTML;
      return $callback;
    }
    else return false;
  }

  /**
     * Generate random code of 4 to 7 alphanumeric chars (cryptographically random).
  */

  public function generateCode() {
    $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = random_int(4, 7);
    $max = strlen($string) - 1;
    $str = '';
    for($i = 0;$i < $count;$i++){
      $str .= $string[random_int(0, $max)];
    }
    return $str;
  }

  /**
    * Return url with bare & escaped as &amp; for use in HTML.
    * Existing entities (&amp;, &#123;, ...) are left as is, so the call is idempotent.
    * @param string $sUrl url
  */

  public function cleanAmp($sUrl) {
    return preg_replace('/&(?!(?:[a-zA-Z][a-zA-Z0-9]*|#[0-9]+|#[xX][0-9a-fA-F]+);)/', '&amp;', (string) $sUrl);
  }

  /**
    * Return your age;
    * @param integer $day day
    * @param integer $month month
    * @param integer $year year
    * @return integer|false Age in full years, false for an invalid or future date
  */

  public function getAge($day, $month, $year) {
    $day = (int) $day; $month = (int) $month; $year = (int) $year;
    if (!checkdate($month, $day, $year)) return false;
    $birthday = (new DateTimeImmutable())->setDate($year, $month, $day)->setTime(0, 0);
    $today = new DateTimeImmutable('today');
    if ($birthday > $today) return false;
    return $birthday->diff($today)->y;
  }

  /**
    * Return client ip (REMOTE_ADDR, IPv4 or IPv6) or 'localhost' if it is missing or invalid.
    * Proxy headers like X-Forwarded-For are ignored on purpose: they are client-controlled.
  */

  public function getIp() {
    $ip = filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP);
    return $ip !== false ? $ip : 'localhost';
  }

  /**
    * Return current host without scheme, leading "www." and path, lowercased.
  */

  public function getHost() {
    $url = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $url = preg_replace('#^https?://#i', '', $url);
    $url = preg_replace('/^www\./i', '', $url);
    $eX = explode('/',$url);
    return mb_strtolower($eX[0]);
  }

  /**
    * Set cache;
    * @param string  $name name of cache file (letters, digits, "_", "-", ".")
    * @param mixed   $arr  any JSON-serializable value
    * @param string  $conf path to cache directory
    * @param integer $time lifetime of cache in minutes
    * @return bool
  */

  public function setCache($name, $arr, $conf = __DIR__.'/../localhost/cache/', $time = 30) {
    $path = $this->storagePath($conf, $name, '.sys');
    if ($path === false || !$this->ensureDir($conf)) return false;
    $json = json_encode(['expires' => time() + 60 * (int) $time, 'data' => $arr]);
    if ($json === false) return false;
    return file_put_contents($path, $json, LOCK_EX) !== false;
  }

  /**
    * Get cache;
    * @param string $name name of cache file
    * @param string $conf path to cache directory
    * @return mixed Cached value, or false if missing or expired
  */

  public function getCache($name, $conf = __DIR__.'/../localhost/cache/') {
    $path = $this->storagePath($conf, $name, '.sys');
    if ($path === false || !is_file($path)) return false;
    $data = json_decode((string) @file_get_contents($path), true);
    if (!is_array($data) || !array_key_exists('data', $data)) return false;
    if (time() >= (int) ($data['expires'] ?? 0)) {
      @unlink($path);
      return false;
    }
    return $data['data'];
  }

  /**
    * Writing logs: appends one JSON object per line.
    * @param string  $name name of log file
    * @param string  $msg log information
    * @param string  $conf path to log directory
    * @return bool
  */

  public function logs($name, $msg, $conf = __DIR__.'/../localhost/logs/') {
    $path = $this->storagePath($conf, $name, '.log');
    if ($path === false || !$this->ensureDir($conf)) return false;
    $entry = ['message' => $msg,
              'ip'      => $this->getIp(),
              'browser' => $this->getBrowser($_SERVER['HTTP_USER_AGENT'] ?? ''),
              'file'    => $_SERVER['PHP_SELF'] ?? '',
              'query'   => $_SERVER['HTTP_REFERER'] ?? '',
              'time'    => time()];
    $line = json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if ($line === false) return false;
    return file_put_contents($path, $line."\n", FILE_APPEND | LOCK_EX) !== false;
  }

  /**
    * Return tags
    * @param string       $tagName div, span, img, link or h1-h6
    * @param string       $string  text inside tag, img src or link href
    * @param string|array $class   class name(s); for link it is the rel value (default "stylesheet")
    * @param bool         $escape  escape tag content; pass false only for trusted HTML
  */

  public function htmlTags($tagName, $string = '', $class = '', $escape = true) {
    if(is_array($class)) $class = implode(' ',$class);
    $class = (string) $class;

    if($tagName == 'img')  return "<img src='{$this->escape($string)}'".$this->attr('class', $class)."/>";
    if($tagName == 'link') return "<link rel='".$this->escape($class !== '' ? $class : 'stylesheet')."' href='{$this->escape($string)}'/>";
    if(!in_array($tagName, ['div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) return false;

    $content = $escape ? $this->escape($string) : $string;
    return "<{$tagName}".$this->attr('class', $class).">{$content}</{$tagName}>";
  }

  /**
    * Return converted string with UTF-8
    * @param string  $str string
  */

  public function mb_ucwords($str) {
    $str = mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
    return ($str);
  }

  /**
    * Return array with information from .xlsx file
    * @param string  $file name of xlsx file
    * @param string  $dir  name of directory where PHPExcel file located
  */

  public function importXlsx($file, $dir = ENGINE_DIR."/lib/phpexcel/PHPExcel.php") {
    require_once $dir;
    $getFile = PHPExcel_IOFactory::load($file);
    $getFile->setActiveSheetIndex(0);
    $aSheet = $getFile->getActiveSheet();

    $array = [];
    $iRow = 0;
    foreach ($aSheet->getRowIterator() as $row) {
        $iRow++;
        $cellIterator = $row->getCellIterator();
        $iCell = 0;
        foreach ($cellIterator as $key=>$cell) {
            $iCell++;
            if($cell->getValue() instanceof PHPExcel_RichText) {
                $val = $cell->getValue()->getPlainText();
            } else {
                $val = $cell->getValue();
            }
            $array[$iRow][$key] = $val?$val:'';
        }
    }
    return $array;
  }

  /**
    * Has ssl sertification
  */

  public function has_ssl() {
    return !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
  }

  /**
    * Set a value deep inside an array, creating missing levels.
    * @param array  $arr        array to change (by reference)
    * @param array  $arr_keys   path of keys; ['main'] means the root of $arr
    * @param string $main_key   key to set at the end of the path
    * @param mixed  $main_value value to set; '' or null sets an empty array
    * @throws InvalidArgumentException When a key on the path holds a non-array value
  */

  public function insertArrRecursive(&$arr, $arr_keys, $main_key, $main_value = '') {
    $node = &$arr;
    $arr_keys = array_values((array) $arr_keys);
    if (($arr_keys[0] ?? null) !== 'main') {
      foreach ($arr_keys as $key) {
        if (!isset($node[$key])) $node[$key] = [];
        else if (!is_array($node[$key])) throw new InvalidArgumentException("Key '{$key}' does not hold an array");
        $node = &$node[$key];
      }
    }
    $node[$main_key] = ($main_value === '' || $main_value === null) ? [] : $main_value;
  }

  /**
    * Flatten a nested array into chains of keys, e.g. ['a' => ['b' => 1]] gives ['[a][b]' => 1].
    * @param array  $arr    recursive or normal array
    * @param string $prefix chain of parent keys, used by the recursion
    * @return array
  */

  public function keysArr($arr, $prefix = '') {
    $newArr = [];
    foreach ($arr as $key => $val) {
      $chain = $prefix.'['.$key.']';
      if (is_array($val)) $newArr += $this->keysArr($val, $chain);
      else                $newArr[$chain] = $val;
    }
    return $newArr;
  }

  /**
    * Save array as a .php config file that sets the variable $namec.
    * Keys already set in $namec when the file is included win over the saved ones.
    * @param array    $save_con   array
    * @param string   $file  name of file
    * @param string   $dir  path to file
    * @param string   $namec  name of variable, defaults to $file
    * @return bool
    * @throws InvalidArgumentException When the variable name is not a valid PHP identifier
  */

  public function savetheConfig($save_con, $file, $dir, $namec=false) {
    $namec = $namec?$namec:$file;
    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $namec) || $namec === 'this') {
      throw new InvalidArgumentException('Config name must be a valid PHP variable name');
    }
    $code = "<?php\n\${$namec} = (\${$namec} ?? []) + ".var_export((array) $save_con, true).";\n";

    // Write to a temp file and rename it, so a half-written config is never included.
    $target = $dir.$file;
    $tmp = $target.'.'.bin2hex(random_bytes(4)).'.tmp';
    if (file_put_contents($tmp, $code, LOCK_EX) === false) return false;
    if (!rename($tmp, $target)) {
      @unlink($tmp);
      return false;
    }
    return true;
  }

  /**
    * Escape a value for HTML text or a quoted attribute.
  */

  protected function escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }

  /**
    * Return ` name='value'` or an empty string when the value is empty.
  */

  protected function attr($name, $value) {
    return $value === '' ? '' : " {$name}='{$this->escape($value)}'";
  }

  /**
    * Return file path inside $dir, or false when $name could escape the directory.
  */

  protected function storagePath($dir, $name, $ext) {
    $name = (string) $name;
    if (!preg_match('/^[A-Za-z0-9_.-]+$/', $name) || strpos($name, '..') !== false) return false;
    return rtrim((string) $dir, '/\\').'/'.$name.$ext;
  }

  protected function ensureDir($dir) {
    return is_dir($dir) || @mkdir($dir, 0750, true) || is_dir($dir);
  }
}
