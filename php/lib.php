<?php
class shiba{
  protected $db;
  function __construct(){

  }

  public function factorl($number,$count = 1){
    while($number > 0)  return factorl($number-1,$count*$number);
    return $count;
  }

  public function unqID(){
    return uniqid(md5(md5(time())));
  }

  public function getMonth($month){
    $month = intval($month);
    $monthes = [1 => 'January',2 => 'February',3 => 'March',
                4 => 'April',  5 => 'May',6 => 'June',7 => 'July',
                8 => 'August', 9 => 'September',10 => 'October',
                11 => 'November', 12 => 'December'];
    if(array_key_exists($month,$monthes)) return $monthes[$month];
    else return false;
  }

  public function zodiak($birthday){
    $birthday = explode('.',$birthday);
    preg_replace('/^0/',' ',$birthday[1],$birthday[1]);
    $zodiac = ['21.3|20.4' => 'Овен',    '21.4|20.5' => 'Телец',
               '21.5|21.6' => 'Близнецы','22.6|22.7' => 'Рак',
               '23.7|23.8' => 'Лев',     '24.8|23.9' => 'Дева',
               '24.9|23.10' => 'Весы',   '24.10|22.11' => 'Скорпион',
               '23.11|21.12' => 'Стрелец','22.12|20.1' => 'Козерог',
               '21.1|18.2' => 'Водолей',  '18.2|20.3' => 'Рыбы'];
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

  public function getBrowser($browser){
    if(strpos($browser, "Firefox") !== false)    $browser = "Mozilla Firefox";
    elseif(strpos($browser, "Opera") !== false)  $browser = "Opera";
    elseif(strpos($browser, "Chrome") !== false) $browser = "Google Chrome";
    elseif(strpos($browser, "MSIE") !== false)   $browser = "Internet Explorer";
    elseif(strpos($browser, "Safari") !== false) $browser = "Safari";
    else                                         $browser = "Неизвестный";
    return $browser;
  }

  public function dump($var){
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
  }

  public function findFile($file,$dir){
    $dir_hndl = opendir($dir);
    while(($name = readdir($dir_hndl)) !== false){
      if($name == '.' || $name == '..') continue;
      if(is_dir($dir.'/'.$name)) findFile($file,$dir.'/'.$name);
      else if(is_file($dir.'/'.$name) && $file === $name) echo $dir.'/'.$name.'<br>';
    }
    closedir($dir_hndl);
  }

  public function findDirectory($dirr,$dir){
  $dir_hndl = opendir($dir);
    while(($name = readdir($dir_hndl)) !== false){
      if($name == '.' || $name == '..') continue;
      if(is_dir($dir.'/'.$name)  && $dirr === $name) echo $dir.'/'.$name.'<br>';
      if(is_dir($dir.'/'.$name)) findDirectory($dirr,$dir.'/'.$name);
    }
    closedir($dir_hndl);
  }

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

  public function cleanAmp($sUrl){
    return str_replace('&', '&amp;', $sUrl);
  }

  public function getAge($day,$month,$year){
    if($month > date('m') || ($month == date('m') && $day > date('d'))) return (date('Y') - $year - 1);
    else return (date('Y') - $year);
  }

  public function getIp(){
    if      (filter_var($SERVER['REMOTE_ADDR'],FILTER_FLAG_IPV4,FILTER_FLAG_IPV4)) return filter_var($SERVER['REMOTE_ADDR'],FILTER_FLAG_IPV4,FILTER_FLAG_IPV4);
    else if (filter_var($SERVER['REMOTE_ADDR'],FILTER_FLAG_IPV4,FILTER_FLAG_IPV6)) return filter_var($SERVER['REMOTE_ADDR'],FILTER_FLAG_IPV4,FILTER_FLAG_IPV6);
    else return 'localhost';
  }

  public function getHost(){
    $Url = $_SERVER['HTTP_HOST'];
    $url = str_replace('https://','',$Url);
    $url = str_replace('http://','',$Url);
    $url = str_replace('www.','',$Url);
    $eX = explode('/',$url);
    return mb_strtolower($eX[0]);
  }

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
}
?>
