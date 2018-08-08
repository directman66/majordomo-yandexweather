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
	
///	
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
  global $today;
  global $forecast;
  global $type;	
  global $skin;		
global $sayweather;		
global $sayforecast;		
global $alarmweather;		
global $alarmforecast;		
global $upd_PROPERTY_NAME;			
	
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
if (isset($today)) {
   $this->today=$today;
  }	
	
if (isset($type)) {
   $this->type=$type;
  }		
	
if (isset($skin)) {
   $this->skin=$skin;
  }		
	
	
if (isset($forecast)) {
   $this->forecast=$forecast;
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
// global $type;	
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
	
//$out['TODAY']=$this->today;	
//$out['FORECAST']=$this->forecast;		
$out['TYPE']=$this->type;			
//$out['ENABLE_EVENTS']=$this->enable_events;			
//$out['DUUID']=$this->duuid;				
//$out['DEVICEID']=$this->deviceid;					
	
//if (IsSet($this->skin)) {$out['SKIN']=$this->skin;}	
//else {$out['SKIN']=1;}
	
$out['SKIN']=1;
	
//$out['TYPE']=$type;				
//$out['TYPE']='FORECAST';
	
	
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
///	echo "admin";
//echo $this->view_mode;	
//('test.view_mode',$this->view_mode);	
 $this->getConfig();
        if ((time() - gg('cycle_yandexweatherRun')) < 360*2 ) {
			$out['CYCLERUN'] = 1;
		} else {
			$out['CYCLERUN'] = 0;
		}
 //$out['ENABLE_EVENTS'] = $this->config['ENABLE_EVENTS'];	
 //$out['DUUID']=$this->config['DUUID'];	
 //$out['DEVICEID']=$this->config['DEVICEID'];
 //$out['EVERY']=$this->config['EVERY'];
	
$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='EVERY'");
$out['EVERY']=$cmd_rec['VALUE'];

$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='DUUID'");
$out['DUUID']=$cmd_rec['VALUE'];


$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='ENABLE_EVENTS'");
$out['ENABLE_EVENTS']=$cmd_rec['VALUE'];
	
	
$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='MSG_LEVEL'");
$out['MSG_LEVEL']=$cmd_rec['VALUE'];
	
//$out['ENABLE_EVENTS']=1;


$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='DEVICEID'");
$out['DEVICEID']=$cmd_rec['VALUE'];

	
$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='FORECAST_DAY'");
$forecast_day=$cmd_rec['VALUE'];
$out['FORECAST_DAY']=$forecast_day;
	
	

for ($i = 1; $i <= $forecast_day; $i++) {
$fday='FORECAST_DAY'.$forecast_day;	
$out[$fday]="1";
}
	

//if (!$cmd_rec['EVERY']) $out['EVERY']=$cmd_rec['EVERY'];
//if (!$cmd_rec['ENABLE_EVENTS']) $out['ENABLE_EVENTS']=$cmd_rec['EVERY'];	
//if (!$cmd_rec['DUUID']) $out['DUUID']=$cmd_rec['DUUID'];
//if (!$cmd_rec['DEVICEID']) $out['DEVICEID']=$cmd_rec['DEVICEID'];

$cmd_rec = array();
	
	
	
if ($this->view_mode=='update_headsettings') 
	 
 {
global $duuid;
global $every;	
global $deviceid;	
global $forecast_day;	
	
	$this->config['DUUID']=$duuid;	 
	$this->config['DEVICEID']=$deviceid;	 
	$this->config['EVERY']=$every;	 
	$this->saveConfig();
//      $this->redirect("?");
	
//$rec=array();
//$rec['DUUID']=$duuid;	 	 
//$rec['DEVICEID']=$duuid;	 	 
//$rec['EVERY']=$duuid;	 	 	
//SQLUpdate('yaweather_config', $rec); // update	 	
SQLexec("update yaweather_config set value='$duuid' where parametr='DUUID'");
SQLexec("update yaweather_config set value='$every' where parametr='EVERY'");
SQLexec("update yaweather_config set value='$deviceid' where parametr='DEVICEID'");
SQLexec("update yaweather_config set value='$forecast_day' where parametr='FORECAST_DAY'");		   	   	   	

	

}
	
if ($this->view_mode=='add_city') 
	 
{
   global $ID;
   $rec['ID']=$ID;
	 
   global $country;
   $rec['country']=$country;
  
   global $cityname;
  $rec['cityname']=$cityname;

   global $part;
   $rec['part']=$part;
   global $check;
   $rec['check']=$check;

   global $latlon;
   $rec['latlon']=$latlon;

$table_name='yaweather_cities';
     SQLInsert($table_name, $rec); // adding new record


  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);



}


	
 if ($this->view_mode=='update_eventssettings') 
	 
 {
global $enable_events;
global $msg_level;	 

$this->config['ENABLE_EVENTS']=$enable_events;	 
   $this->saveConfig();
//   $this->redirect("?");

SQLexec("update yaweather_config set value='$enable_events' where parametr='ENABLE_EVENTS'");
SQLexec("update yaweather_config set value='$msg_level' where parametr='MSG_LEVEL'");	 


 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 
	
// if ($this->tab=='' || $this->tab=='outdata') {
//   $this->outdata_search($out);
// }  
 if ($this->tab=='' || $this->tab=='indata' || $this->tab=='widgets'|| $this->tab=='indataforecast') {

    $this->indata_search($out); 
 }
	
 if (($this->tab=='settings1')|| ($this->tab=='settings2')) {

    $this->settingstab($out); 
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
if ($this->view_mode=='indata_del') {
   $this->config_del($this->id);
 }
	
if ($this->view_mode=='titledel') {
   $this->title_del($this->id);
 }
	
	
	
	
 if ($this->view_mode=='get') {
setGlobal('cycle_yandexweatherControl','start'); 
		$this->getdatefnc();
//$t1 = new Thread('$this->upd_PROPERTY_NAME' );
//		$this->upd_PROPERTY_NAME_timer();	 
cm('yw_mycity.updatetitle');	 
    		$this->insertmain();
	 
	 
 }
        if ($this->view_mode=='sayweather')
        {
            $this->sayweather();
        }
        if ($this->view_mode=='sayforecast')
        {
            $this->sayforecast();
        }
	
if ($this->view_mode=='tlg_yandex')
        {
            $this->tlg_yandex();
        }	

	
        if ($this->view_mode=='alarmweather')
        {
            $this->alarmweather();
        }
	
        if ($this->view_mode=='alarmforecast')
        {
            $this->alarmforecast();
        }	
	
        if ($this->view_mode=='upd_PROPERTY_NAME')
        {
            $this->upd_PROPERTY_NAME();
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
	
	
 function settingstab(&$out) {	 
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
$this->insertmain();
		 
	$this->config['LATEST_UPDATE']=time();
	//$this->saveConfig();
SQLexec("update yaweather_config set value=UNIX_TIMESTAMP() where parametr='LASTCYCLE_TS'");		   
SQLexec("update yaweather_config set value=now() where parametr='LASTCYCLE_TXT'");		   	   

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
  // some action for related tables
  SQLExec("DELETE FROM yaweather_cities WHERE ID='".$id."'");
 }

function title_del($id) {
  // some action for related tables
  SQLExec("delete  from objects where class_id = (select id from classes where title = 'YandexWeather') and TITLE='".$id."'");
 }	
	
function get_props2($obj)
{
//$sql='SELECT title FROM `properties`  where object_id = (SELECT id FROM `objects`  where title="'.$obj.'")';
$sql='SELECT substring(PROPERTY_NAME, POSITION("." in PROPERTY_NAME)+1) title FROM `pvalues` where PROPERTY_NAME like "'.$obj.'%"';
$rec = SQLSelect($sql); 
foreach ($rec as $prop)
{
 //print_r($prop)[title];
$ar2[] = $prop['title'];
}
return $ar2;
}	
	
function insertmain() {
  // some action for related tables
  SQLExec("delete from yaweather_main");
  SQLExec("insert into yaweather_main (locality_name  , district_name  , TITLE   ,DESCRIPTION   ,temp   , conditionn   , wind_speed  ,  wind_dir   ,pressure_mm ,   humidity   , uv_index   , forecast_1_day_temp_avg   , forecast_2_day_temp_avg   , forecast_3_day_temp_avg   , forecast_4_day_temp_avg   , forecast_5_day_temp_avg   , forecast_6_day_temp_avg   , forecast_7_day_temp_avg   , forecast_1_daycondition   , forecast_2_daycondition   , forecast_3_daycondition   , forecast_4_daycondition   , forecast_5_daycondition   , forecast_6_daycondition   , forecast_7_daycondition   , forecast_0_morningcondition,    forecast_0_daycondition   , forecast_0_eveningcondition  ,  forecast_0_nightcondition   , forecast_0_morning_temp_avg,    forecast_0_day_temp_avg   , forecast_0_evening_temp_avg  ,  forecast_0_night_temp_avg   , forecast_0_morning_wind_speed,    forecast_0_day_wind_speed   , forecast_0_evening_wind_speed,    forecast_0_night_wind_speed ,   forecast_0_morning_wind_dir,    forecast_0_day_wind_dir   , forecast_0_evening_wind_dir  ,  forecast_0_night_wind_dir   , forecast_0_morning_pressure_mm,    forecast_0_day_pressure_mm   , forecast_0_evening_pressure_mm   , forecast_0_night_short_pressure_mm   ) select  max(locality_name) locality_name, max(district_name) district_name, titlename TITLE ,descr DESCRIPTION ,max(temp) temp ,  max(conditionn) conditionn, max(wind_speed) wind_speed, max(wind_dir) wind_dir, max(pressure_mm) pressure_mm, max(humidity) humidity,  max(uv_index) uv_index,max(forecast_1_day_temp_avg) forecast_1_day_temp_avg, max(forecast_2_day_temp_avg) forecast_2_day_temp_avg,max(forecast_3_day_temp_avg) forecast_3_day_temp_avg, max(forecast_4_day_temp_avg) forecast_4_day_temp_avg,max(forecast_5_day_temp_avg) forecast_5_day_temp_avg, max(forecast_6_day_temp_avg) forecast_6_day_temp_avg,max(forecast_7_day_temp_avg) forecast_7_day_temp_avg,max(forecast_1_daycondition)  forecast_1_daycondition ,  max(forecast_2_daycondition)  forecast_2_daycondition,  max(forecast_3_daycondition)  forecast_3_daycondition,  max(forecast_4_daycondition)  forecast_4_daycondition,  max(forecast_5_daycondition)  forecast_5_daycondition,  max(forecast_6_daycondition)  forecast_6_daycondition,max(forecast_7_daycondition)  forecast_7_daycondition,max(forecast_0_morningcondition)  forecast_0_morningcondition,max(forecast_0_daycondition)  forecast_0_daycondition  ,max(forecast_0_eveningcondition)  forecast_0_eveningcondition ,max(forecast_0_nightcondition)  forecast_0_nightcondition ,max(forecast_0_morning_temp_avg)  forecast_0_morning_temp_avg ,max(forecast_0_day_temp_avg)  forecast_0_day_temp_avg ,max(forecast_0_evening_temp_avg)  forecast_0_evening_temp_avg ,max(forecast_0_night_temp_avg)  forecast_0_night_temp_avg ,max(forecast_0_morning_wind_speed)  forecast_0_morning_wind_speed ,max(forecast_0_day_wind_speed)  forecast_0_day_wind_speed ,max(forecast_0_evening_wind_speed)  forecast_0_evening_wind_speed ,max(forecast_0_night_wind_speed)  forecast_0_night_wind_speed,max(forecast_0_morning_wind_dir)  forecast_0_morning_wind_dir ,max(forecast_0_day_wind_dir)  forecast_0_day_wind_dir ,max(forecast_0_evening_wind_dir)  forecast_0_evening_wind_dir ,max(forecast_0_night_wind_dir)  forecast_0_night_wind_dir ,max(forecast_0_morning_pressure_mm)  forecast_0_morning_pressure_mm ,max(forecast_0_day_pressure_mm)  forecast_0_day_pressure_mm ,max(forecast_0_evening_pressure_mm)  forecast_0_evening_pressure_mm ,max(forecast_0_night_short_pressure_mm)  forecast_0_night_short_pressure_mm from (select titlename,descr,if (tip='temp', VALUE,null) temp,if (tip='condition', VALUE,null) conditionn,if (tip='wind_speed', VALUE,null) wind_speed,if (tip='wind_dir', VALUE,null) wind_dir,if (tip='pressure_mm', VALUE,null) pressure_mm,if (tip='humidity', VALUE,null) humidity,  if (tip='uv_index', VALUE,null) uv_index,  if (tip='forecast_1_day_temp_avg', VALUE,null) forecast_1_day_temp_avg,  if (tip='forecast_2_day_temp_avg', VALUE,null) forecast_2_day_temp_avg,  if (tip='forecast_3_day_temp_avg', VALUE,null) forecast_3_day_temp_avg,   if (tip='forecast_4_day_temp_avg', VALUE,null) forecast_4_day_temp_avg,  if (tip='forecast_5_day_temp_avg', VALUE,null) forecast_5_day_temp_avg,  if (tip='forecast_6_day_temp_avg', VALUE,null) forecast_6_day_temp_avg,  if (tip='forecast_7_day_temp_avg', VALUE,null) forecast_7_day_temp_avg,if (tip='forecast_1_daycondition', VALUE,null) forecast_1_daycondition,if (tip='forecast_2_daycondition', VALUE,null) forecast_2_daycondition ,if (tip='forecast_3_daycondition', VALUE,null) forecast_3_daycondition,if (tip='forecast_4_daycondition', VALUE,null) forecast_4_daycondition,if (tip='forecast_5_daycondition', VALUE,null) forecast_5_daycondition ,if (tip='forecast_6_daycondition', VALUE,null) forecast_6_daycondition,if (tip='forecast_7_daycondition', VALUE,null) forecast_7_daycondition  ,if (tip='forecast_0_morningcondition', VALUE,null) forecast_0_morningcondition  ,if (tip='forecast_0_daycondition', VALUE,null) forecast_0_daycondition   ,if (tip='forecast_0_eveningcondition ', VALUE,null) forecast_0_eveningcondition    ,if (tip='forecast_0_nightcondition', VALUE,null) forecast_0_nightcondition ,if (tip='forecast_0_morning_temp_avg', VALUE,null) forecast_0_morning_temp_avg  ,if (tip='forecast_0_day_temp_avg', VALUE,null) forecast_0_day_temp_avg    ,if (tip='forecast_0_evening_temp_avg ', VALUE,null) forecast_0_evening_temp_avg    ,if (tip='forecast_0_night_temp_avg', VALUE,null) forecast_0_night_temp_avg  ,if (tip='forecast_0_morning_wind_speed', VALUE,null) forecast_0_morning_wind_speed   ,if (tip='forecast_0_day_wind_speed', VALUE,null) forecast_0_day_wind_speed    ,if (tip='forecast_0_evening_wind_speed ', VALUE,null) forecast_0_evening_wind_speed     ,if (tip='forecast_0_night_wind_speed', VALUE,null) forecast_0_night_wind_speed  ,if (tip='forecast_0_morning_wind_dir', VALUE,null) forecast_0_morning_wind_dir  ,if (tip='forecast_0_day_wind_dir', VALUE,null) forecast_0_day_wind_dir    ,if (tip='forecast_0_evening_wind_dir', VALUE,null) forecast_0_evening_wind_dir     ,if (tip='forecast_0_night_wind_dir', VALUE,null) forecast_0_night_wind_dir  ,if (tip='forecast_0_morning_pressure_mm', VALUE,null) forecast_0_morning_pressure_mm,if (tip='forecast_0_day_pressure_mm', VALUE,null) forecast_0_day_pressure_mm    ,if (tip='forecast_0_evening_pressure_mm', VALUE,null) forecast_0_evening_pressure_mm   ,if (tip='forecast_0_night_short_pressure_mm', VALUE,null) forecast_0_night_short_pressure_mm ,if (tip='district_name', VALUE,null) district_name,if (tip='locality_name', VALUE,null) locality_name     from   (SELECT objects.TITLE titlename , objects.DESCRIPTION descr, substring(pvalues.PROPERTY_NAME, position('.' in pvalues.PROPERTY_NAME)+1) tip, pvalues.VALUE fROM `objects`,  `pvalues`WHERE  objects.class_id = (SELECT ID FROM `classes` WHERE title='YandexWeather') and objects.ID=pvalues.OBJECT_ID and   objects.TITLE IS NOT NULL)a    )b    where titlename<>'' group by  titlename,descr");
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
	
 function alarmweather() {
$objn='AlarmClock'.AlarmIndex();	 
addClassObject('AlarmClock',$objn);	 
sg($objn.'.days','1111111');
sg($objn.'.once','0');	 
sg($objn.'.method','method');	 	 
sg($objn.'.AlarmTime','07:00');	 	 
sg($objn.'.AlarmOn','1');	 	 
sg($objn.'.code','yw_mycity.sayweather');	 	 	 
sg($objn.'.linked_method','sayweather');	 	 	 	 
SQLUpdate('objects', array("ID"=>get_id($objn), "DESCRIPTION"=>"sayweather"));   	 
	 
} 
 function alarmforecast() {
$objn='AlarmClock'.AlarmIndex();	 
addClassObject('AlarmClock',$objn);	 
sg($objn.'.days','1111111');
sg($objn.'.once','0');	 
sg($objn.'.method','method');	 	 
sg($objn.'.AlarmTime','07:15');	 	 
sg($objn.'.AlarmOn','1');	 	 
sg($objn.'.code','yw_mycity.sayforecast');	 	 	 
sg($objn.'.linked_method','sayforecast');	 	 	 	 
SQLUpdate('objects', array("ID"=>get_id($objn), "DESCRIPTION"=>"sayforecast"));   	 	 
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
	
 function tlg_yandex() {
	 
 
	 
$code='
$option = array( array(
$this->buildInlineKeyboardButton($text="Текущая","","Callback_yandexweather_saynow",""),
$this->buildInlineKeyboardButton($text="Прогноз","","Callback_yandexweather_sayforecast",""),                      
$this->buildInlineKeyboardButton($text="Виджет с сайта","","Callback_yandexweather_widget1","")
//$this->buildInlineKeyboardButton($text="Виджет локальный","","Callback_yandexweather_widget2",""),
//$this->buildInlineKeyboardButton($text="Виджет текстовый","","Callback_yandexweather_widget3","")
) );

$keyb = $this->buildInlineKeyBoard($option);
$content = array(\'chat_id\' => $chat_id, \'text\' => "Погода Яндекс:", \'reply_markup\' => $keyb);
$this->sendContent($content);

';
	 
$code1='
include_once(DIR_MODULES . "yandexweather/yandexweather.class.php");
 $yw = new yandexweather();
if ($callback == "Callback_yandexweather_saynow")
{ $yw->sayweather();  }
if ($callback == "Callback_yandexweather_sayforecast")
{ $yw->sayforecast();  }
if ($callback == "Callback_yandexweather_widget1")
{

$url=$params[\'url\'];
$w=$params[\'w\'];
$h=$params[\'h\'];

$mycity1=SQLSelectOne("SELECT ID FROM yaweather_cities where mycity=1 ");
$mycityid=$mycity1[\'ID\'];	


 $url="https://yandex.ru/pogoda/".$mycityid."/details?from=serp_title";
 

if ($w==""){$w=200;}
if ($h==""){$h=900;}

$save_to="./cms/cached/screen.png"; // куда сохранять
    unlink($save_to);
$cmd=\'xvfb-run -a -s "-screen 0 1024x768x24" wkhtmltoimage --crop-x 15 --crop-y 180 --crop-w 882 --crop-h 437 \'.$url.\' \'.$save_to;
// echo $cmd;
$output = shell_exec($cmd);
include_once(DIR_MODULES . \'telegram/telegram.class.php\');
$telegram_module = new telegram();

if (file_exists($save_to)) {
$telegram_module->sendImageToAll($save_to);
} else {
$text="У вас не установлен xvfb-run, для установки в системе Linux наберите. 
wget wget https://downloads.wkhtmltopdf.org/0.12/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz
tar -xvf wkhtmltox-0.12.4_linux-generic-amd64.tar.xz
cd wkhtmltox/bin/
sudo mv wkhtmltopdf  /usr/bin/wkhtmltopdf
sudo mv wkhtmltoimage  /usr/bin/wkhtmltoimage";
$telegram_module->sendMessageToAll($text);
}}
';	 
	 
	 
$rec=SQLSelect("SELECT *  FROM tlg_cmd  where TITLE='Погода Яндекс'" );
 
  if  (count($rec)==0 ){
	  

$rec=SQLSelectOne("SELECT max(ID)+1 id FROM tlg_cmd " );
  $cmdid= $rec['id'];
	  
$par=array();		 
$par['ID'] = $cmdid;
$par['TITLE'] = 'Погода Яндекс';		 		 
$par['DESCRIPTION'] = 'Ветка модуля Погоды Яндекс';		 		 	 
$par['ACCESS'] = 3;		 		 	 	 
$par['SHOW_MODE'] = 1;		 		 	 	 	 
$par['CONDITION'] = 1;		 		 	 	 	 
$par['PRIORITY'] = 0;		 		 	 	 	 
$par['CODE'] = $code;		 		 	 	 	 	 
SQLInsert('tlg_cmd', $par);						

	  
$rec=SQLSelectOne("SELECT max(ID)+1 id FROM tlg_event" );
  $eventid= $rec['id'];
	 
$par=array();		 
$par['ID'] = $eventid;
$par['TITLE'] = 'yw_callback';		 		 
$par['DESCRIPTION'] = 'Ветка модуля Погоды Яндекс';		 		 	 
$par['TYPE_EVENT'] = 9;		 		 	 	 
$par['ENABLE'] = 1;		 		 	 	 	 
$par['CODE'] = $code1;		 		 	 	 	 	 
SQLInsert('tlg_event', $par);						
	 
	 
}	}
	
////////////////////////////////////////
////////////////////////////////////////
////////////////////////////////////////	
function sayweather() {
require(DIR_MODULES.$this->name.'/sayforecastnow.php');	
}
///////////////////////////////////////////
///////////////////////////////////////////
///////////////////////////////////////////
///////////////////////////////////////////	
function sayforecast() {
  require(DIR_MODULES.$this->name.'/sayforecast.php');

}
	
	
 function config_mycity($id) {
SQLExec("update yaweather_cities set mycity=0");
//SQLExec($rec);
	 
SQLExec("update yaweather_cities set mycity=1 WHERE ID=".$id );
//SQLExec($rec);
	 
} 	
	
function upd_PROPERTY_NAME_timer() {	
SetTimeOut("upd_PROPERTY_NAME",'

$sqlQuery = "SELECT pvalues.*, objects.TITLE as OBJECT_TITLE, properties.TITLE as PROPERTY_TITLE FROM pvalues JOIN objects ON pvalues.OBJECT_ID = objects.id JOIN properties ON pvalues.PROPERTY_ID = properties.id WHERE pvalues.PROPERTY_NAME != CONCAT_WS('.', objects.TITLE, properties.TITLE)"; 

$data = SQLSelect($sqlQuery); 

$total = count($data); 

for ($i = 0; $i < $total; $i++)

{ $objectProperty = $data[$i]["OBJECT_TITLE"] . "." . $data[$i]["PROPERTY_TITLE"];

 $sqlQuery = "SELECT * FROM pvalues WHERE ID =".$data[$i]["ID"] ;
 
 $rec = SQLSelectOne($sqlQuery); $rec["PROPERTY_NAME"] = $data[$i]["OBJECT_TITLE"] . "." . $data[$i]["PROPERTY_TITLE"]; SQLUpdate("pvalues", $rec); } 
 
 ',30);	

}
	
function upd_PROPERTY_NAME() {	
	
$sqlQuery = "SELECT pvalues.*, objects.TITLE as OBJECT_TITLE, properties.TITLE as PROPERTY_TITLE
               FROM pvalues
               JOIN objects ON pvalues.OBJECT_ID = objects.id
               JOIN properties ON pvalues.PROPERTY_ID = properties.id
              WHERE pvalues.PROPERTY_NAME != CONCAT_WS('.', objects.TITLE, properties.TITLE)";
$data = SQLSelect($sqlQuery);
$total = count($data);
for ($i = 0; $i < $total; $i++)
{
   $objectProperty = $data[$i]['OBJECT_TITLE'] . "." . $data[$i]['PROPERTY_TITLE'];
   if ($data[$i]['PROPERTY_NAME'])
      echo "Incorrect: " . $data[$i]['PROPERTY_NAME'] . " should be $objectProperty" . PHP_EOL;
   else
      echo "Missing: " . $objectProperty . PHP_EOL;
   $sqlQuery = "SELECT *
                  FROM pvalues
                 WHERE ID = '" . $data[$i]['ID'] . "'";
   $rec = SQLSelectOne($sqlQuery);
   $rec['PROPERTY_NAME'] = $data[$i]['OBJECT_TITLE'] . "." . $data[$i]['PROPERTY_TITLE'];
   SQLUpdate('pvalues', $rec);
}
}
	
 
 
///////////////////////////////////
	///////////////////////////////////
	///////////////////////////////////
	///////////////////////////////////
	///////////////////////////////////
	///////////////////////////////////
	///////////////////////////////////
	///////////////////////////////////
	///////////////////////////////////
	///////////////////////////////////
///////////////////////////////////	
	
function  getdatefnc(){

  require(DIR_MODULES.$this->name.'/get.inc.php');

//if ($new==1) {upd_PROPERTY_NAME();}	
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
SQLExec('DROP TABLE IF EXISTS yaweather_main');
SQLExec('DROP TABLE IF EXISTS yaweather_config');	 
SQLExec("delete from pvalues where property_id in (select id FROM properties where object_id in (select id from objects where class_id = (select id from classes where title = 'YandexWeather')))");
SQLExec("delete from properties where object_id in (select id from objects where class_id = (select id from classes where title = 'YandexWeather'))");
SQLExec("delete from objects where class_id = (select id from classes where title = 'YandexWeather')");
SQLExec("delete from methods where class_id = (select id from classes where title = 'YandexWeather')");	 
SQLExec("delete from classes where title = 'YandexWeather'");	 


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
	 
$ChangeCondition='
if(($this->object_title=="yw_mycity") and ($this->getProperty("condition")<>""))
{
//require(DIR_MODULES."yandexweather/ywext.inc.php");
include_once(DIR_MODULES."yandexweather/ywext.inc.php");
 
$lastcondition=gg("yw_mycity.lastcondition");
$conditioneng=gg("yw_mycity.condition");
$condition1eng=gg("yw_mycity.forecast_0_daycondition");  
$condition2eng=gg("yw_mycity.forecast_1_daycondition");  
$condition3eng=gg("yw_mycity.forecast_2_daycondition");    

 $condition=getconditionrusincl($conditioneng);
$condition1=getconditionrusincl($condition1eng);    
$condition2=getconditionrusincl($condition2eng);        
$condition3=getconditionrusincl($condition3eng);        

sg("yw_mycity.conditionrus",$condition); 
    
sg("yw_mycity.condition1rus",$condition1) ;     
sg("yw_mycity.condition2rus",$condition2) ;     
sg("yw_mycity.condition3rus",$condition3) ;         

$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr=\'ENABLE_EVENTS\'");
$ee=$cmd_rec[\'VALUE\'];
 
$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr=\'MSG_LEVEL\'");
$msglevel=$cmd_rec[\'VALUE\'];
 
$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr=\'LASTCONDITION\'");
$lastcondition=$cmd_rec[\'VALUE\'];

 
//say($ee.":".$this->getProperty("condition").\':\'.$lastcondition,$msglevel); 
//say($ee.":".getObject(\'yw_mycity\')->getProperty(\'condition\').\':\'.$lastcondition,$msglevel);  
// say($ee.":".$params[\'NEW_VALUE\'].\':\'.$lastcondition,$msglevel);  
 //getObject(\'yw_mycity\')->getProperty(\'condition\');

 //if (($ee=="1") && ($this->getProperty("condition")!=$lastcondition)) {
if (($ee=="1") && ($params[\'NEW_VALUE\']!=$lastcondition)) { 
   
  say(" На улице ".$condition,$msglevel); 
}
}
sg("yw_mycity.lastcondition",$this->getProperty("condition"));
//$cmd_rec = SQLSelectOne("update yaweather_config set value=\'".$this->getProperty("condition")."\' where parametr=\'LASTCONDITION\'");		   	   	   	
SQLexec("update yaweather_config set value=\'".$params[\'NEW_VALUE\']."\' where parametr=\'LASTCONDITION\'");	

';
	 
$Changetemp='
require(DIR_MODULES."yandexweather/changetemp.php");';	 

$updatetitle='
$sqlQuery = "SELECT pvalues.*, objects.TITLE as OBJECT_TITLE, properties.TITLE as PROPERTY_TITLE
               FROM pvalues
               JOIN objects ON pvalues.OBJECT_ID = objects.id
               JOIN properties ON pvalues.PROPERTY_ID = properties.id
              WHERE pvalues.PROPERTY_NAME != CONCAT_WS(\'.\', objects.TITLE, properties.TITLE)";

$data = SQLSelect($sqlQuery); 
$total = count($data); 
for ($i = 0; $i < $total; $i++)
{ $objectProperty = $data[$i]["OBJECT_TITLE"] . "." . $data[$i]["PROPERTY_TITLE"];
 $sqlQuery = "SELECT * FROM pvalues WHERE ID =".$data[$i][\'ID\'] ;
 $rec = SQLSelectOne($sqlQuery); 
 $rec[\'PROPERTY_NAME\'] = $data[$i]["OBJECT_TITLE"] . "." . $data[$i]["PROPERTY_TITLE"]; SQLUpdate("pvalues", $rec); } 

$data = SQLSelect($sqlQuery); 
$total = count($data); 
for ($i = 0; $i < $total; $i++)
{ $objectProperty = $data[$i]["OBJECT_TITLE"] . "." . $data[$i]["PROPERTY_TITLE"];
 $sqlQuery = "SELECT * FROM pvalues WHERE ID =".$data[$i]["ID"] ;
 $rec = SQLSelectOne($sqlQuery); $rec["PROPERTY_NAME"] = $data[$i]["OBJECT_TITLE"] . "." . $data[$i]["PROPERTY_TITLE"]; SQLUpdate("pvalues", $rec); } 
';
 

	
	 
addClassMethod($classname,'OnChange','SQLUpdate("objects", array("ID"=>$this->id, "DESCRIPTION"=>gg("sysdate")." ".gg("timenow"))); ');
//addClassMethod($classname,'ChangeCondition',$ChangeCondition);
addClassMethod($classname,'ChangeCondition','///');
addClassMethod($classname,'Changetemp',$Changetemp);	 
addClassMethod($classname,'updatetitle',$updatetitle);	 
	 
addClassMethod($classname,'sayweather','include_once(DIR_MODULES . "yandexweather/yandexweather.class.php"); $yw = new yandexweather(); $yw->sayweather(); ');	 
addClassMethod($classname,'sayforecast','include_once(DIR_MODULES . "yandexweather/yandexweather.class.php"); $yw = new yandexweather(); $yw->sayforecast(); ');	 

	 $prop_id=addClassProperty($classname, 'temp', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Фактическая температура'; //   <-----------
$property['ONCHANGE']="Changetemp"; //	   	       
SQLUpdate('properties',$property); }

	 $prop_id=addClassProperty($classname, 'wind_speed', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Скорость ветра'; //   <-----------
SQLUpdate('properties',$property); } 
	
$prop_id=addClassProperty($classname, 'condition', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['ONCHANGE']="ChangeCondition"; // 	       <-----------	       
$property['DESCRIPTION']='Состояние погоды'; //   <-----------
SQLUpdate('properties',$property); } 
	 

	 
	 $prop_id=addClassProperty($classname, 'locality_name', 0);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Название населенного пункта'; //   <-----------
SQLUpdate('properties',$property); } 
	 
	 $prop_id=addClassProperty($classname, 'conditionrus', 0);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Состояние погоды сейчас по русски'; //   <-----------
SQLUpdate('properties',$property); } 	 
	 
$prop_id=addClassProperty($classname, 'condition1rus', 0);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Состояние погоды на завтра  по русски'; //   <-----------
SQLUpdate('properties',$property); } 	 	 
	 
$prop_id=addClassProperty($classname, 'condition2rus', 0);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Состояние погоды на после-завтра  по русски'; //   <-----------
SQLUpdate('properties',$property); } 	 	 	 
	 
$prop_id=addClassProperty($classname, 'condition3rus', 0);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Состояние погоды на после-после-завтра  по русски'; //   <-----------
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
 yaweather_cities: latlon varchar(50) 
 
EOD;
   parent::dbInstall($data);

	 
  $data = <<<EOD
 yaweather_config: parametr varchar(300)
 yaweather_config:  value varchar(100)  
EOD;
   parent::dbInstall($data);
	 
	 
$data = <<<EOD
yaweather_main: locality_name       varchar(100) 
yaweather_main: district_name       varchar(100) 
yaweather_main: TITLE               varchar(100) 
yaweather_main: DESCRIPTION         varchar(100) 
yaweather_main: temp                varchar(100) 
yaweather_main: conditionn          varchar(100) 
yaweather_main: wind_speed          varchar(100) 
yaweather_main: wind_dir            varchar(100) 
yaweather_main: pressure_mm         varchar(100) 
yaweather_main: humidity            varchar(100) 
yaweather_main: uv_index            varchar(100) 
yaweather_main: forecast_1_day_temp_avg  varchar(100) 
yaweather_main: forecast_2_day_temp_avg  varchar(100) 
yaweather_main: forecast_3_day_temp_avg  varchar(100) 
yaweather_main: forecast_4_day_temp_avg  varchar(100) 
yaweather_main: forecast_5_day_temp_avg  varchar(100) 
yaweather_main: forecast_6_day_temp_avg  varchar(100) 
yaweather_main: forecast_7_day_temp_avg  varchar(100) 
yaweather_main: forecast_1_daycondition  varchar(100) 
yaweather_main: forecast_2_daycondition  varchar(100) 
yaweather_main: forecast_3_daycondition  varchar(100) 
yaweather_main: forecast_4_daycondition  varchar(100) 
yaweather_main: forecast_5_daycondition  varchar(100) 
yaweather_main: forecast_6_daycondition      varchar(100) 
yaweather_main: forecast_7_daycondition      varchar(100) 
yaweather_main: forecast_0_morningcondition  varchar(100) 
yaweather_main: forecast_0_daycondition      varchar(100) 
yaweather_main: forecast_0_eveningcondition  varchar(100) 
yaweather_main: forecast_0_nightcondition    varchar(100) 
yaweather_main: forecast_0_morning_temp_avg  varchar(100) 
yaweather_main: forecast_0_day_temp_avg      varchar(100) 
yaweather_main: forecast_0_evening_temp_avg    varchar(100) 
yaweather_main: forecast_0_night_temp_avg      varchar(100) 
yaweather_main: forecast_0_morning_wind_speed  varchar(100) 
yaweather_main: forecast_0_day_wind_speed      varchar(100) 
yaweather_main: forecast_0_evening_wind_speed  varchar(100) 
yaweather_main: forecast_0_night_wind_speed    varchar(100) 
yaweather_main: forecast_0_morning_wind_dir    varchar(100) 
yaweather_main: forecast_0_day_wind_dir        varchar(100) 
yaweather_main: forecast_0_evening_wind_dir      varchar(100) 
yaweather_main: forecast_0_night_wind_dir        varchar(100) 
yaweather_main: forecast_0_morning_pressure_mm   varchar(100) 
yaweather_main: forecast_0_day_pressure_mm       varchar(100) 
yaweather_main: forecast_0_evening_pressure_mm  varchar(100) 
yaweather_main: forecast_0_night_short_pressure_mm  varchar(100) 
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
            $rec['mycity'] = '0';		
            SQLInsert('yaweather_cities', $rec);
        $cmds = SQLSelectOne("SELECT * FROM yaweather_cities;"); 
     
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Москва';
            $rec['part'] = 'Московская область';
            $rec['ID'] = 213;
            $rec['check'] = '1';
            $rec['head'] = 0;
            $rec['type'] = '1';
            $rec['mycity'] = '1';				
		
            SQLInsert('yaweather_cities', $rec);
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Санкт-Петербург';
            $rec['part'] = 'Ленинградская область';
            $rec['ID'] = 2;
            $rec['check'] = '0';
		$rec['mycity'] = '0';				
            
            SQLInsert('yaweather_cities', $rec);
		
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Курган';
            $rec['part'] = 'Курганская область';
            $rec['ID'] = 53;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);
		
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Новосибирск';
            $rec['part'] = 'Новосибирская область';
            $rec['ID'] = 65;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);		
		   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Красноярск';
            $rec['part'] = 'Красноярский край';
            $rec['ID'] = 62;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
		
	   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Краснодарский край';
            $rec['part'] = 'Краснодар';
            $rec['ID'] = 35;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
		
$rec['country'] = 'Россия';
            $rec['cityname'] = 'Краснодарский край';
            $rec['part'] = 'Сочи';
            $rec['ID'] = 239;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);			
		
	   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Анапа';
            $rec['part'] = 'Краснодарский край';
            $rec['ID'] = 1107;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
		
		
			   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Челябинск';
            $rec['part'] = '';
            $rec['ID'] = 56;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Пермь';
            $rec['part'] = '';
            $rec['ID'] = 50;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Уфа';
            $rec['part'] = '';
            $rec['ID'] = 172;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
		
	$rec['country'] = 'Россия';
            $rec['cityname'] = 'Казань';
            $rec['part'] = '';
            $rec['ID'] = 43;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Самара';
            $rec['part'] = '';
            $rec['ID'] = 51;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Волгоград';
            $rec['part'] = '';
            $rec['ID'] = 38;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Саратов';
            $rec['part'] = '';
            $rec['ID'] = 194;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Воронеж';
            $rec['part'] = '';
            $rec['ID'] = 193;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Тверь';
            $rec['part'] = '';
            $rec['ID'] = 14;
            $rec['check'] = '0';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Вологда';
            $rec['part'] = '';
            $rec['ID'] = 21;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Ярославль';
            $rec['part'] = '';
            $rec['ID'] = 16;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Киров';
            $rec['part'] = '';
            $rec['ID'] = 46;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);	
	
	    $rec['country'] = 'Украина';
            $rec['cityname'] = 'Полтава';
            $rec['part'] = '';
            $rec['ID'] = 964;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);		
			    $rec['country'] = 'Белорусия';
            $rec['cityname'] = 'Минск';
            $rec['part'] = '';
            $rec['ID'] = 157;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);
		
    $rec['country'] = 'Украина';
            $rec['cityname'] = 'Запорожье';
            $rec['part'] = '';
            $rec['ID'] = 960;
            $rec['check'] = '0';
            $rec['latlon'] = '';
$rec['mycity'] = '0';				
            SQLInsert('yaweather_cities', $rec);		
 $rec['country'] = 'Россия';
            $rec['cityname'] = 'Спутник';
            $rec['part'] = 'Ростовская область';
            $rec['ID'] = 0;
            $rec['check'] = '0';
            $rec['latlon'] = 'lat=47.240585&lon=38.870989';		
$rec['mycity'] = '0';				
		
            SQLInsert('yaweather_cities', $rec);		
		
        $rec['country'] = 'Россия';
            $rec['cityname'] = 'Ростов-на-Дону';
            $rec['part'] = 'Ростовская область';
            $rec['ID'] = 39;
            $rec['check'] = '0';
            $rec['latlon'] = '';				
$rec['mycity'] = '0';				
            
            SQLInsert('yaweather_cities', $rec);		
$rec['country'] = 'Россия';
            $rec['cityname'] = 'Таганрог';
            $rec['part'] = 'Ростовская область';
            $rec['ID'] = 971;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
$rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);				
////////////////
$rec['country'] = 'Россия';
            $rec['cityname'] = 'Керчь';
            $rec['part'] = 'Республика Крым';
            $rec['ID'] = 11464;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
$rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);				

		 
$rec['country'] = 'Россия';
            $rec['cityname'] = 'Моздок';
            $rec['part'] = 'Северная Осетия — Алания';
            $rec['ID'] = 11022;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
$rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);				

		 
		 
		 $rec['country'] = 'Россия';
            $rec['cityname'] = 'Тольяти';
            $rec['part'] = 'Самарская область';
            $rec['ID'] = 240;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
$rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);				

	
       	 $rec['country'] = 'Россия';
            $rec['cityname'] = 'Домодедово';
            $rec['part'] = 'Московская область';
            $rec['ID'] = 10725;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
$rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);	
		 
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Оха';
            $rec['part'] = 'Сахалинская область';
            $rec['ID'] = 11447;
            $rec['check'] = '0';
            $rec['latlon'] = 'lat=53.58991&lon=142.95313';						
            $rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);	
		 
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Балашиха';
            $rec['part'] = 'Московская область';
            $rec['ID'] = 10716;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
            $rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);
		 
		 
		     $rec['country'] = 'Россия';
            $rec['cityname'] = 'Балашиха';
            $rec['part'] = 'Московская область';
            $rec['ID'] = 10716;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
            $rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);
		 
		 
		     $rec['country'] = 'Россия';
            $rec['cityname'] = 'Гатчина';
            $rec['part'] = 'Ленинградская область';
            $rec['ID'] = 10867;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
            $rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);
		 
		     $rec['country'] = 'Россия';
            $rec['cityname'] = 'Архангельск';
            $rec['part'] = 'Архангельская область';
            $rec['ID'] = 20;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
            $rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);
		 
		     $rec['country'] = 'Россия';
            $rec['cityname'] = 'Норильск';
            $rec['part'] = 'Красноярский край';
            $rec['ID'] = 11311;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
            $rec['mycity'] = '0';		            
            SQLInsert('yaweather_cities', $rec);
 
				
////////////////////////////////////////////////////////////////
		 ////////////////////////////////////////////////////////////////
		 ////////////////////////////////////////////////////////////////
		 ////////////////////////////////////////////////////////////////
		 ////////////////////////////////////////////////////////////////

		 
$par['parametr'] = 'EVERY';
$par['value'] = 30;		 
SQLInsert('yaweather_config', $par);				
		 
$par['parametr'] = 'ENABLE_EVENTS';
$par['value'] = 0;		 
SQLInsert('yaweather_config', $par);				
		 
$par['parametr'] = 'DUUID';
$par['value'] = "";		 
SQLInsert('yaweather_config', $par);						
		
$par['parametr'] = 'DEVICEID';
$par['value'] = "";		 
SQLInsert('yaweather_config', $par);						
		
$par['parametr'] = 'LASTCYCLE_TS';
$par['value'] = "0";		 
SQLInsert('yaweather_config', $par);						
		
$par['parametr'] = 'LASTCYCLE_TXT';
$par['value'] = "0";		 
SQLInsert('yaweather_config', $par);						
		
$par['parametr'] = 'FORECAST_DAY';
$par['value'] = "3";		 
SQLInsert('yaweather_config', $par);						

$par['parametr'] = 'MSG_LEVEL';
$par['value'] = "2";		 
SQLInsert('yaweather_config', $par);		 
		 
$par['parametr'] = 'LASTCONDITION';
$par['value'] = "0";		 
SQLInsert('yaweather_config', $par);		 

		 
$objmycity='yw_mycity';
addClassObject('YandexWeather',$objmycity);	 	 
		 
//$sql='SELECT * FROM methods where OBJECT_ID=(SELECT id   FROM objects  WHERE TITLE ="yw_mycity")';
$sql='SELECT id   FROM objects  WHERE TITLE ="yw_mycity"';		 
$cmd_rec=SQLSelectOne($sql);

		 $id=$cmd_rec['id'];
		 
$par=array();		 
$par['OBJECT_ID'] = $id;
$par['CODE'] = $ChangeCondition;		 
$par['TITLE'] = 'ChangeCondition';		 		 
SQLInsert('methods', $par);						
		 
//		 $property['CODE']=$Changetemp; //   
//SQLUpdate('methods',$property); 
	
		
		
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

function getconditionrus($conditioneng){
  require(DIR_MODULES.$this->name.'/ywext.inc.php');
//require(DIR_MODULES.$this->name.'/ywext.php');

$condition=getconditionrusincl($conditioneng);
return $condition;
//}


}}
/*
*
* TW9kdWxlIGNyZWF0ZWQgQXByIDA0LCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
function get_props($obj)
{
//$sql='SELECT title FROM `properties`  where object_id = (SELECT id FROM `objects`  where title="'.$obj.'")';
$sql='SELECT distinct substring(PROPERTY_NAME, POSITION("." in PROPERTY_NAME)+1) title FROM `pvalues` where PROPERTY_NAME like "'.$obj.'%"';
$rec = SQLSelect($sql); 
foreach ($rec as $prop)
{
 //print_r($prop)[title];
$ar2[] = $prop['title'];
}
return $ar2;
}


 function AlarmIndex() {
    $objects=getObjectsByClass('AlarmClock');
    $index=0;
    $total = count($objects);
    for ($i = 0; $i < $total; $i++) {
        if (preg_match('/(\d+)/',$objects[$i]['TITLE'],$m)) {
            $current_index=(int)$m[1];
            if ($current_index>$index) {
                $index=$current_index;
            }
        }
    }
    $index++;
    if ($index<10) {
        $index='0'.$index;
    }
    return $index;
}

function get_id($prop)
{
$sql='SELECT distinct id idFROM `objects`  WHERE TITLE ="'.$prop.'"';
$rec = SQLSelect($sql); 
return $rec[0][id];
}


	
	
