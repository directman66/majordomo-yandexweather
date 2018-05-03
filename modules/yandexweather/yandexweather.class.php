<?php
/**
* https://yandex.ru/pogoda/
* author Sannikov Dmitriy sannikovdi@yandex.ru
* support page 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 09:04:00 [Apr 04, 2016])
*/
//
//
class starline extends module {
/**
*
* Module class constructor
*
* @access private
*/
function yandexweather() {
  $this->name="yandexweather";
  $this->title="Погода Яндекс";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
 function edit_classes(&$out, $id) {
  require(DIR_MODULES.$this->name.'/classes_edit.inc.php');
 }

function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}



/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();

//        if ((time() - gg('cycle_livegpstracksRun')) < $this->config['TLG_TIMEOUT']*2 ) {
        if ((time() - gg('cycle_starlineRun')) < 360*2 ) {
			$out['CYCLERUN'] = 1;
		} else {
			$out['CYCLERUN'] = 0;
		}

 
 $out['STARLINELOGIN'] = $this->config['STARLINELOGIN'];
 $out['STARLINEPWD']=$this->config['STARLINEPWD'];
 $out['STARLINETOKEN']=$this->config['STARLINETOKEN'];
 $out['STARLINESESID']=$this->config['STARLINESESID'];

 $out['STARLINECOOKIES']=$this->config['STARLINECOOKIES'];

$out['STARLINEDEBUG']=$this->config['STARLINEDEBUG'];
	
 $out['EVERY']=$this->config['EVERY'];
 
 if (!$out['UUID']) {
	 $out['UUID'] = md5(microtime() . rand(0, 9999));
	 $this->config['UUID'] = $out['UUID'];
	 $this->saveConfig();
 }
 
 if ($this->view_mode=='update_settings') {
	global $starlinelogin;
	$this->config['STARLINELOGIN']=$starlinelogin;	 

	global $starlinepwd;
	$this->config['STARLINEPWD']=$starlinepwd;	 

	global $starlinetoken;
	$this->config['STARLINETOKEN']=$starlinetoken;	 

	global $starlinesesid;
	$this->config['STARLINESESID']=$starlinesesid;	 


	global $starlinecookies;
	$this->config['STARLINECOOKIES']=$starlinecookies;	 

	global $starlinedebug;
	$this->config['STARLINEDEBUG']=$starlinedebug;	 

   
   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 
// if ($this->tab=='' || $this->tab=='outdata') {
//   $this->outdata_search($out);
// }  

 if ($this->tab=='' || $this->tab=='indata') {
    $this->indata_search($out); 
 }
 if ($this->view_mode=='login') {
		$this->login();
 }

 if ($this->view_mode=='get') {
		$this->getdatefnc();
 }

 if ($this->view_mode=='startign') {
		$this->startign2();

 }

}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
 
 function indata_search(&$out) {	 
  require(DIR_MODULES.$this->name.'/indata.inc.php');
 }

 function processCycle() {
   $this->getConfig();

   $every=$this->config['EVERY'];
   $tdev = time()-$this->config['LATEST_UPDATE'];
   $has = $tdev>$every*60;
   if ($tdev < 0) {
		$has = true;
   }
   
   if ($has) {  
$this->getdatefnc();   
		 
	$this->config['LATEST_UPDATE']=time();
	$this->saveConfig();
   } 
  }

 function sendData() {

 }
 
 

	
	
function login() {
$cookie_file = ROOT . 'cached/starline_cookie.txt'; 
$this->getConfig();
//sg('test.starline','login:'.$this->config['STARLINELOGIN']);
//sg('test.starline','login:'.$this->config['STARLINEPWD']);

$url = 'https://starline-online.ru/user/login';
$fields = array(
'LoginForm[login]' =>$this->config['STARLINELOGIN'], 
'LoginForm[rememberMe]' => 'on', 
'LoginForm[pass]' => $this->config['STARLINEPWD'],
'captcha[code]'=>'',
'captcha[sid]'=>''
);



$fields_string = '';
foreach ($fields as $key => $value) {    $fields_string .= urlencode($key) . '=' . urlencode($value) . '&';}
rtrim($fields_string, '&');
$this->config['STARLINEDEBUG']=$fields_string;
//sg('test.starline','login:'.$fields_string);

//sg('test.starline',$this->config['COOKIES']);
$cdata=$this->config['STARLINECOOKIES'];
	 
$ch = curl_init();
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
//curl_setopt($ch, CURLOPT_POST, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0',
'Accept: application/json, text/javascript, */*; q=0.01',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest'

//'User-Agent\': \'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0',
//'Accept\': \'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
//'Accept-Language\': \'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
//'Connection\': \'keep-alive'


//'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0',
//'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
//'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
//'Connection: keep-alive'

));


$result = curl_exec($ch);
$info = curl_getinfo($ch);
$this->config['STARLINEDEBUG']=$result;

//sg('test.starline','ch:'.$ch);
sg('test.starline','result:'.$result);


//sg('test.starline','reqestheader:'.json_encode($info));
//sg('test.starline','reqestheade_ifo:'.$info['request_header']);

//$headers=array();
$data=explode("\n",$result);
//$headers['status']=$data[0];

//array_shift($data);

foreach($data as $part){
$par=substr ($part,0,10);

//sg('test.starline','part:'.$part);
if (strpos($part,'PHPSESSID')>0) {
$sesid=explode('=',  $part);
$sesid2=explode(';',  $sesid[1]);
sg('test.starline_PHPSESSID',$sesid2[0]);
$this->config['STARLINESESID']=$sesid2[0];
}

if (strpos($part,': t=')>0) {
$token=explode('=',  $part);
$token2=explode(';',  $token[1]);

	
 addClassObject('starline-online','starlinecfg');	
sg('starlinecfg.token',$token2[0]);	
$this->config['STARLINETOKEN']=$token2[0];
}

if (strpos($part,'starline.ru')>0) {
//$token=explode('=',  $part);
//$token2=explode(';',  $token[1]);
sg('test.starline_cookies',$part);
$this->config['STARLINECOOKIES']=$part;
}


//sg('test.starline','part:'.$part);
if (strpos($part,'Captcha')>0) {
//sg('test.starline_Captcha',$part);
}else 
{
//sg('test.starline_Captcha','no need');
}

if (strpos($part,'Cookies')>0) {
//sg('test.starline_Cookies',$part);
}
}


//sg('test.starline',$matches);
//sg('test.starline',$cookies);
curl_close($ch);
$this->saveConfig();
 }








///////////////////////////////////

function  getdatefnc(){
$this->getConfig();
$timestamp = time();
$token = md5('eternalsun'.$timestamp);
 
$uuid = "8211637137c4408898aceb1097921872";
$deviceid = "315f0e802b0b49eb8404ea8056abeaaf";
 
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: yandex-weather-android/4.2.1\n" .
               "X-Yandex-Weather-Client: YandexWeatherAndroid/4.2.1\n" .
               "X-Yandex-Weather-Device: os=null;os_version=21;manufacturer=chromium;model=App Runtime for Chrome Dev;device_id=$deviceid;uuid=$uuid;\n" .
               "X-Yandex-Weather-Token: $token\n" .
               "X-Yandex-Weather-Timestamp: $timestamp\n" .
               "X-Yandex-Weather-UUID: $uuid\n" .
               "X-Yandex-Weather-Device-ID: $deviceid\n" .
               "Accept-Encoding: gzip, deflate\n" .
               "Host: api.weather.yandex.ru\n" .
               "Connection: Keep-Alive"
  )
);
 
$context = stream_context_create($opts);
 //ID города узнаем тут: https://pogoda.yandex.ru/static/cities.xml
//region="11162" id="28440
$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid=54&lang=ru', false, $context);
//$file = file_get_contents('https://api.weather.yandex.ru/v1/locations?lang=ru', false, $context);

 
header('Content-type: text/json');
//echo gzdecode($file);
$otvet=gzdecode($file);

$data=json_decode($otvet,true);
//$objn=$data[0]['id'];

 $objn=$data[info][slug]."_fact";
$src=$data[info];
echo $objn;
addClassObject('YandexWeather',$objn);

$src=$data[info];
sg( $objn.'.now',gg('sysdate').' '.gg('timenow')); 

foreach ($src as $key=> $value ) {   sg( $objn.'.'.$key,$value); }     

$src=$data[fact];
foreach ($src as $key=> $value ) {   sg( $objn.'.'.$key,$value); }     

$fobjn=$objn."_forecast0";
//addClassObject('YandexWeather',$fobjn);
$src=$data[forecasts][0][parts];
foreach ($data[forecasts] as $day=> $value ) { 
 $fobjn=$objn."_forecast".$day;
addClassObject('YandexWeather',$fobjn); 
foreach ($data[forecasts][$day][parts] as $key=> $value ) {   
sg( $fobjn.'.now',gg('sysdate').' '.gg('timenow'));  
sg( $fobjn.'.'.$key.'_temp_avg',$data[forecasts][$day][parts][$key][temp_avg]);
sg( $fobjn.'.'.$key.'_wind_speed',$data[forecasts][$day][parts][$key][wind_speed]);
sg( $fobjn.'.'.$key.'_wind_gust',$data[forecasts][$day][parts][$key][wind_gust]);
sg( $fobjn.'.'.$key.'_wind_dir',$data[forecasts][$day][parts][$key][wind_dir]);
sg( $fobjn.'.'.$key.'_pressure_mm',$data[forecasts][$day][parts][$key][pressure_mm]);
sg( $fobjn.'.'.$key.'_pressure_pa',$data[forecasts][$day][parts][$key][pressure_pa]);
sg( $fobjn.'.'.$key.'_humidity',$data[forecasts][$day][parts][$key][humidity]);
sg( $fobjn.'.'.$key.'condition',$data[forecasts][$day][parts][$key][condition]);
sg( $fobjn.'.'.$key.'daytime',$data[forecasts][$day][parts][$key][daytime]); 
 
 
 
}
}




}
  
  
  
 
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS yandexweather');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
$classname='YandexWeather';
addClass($classname); 
addClassMethod($classname,'OnChange','SQLUpdate("objects", array("ID"=>$this->id, "DESCRIPTION"=>gg("sysdate")." ".gg("timenow"))); ');

$prop_id=addClassProperty($classname, 'temp', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Фактическая температура'; //   <-----------
SQLUpdate('properties',$property); }


$prop_id=addClassProperty($classname, 'wind_speed', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Скорость ветра'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'pressure_pa', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']=' Нормальное давление для заданных координат, кПА'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'pressure_mm', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Атмосферное давление, ммртст.'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'now', 2);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['ONCHANGE']='OnChange'; //   <-----------
$property['DESCRIPTION']='Когда обновлена инфомация'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'humidity', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Текущая влажность'; //   <-----------
SQLUpdate('properties',$property); } 


$prop_id=addClassProperty($classname, 'wind_dir', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Направление ветра'; //   <-----------
SQLUpdate('properties',$property); } 


$prop_id=addClassProperty($classname, 'phenom', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']=''; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'soil_temp', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Ощущение погоды'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty($classname, 'uv_index', 10);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='УФ-индекс'; //   <-----------
SQLUpdate('properties',$property);} 

 }
// --------------------------------------------------------------------

//////
function getaddrfromcoord($x,$y)
{
$url='http://maps.googleapis.com/maps/api/geocode/xml?latlng='.$x.',' .$y.'&sensor=false&language=ru'; 
  $fields = array(
   	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
	'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.3',
	'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',	'Connection: keep-alive',	'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.76 Safari/537.36'     );
foreach($fields as $key=>$value)
{ $fields_string .= $key.'='.urlencode($value).'&'; }
rtrim($fields_string, '&');
   $ch = curl_init();   
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POST, count($fields));   
   curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
   $result = curl_exec($ch);
 curl_close($ch);
$xml = simplexml_load_string($result);
$otvet=$xml->result->formatted_address; 
$spl=explode(',',$otvet) ;
return $spl[0] ;
//return $url;
} 
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgQXByIDA0LCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
