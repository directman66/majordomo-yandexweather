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
class yandexweather extends module {
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
  $date = date("Y-m-d");
  $date = strtotime($date);
  $out['D1']=date('d/m', strtotime("+1 day", $date));
  $out['D2']=date('d/m', strtotime("+2 day", $date));
  $out['D3']=date('d/m', strtotime("+3 day", $date));
  $out['D4']=date('d/m', strtotime("+4 day", $date));
  $out['D5']=date('d/m', strtotime("+5 day", $date));
  $out['D6']=date('d/m', strtotime("+6 day", $date));
  $out['D7']=date('d/m', strtotime("+7 day", $date));	
	
	
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
        if ((time() - gg('cycle_yandexweatherRun')) < 360*2 ) {
			$out['CYCLERUN'] = 1;
		} else {
			$out['CYCLERUN'] = 0;
		}

 $out['DUUID'] = $this->config['DUUID'];
 $out['DEVICEID']=$this->config['DEVICEID'];

	
 $out['EVERY']=$this->config['EVERY'];
 
 if (!$out['UUID']) {
	 $out['UUID'] = md5(microtime() . rand(0, 9999));
	 $this->config['UUID'] = $out['UUID'];
	 $this->saveConfig();
 }
 
 if ($this->view_mode=='update_settings') {
	global $duuid;
	$this->config['DUUID']=$duuid;	 

	global $deviceid;
	$this->config['DEVICEID']=$deviceid;	 

   
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
	
 if ($this->tab=='indataforecast') {
    $this->indata_search($out); 
 }
	

 if ($this->view_mode=='config_edit') {
   $this->config_edit($out, $this->id);
 }

 if ($this->view_mode=='config_check') {
echo "echeck";
   $this->config_check($this->id);
 }

 if ($this->view_mode=='config_uncheck') {
   $this->config_uncheck($this->id);
 }

if ($this->view_mode=='config_mycity') {
   $this->config_mycity($this->id);
 }
	



 if ($this->view_mode=='get') {
setGlobal('cycle_yandexweatherControl','start'); 
		$this->getdatefnc();
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
  require(DIR_MODULES.$this->name.'/cfgdata.inc.php');
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
/**
* InData edit/add
*
* @access public
*/
 
 function config_edit(&$out, $id) {	
  require(DIR_MODULES.$this->name.'/config_edit.inc.php');
 } 
/**
* InData delete record
*
* @access public
*/
 function config_del($id) {
  $rec=SQLSelectOne("SELECT * FROM yaweather_cities WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM yaweather_cities WHERE ID='".$rec['ID']."'");
 }
/**
* InData delete record
*
* @access public
*/
 function config_check($id) {
  $rec=SQLSelectOne("SELECT * FROM yaweather_cities WHERE ID=".$id);
//echo "<br>". implode( $id);
   $rec['check']=1;
SQLUpdate('yaweather_cities',$rec); 
} 


/**
* InData delete record
*
* @access public
*/
 
 function config_uncheck($id) {
  $rec=SQLSelectOne("SELECT * FROM yaweather_cities WHERE ID=".$id);
   $rec['check']=0;
SQLUpdate('yaweather_cities',$rec); 
} 
	
 function config_mycity($id) {
$rec=SQLSelectOne("update yaweather_cities set mycity=0");
SQLExec($rec);
	 
$rec=SQLSelectOne("update yaweather_cities set mycity=1 WHERE ID=".$id );
SQLExec($rec);

	 
} 	
	
 
 
///////////////////////////////////

function  getdatefnc(){
$this->getConfig();
$timestamp = time();
$token = md5('eternalsun'.$timestamp);
 
$uuid = "0b122ce93c77f68831839ca1d7cbf44a";
$deviceid = "3fb4aa04ac896f1b51dd48d643d9e76e";


$mycity=SQLSelect("SELECT ID FROM `yaweather_cities` where `mycity`=1 ")[ID];
	
	$properties=SQLSelect("SELECT * FROM `yaweather_cities` where `check`=1 ");

foreach ($properties as $did)
{
$cityid=$did[ID];
   

 
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
//$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid=54&lang=ru', false, $context);
//$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid=53&lang=ru', false, $context);
$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid='.$cityid.'&lang=ru', false, $context);
//$file = file_get_contents('https://api.weather.yandex.ru/v1/locations?lang=ru', false, $context);

 
header('Content-type: text/json');
//echo gzdecode($file);
$otvet=gzdecode($file);

$data=json_decode($otvet,true);
//$objn=$data[0]['id'];

$objn=$data[info][slug];
$src=$data[info];
//echo $objn;
addClassObject('YandexWeather',$objn);
sg( $objn.'.json',$otvet);
$src=$data[info];
sg( $objn.'.now',gg('sysdate').' '.gg('timenow')); 

foreach ($src as $key=> $value ) {   sg( $objn.'.'.$key,$value); }     
$src=$data[fact];
	foreach ($src as $key=> $value ) {   sg( $objn.'.'.$key,$value); }     
	$fobjn= $objn;
	$src=$data[forecasts][0][parts];
		foreach ($data[forecasts] as $day=> $value ) { 
			foreach ($data[forecasts][$day][parts] as $key=> $value ) {    
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_temp_avg',$data[forecasts][$day][parts][$key][temp_avg]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_speed',$data[forecasts][$day][parts][$key][wind_speed]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_gust',$data[forecasts][$day][parts][$key][wind_gust]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_dir',$data[forecasts][$day][parts][$key][wind_dir]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_mm',$data[forecasts][$day][parts][$key][pressure_mm]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_pa',$data[forecasts][$day][parts][$key][pressure_pa]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_humidity',$data[forecasts][$day][parts][$key][humidity]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'condition',$data[forecasts][$day][parts][$key][condition]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'daytime',$data[forecasts][$day][parts][$key][daytime]); 
 			}
		}
	}

	
//mycity	
//if ($mycity==$cityid){
$objmycity='yw_mycity';
addClassObject('YandexWeather',$objmycity);

//$objprops=get_props($fobjn);
//foreach ($objprops as $value)
//{ sg($objmycity.'.'.$value,gg($fobjn.".".$value);     } 

}
	
	
	
//////////	
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
  SQLExec('DROP TABLE IF EXISTS yaweather_cities');
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
setGlobal('cycle_yandexweatherAutoRestart','1');	 	 
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


  $data = <<<EOD
 yaweather_cities: country varchar(100) 
 yaweather_cities: cityname varchar(30) 
 yaweather_cities: part varchar(30) 
 yaweather_cities: ID int(30) unsigned NOT NULL 
 yaweather_cities: check int(30) 
 yaweather_cities: head int(30)
 yaweather_cities: type int(30) 
 yaweather_cities: region int(30) 
 yaweather_cities: mycity int(30) 

EOD;
  parent::dbInstall($data);

        $cmds = SQLSelectOne("SELECT * FROM yaweather_cities;");
        if(count($cmds) == 0) {
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Екатеринбург';
            $rec['part'] = 'Свердловская область';
            $rec['ID'] = 54;
            $rec['check'] = '1';
            $rec['head'] = '1';
            SQLInsert('yaweather_cities', $rec);
        $cmds = SQLSelectOne("SELECT * FROM yaweather_cities;"); 
     

            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Москва';
            $rec['part'] = 'Московская область';
            $rec['ID'] = 213;
            $rec['check'] = '1';
            $rec['head'] = 0;
            $rec['type'] = '1';

            SQLInsert('yaweather_cities', $rec);

            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Санкт-Петербург';
            $rec['part'] = 'Ленинградская область';
            $rec['ID'] = 2;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);
		
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Курган';
            $rec['part'] = 'Курганская область';
            $rec['ID'] = 53;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);
		
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Новосибирск';
            $rec['part'] = 'Новосибирская область';
            $rec['ID'] = 65;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);		

		   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Красноярск';
            $rec['part'] = 'Красноярский край';
            $rec['ID'] = 62;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
	   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Краснодарский край';
            $rec['part'] = 'Краснодар';
            $rec['ID'] = 35;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
$rec['country'] = 'Россия';
            $rec['cityname'] = 'Краснодарский край';
            $rec['part'] = 'Сочи';
            $rec['ID'] = 239;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);			
		
	   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Анапа';
            $rec['part'] = 'Краснодарский край';
            $rec['ID'] = 1107;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
		
			   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Челябинск';
            $rec['part'] = '';
            $rec['ID'] = 56;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Пермь';
            $rec['part'] = '';
            $rec['ID'] = 50;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	

	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Уфа';
            $rec['part'] = '';
            $rec['ID'] = 172;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	

		
	$rec['country'] = 'Россия';
            $rec['cityname'] = 'Казань';
            $rec['part'] = '';
            $rec['ID'] = 43;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	

	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Самара';
            $rec['part'] = '';
            $rec['ID'] = 51;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Волгоград';
            $rec['part'] = '';
            $rec['ID'] = 38;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Саратов';
            $rec['part'] = '';
            $rec['ID'] = 194;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Воронеж';
            $rec['part'] = '';
            $rec['ID'] = 193;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	

		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Тверь';
            $rec['part'] = '';
            $rec['ID'] = 14;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	

	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Вологда';
            $rec['part'] = '';
            $rec['ID'] = 21;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Ярославль';
            $rec['part'] = '';
            $rec['ID'] = 16;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Киров';
            $rec['part'] = '';
            $rec['ID'] = 46;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
	
	    $rec['country'] = 'Украина';
            $rec['cityname'] = 'Полтава';
            $rec['part'] = '';
            $rec['ID'] = 964;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);		

			    $rec['country'] = 'Белорусия';
            $rec['cityname'] = 'Минск';
            $rec['part'] = '';
            $rec['ID'] = 157;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);
		
    $rec['country'] = 'Украина';
            $rec['cityname'] = 'Запорожье';
            $rec['part'] = '';
            $rec['ID'] = 960;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);		
		
 }}
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


function get_props($obj)
{
$sql='SELECT substring(PROPERTY_NAME, POSITION("." in PROPERTY_NAME)+1) title FROM `pvalues` where PROPERTY_NAME like "'.$obj.'%"';
$rec = SQLSelect($sql); 
foreach ($rec as $prop)
{$ar2[] = $prop[title];}
return $ar2;
}
