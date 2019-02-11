<?php
class AndyLib{

  protected $db;

  function __construct(){

  }

  /**
     *Return factorial of number.
     * @param integer $number The main number
  */

  public function factorl($number,$count = 1){
    while($number > 0)  return factorl($number-1,$count*$number);
    return $count;
  }

  /**
     * Return unique id.
  */

  public function unqID(){
    return uniqid(md5(md5(time())));
  }

  /**
     *Return month.
     * @param integer $month Number of month
  */

  public function getMonth($month){
    $month = intval($month);
    $monthes = [1 => 'January',2 => 'February',3 => 'March',
                4 => 'April',  5 => 'May',6 => 'June',7 => 'July',
                8 => 'August', 9 => 'September',10 => 'October',
                11 => 'November', 12 => 'December'];
    if(array_key_exists($month,$monthes)) return $monthes[$month];
    else return false;
  }

  /**
     *Return zodiak.
     * @param string $birthday 'Day.Month'
  */

  public function zodiak($birthday){
    $birthday = explode('.',$birthday);
    preg_replace('/^0/',' ',$birthday[1],$birthday[1]);
    $zodiac = ['21.3|20.4'   => 'Овен',    '21.4|20.5'   => 'Телец',
               '21.5|21.6'   => 'Близнецы','22.6|22.7'   => 'Рак',
               '23.7|23.8'   => 'Лев',     '24.8|23.9'   => 'Дева',
               '24.9|23.10'  => 'Весы',    '24.10|22.11' => 'Скорпион',
               '23.11|21.12' => 'Стрелец', '22.12|20.1'  => 'Козерог',
               '21.1|18.2'   => 'Водолей', '18.2|20.3'   => 'Рыбы'];
    foreach ($zodiac as $key => $value) {
      $arr = explode('|',$key);
      $arr1 = explode('.',$arr[0]);
      $arr2 = explode('.',$arr[1]);
      if((intval($arr1[1]) === intval($birthday[1]) && intval($arr1[0]) <= intval($birthday[0])) ||
         (intval($arr2[1]) === intval($birthday[1]) && intval($arr2[0]) >= intval($birthday[0]))){
           return $value;
         }
    }
    return false;
  }

  /**
     *Return your name of browser.
     * @param string $browser Take information from $_SERVER ['HTTP_USER_AGENT']
  */

  public function getBrowser($browser){
    if(strpos($browser, "Firefox")    !== false) $browser = "Mozilla Firefox";
    elseif(strpos($browser, "Opera")  !== false) $browser = "Opera";
    elseif(strpos($browser, "Chrome") !== false) $browser = "Google Chrome";
    elseif(strpos($browser, "MSIE")   !== false) $browser = "Internet Explorer";
    elseif(strpos($browser, "Safari") !== false) $browser = "Safari";
    else                                         $browser = "Неизвестный";
    return $browser;
  }

  /**
     *Return var_dumped in comfortable form.
     * @param mixed $var Take any information
  */

  public function dump($var){
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
  }

  /**
     *Return file or matches if its exists.
     * @param string $file Name of file
     * @param string $dir  Path of directory 
  */

  public function findFile($file,$dir){
    $dir_hndl = opendir($dir);
    while(($name = readdir($dir_hndl)) !== false){
      if($name == '.' || $name == '..') continue;
      if(is_dir($dir.'/'.$name)) findFile($file,$dir.'/'.$name);
      else if(is_file($dir.'/'.$name) && $file === $name) echo $dir.'/'.$name.'<br>';
    }
    closedir($dir_hndl);
  }

  /**
     *Return directory or matches if its exists.
     * @param string $dirr Name of directory 
     * @param string $dir  Path of directory 
  */

  public function findDirectory($dirr,$dir){
  $dir_hndl = opendir($dir);
    while(($name = readdir($dir_hndl)) !== false){
      if($name == '.' || $name == '..') continue;
      if(is_dir($dir.'/'.$name)  && $dirr === $name) echo $dir.'/'.$name.'<br>';
      if(is_dir($dir.'/'.$name)) findDirectory($dirr,$dir.'/'.$name);
    }
    closedir($dir_hndl);
  }

  /**
     *Show Message.
     * @param array $arr where $key is name of class, $val is value of div
  */

  public function showMsg($arr){
    $body = '';
    if(!empty($arr)){
      for($arr as $key => $val){
        $body .= '<div class="'.$key.'">'.$val.'</div>';
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
     *Generate unique code
  */

  public function generateCode(){
    $string = 'abcdefghijklmnopqrstuvwyzABCDEFGHIJKLMNOPQRSTUVWYZ0123456789';
    $count = rand(4,7);
    $str = '';
    $length = strlen($string);
    for($i = 0;$i < $count;$i++){
      $str .= substr($string,rand(1,$length),1);
    }
    $arr = explode(',',$str);
    shuffle($arr);
    return implode('',$arr);
  }

  /**
    *Return string without &amp;
    * @param string $sUrl url
  */

  public function cleanAmp($sUrl){
    return str_replace('&', '&amp;', $sUrl);
  }

  /**
    *Return your age;
    * @param integer $day day
    * @param integer $month month
    * @param integer $year year
  */

  public function getAge($day,$month,$year){
    if($month > date('m') || ($month == date('m') && $day > date('d'))) return (date('Y') - $year - 1);
    else return (date('Y') - $year);
  }

  /**
    *Return ip
  */

  public function getIp(){
    if      (filter_var($SERVER['REMOTE_ADDR'],FILTER_FLAG_IPV4,FILTER_FLAG_IPV4)) return filter_var($SERVER['REMOTE_ADDR'],FILTER_FLAG_IPV4,FILTER_FLAG_IPV4);
    else if (filter_var($SERVER['REMOTE_ADDR'],FILTER_FLAG_IPV4,FILTER_FLAG_IPV6)) return filter_var($SERVER['REMOTE_ADDR'],FILTER_FLAG_IPV4,FILTER_FLAG_IPV6);
    else return 'localhost';
  }

  /**
    *Return current host
  */

  public function getHost(){
    $Url = $_SERVER['HTTP_HOST'];
    $url = str_replace('https://','',$Url);
    $url = str_replace('http://','',$Url);
    $url = str_replace('www.','',$Url);
    $eX = explode('/',$url);
    return mb_strtolower($eX[0]);
  }

  /**
    *Set cache;
    * @param string $name name of cache file
    * @param array  $arr array with information
    * @param string $conf path to cache file
    * @param integer $time time of rewrite cache 
  */

  public function setCache($name,$arr,$conf = '../localhost/cache/',$time = 30){
    if(!is_dir($conf)) mkdir($conf, 0750, true);
    if(is_array($arr) || is_int($arr)){
      if(file_exists($conf.$name.'.sys') && (filemtime($conf.$name.'.sys') > (time() - 60 * $time))) @file_put_contents($conf.$name.'.sys',serialize($arr));
      else{
        $fp = fopen($conf.$name.'.sys', 'wb+');
        fwrite($fp, serialize($arr));
        fclose($fp);
        @chmod($conf.$name.'.sys',0666);
      }
    }
  }

  /**
    *Get cache;
    * @param string $name name of cache file
    * @param array  $arr array with information
  */

  public function getCache($name,$conf = '../localhost/cache/'){
    if(!is_dir($conf)) return false;
    if(file_exists($conf.$name.'.sys')){
      $data = @file_get_contents($conf.$name.'.sys');
      if($data){
        return unserialize($data);
      }
    }
    return false;
  }

  /**
    *Writing logs
    * @param string  $name name of log file
    * @param string  $msg log information
    * @param string  $conf path to log file
  */

  public function logs($name,$msg,$conf = '../localhost/logs/'){
    $path = $conf.$name.'.log';
    if(!is_dir($conf)) mkdir($conf, 0750, true);
    if(file_exists($path)) $array = unserialize(@file_get_contents($path));
    else {
      $fp = fopen($path, 'wb+');
      fclose($fp);
      @chmod($path,0666);
    }
    $array[] = ['message' => $msq,'ip' => get_ip(),'browser' => getBrowser($_SERVER['HTTP_USER_AGENT']),
                'file' => $_SERVER['PHP_SELF'],'query' => $_SERVER['HTTP_REFERER'],'time' => time()];

    $array = serialize($array);
    @file_put_contents($path,$array);
  }

  /**
    *Return tags
    * @param string  $tagName name of tag
    * @param string  $string info inside tag
    * @param string  $class name of class
  */

  public function htmlTags($tagName,$string = '',$class = ''){
    if(is_array($class))                   $class = implode(' ',$class);
    if($class != '' && $tagName != 'link') $class = "class='{$class}'";

         if($tagName == 'div')  return "<div {$class}>{$string}</div>";
    else if($tagName == 'span') return "<span {$class}>{$string}</span>";
    else if($tagName == 'img')  return "<img src='{$string}' {$class}/>";
    else if($tagName == 'link') return "<link {$class} href='{$string}'/>";
    else if(preg_match('/h[1-6]/',$tagName,$match)) return "<{$match[0]} {$class}>{$string}</{$match[0]}>";
    else return false;
  }

  /**
    *Return converted string with UTF-8
    * @param string  $str string
  */

  public function mb_ucwords($str) {
    $str = mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
    return ($str);
  }

  /**
    *Return array with information from .xlsx file
    * @param string  $file name of xlsx file
    * @param string  $dir  name of directory where PHPExcel file located
  */

  public function importXlsx($file,$dir = ENGINE_DIR."/lib/phpexcel/PHPExcel.php") {
    require_once $dir;
    $getFile = PHPExcel_IOFactory::load($file);
    $getFile->setActiveSheetIndex(0);
    $aSheet = $getFile->getActiveSheet();

    $array = [];
    $iRow = 0;
    foreach($aSheet->getRowIterator() as $row) {
        $iRow++;
        $cellIterator = $row->getCellIterator();
        $iCell = 0;
        foreach($cellIterator as $key=>$cell) {
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
    *has ssl sertification
  */

  public function has_ssl() {
     if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) return true;
     else return false;
  }

  /**
    *Return array with information from .xlsx file
    * @param array   $arr recursive or normal array
    * @param string  $dir  name of directory where PHPExcel file located
    * @param string  $file name of xlsx file
    * @param string  $dir  name of directory where PHPExcel file located
    * @param string  $file name of xlsx file
  */

  public function insertArrRecursive(&$arr,$arr_keys,$main_key,$main_value = '',$tempArr = ''){
    if($arr_keys[0] == 'main'){
      if(empty($main_value)) $arr[$main_key]  = [];
      else                   $arr[$main_key]  = $main_value;
      return;
    }
    foreach($arr_keys as $key => $value) {
      if(empty($tempArr)) $tempArr = &$arr[$value];
      else                $tempArr = &$tempArr[$value];
      if($value == end($arr_keys)){
        if(empty($main_value))  $tempArr[$main_key] = [];
        else                    $tempArr[$main_key] = $main_value;
        return;
      }
    }
  }

  /**
    *Return chain of keys for select
    * @param array   $arr  recursive or normal array
    * @param array   $newArr  array with chain of keys
  */

  public function keysArr($arr,$newArr = [], $str = [],$counter = 1){
    foreach($arr as $key => $val){
      if(!is_array($val)){
        array_push($str,"[".$key."]");
        $newArr[implode('',$str)] = $val;
        if($counter > 1) array_pop($str);
        else if(count($str) >= 1 && $counter == 1) array_pop($str);
      }
      else if(is_array($val)){
        array_push($str,"[".$key."]");
        $bigArr  = keysArr($val,$newArr,$str,++$counter);
        $newArr  = array_merge($newArr,$bigArr[0]);
        $str     = $bigArr[1];
        $counter = $bigArr[2];
        if($counter > 1)  array_pop($str);
        --$counter;
      }
    }
    if($counter == 1) return $newArr;
    else return [$newArr,$str,$counter];
  }

  /**
    *Set new array in .php file
    * @param array    $save_con   array
    * @param string   $file  name of file
    * @param string   $namec  new name of file
    * @param string   $dir  path to file
  */

  public function savetheConfig($save_con, $file, $dir, $namec=false) {
    $namec = $namec?$namec:$file;
    $find = array();
    $replace = array();

    $find[] = "'\r'";
    $replace[] = "";
    $find[] = "'\n'";
    $replace[] = "";

    $handler = fopen($dir.$file, "w");
    fwrite($handler, "<?php\n\${$namec} += array (\n");
    foreach($save_con as $name => $value) {
      $value = str_replace("$", "&#036;", $value);
      $value = str_replace("{", "&#123;", $value);
      $value = str_replace("}", "&#125;", $value);
      $value = str_replace(chr(0), "", $value);
      $value = str_replace(chr(92), "", $value);
      $value = str_ireplace("base64_decode", "base64_dec&#111;de", $value);
      $value = addslashes($value);

      $name = str_replace("$", "&#036;", $name);
      $name = str_replace("{", "&#123;", $name);
      $name = str_replace("}", "&#125;", $name);
      $name = str_replace(chr(0), "", $name);
      $name = str_replace(chr(92), "", $name);
      $name = str_replace('(', "", $name);
      $name = str_replace(')', "", $name);
      $name = str_ireplace("base64_decode", "base64_dec&#111;de", $name);
      
      fwrite($handler, "\t'{$name}' => '{$value}',\n");
    }
    fwrite($handler, ");\n\n?>");
    fclose($handler);
  }
}
?>
