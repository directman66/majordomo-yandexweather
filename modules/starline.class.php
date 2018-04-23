<?php
/**
* https://starline-online.ru/
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 09:04:00 [Apr 04, 2016])
*/
//
//
class livegpstracks extends module {
/**
*
* Module class constructor
*
* @access private
*/
function starline() {
  $this->name="starline";
  $this->title="https://starline-online.ru/";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
  $this->API_KEY = "35uRe2lIkUUPY"; // Module Key
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
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

 
 $out['UUID'] = $this->config['UUID'];
 $out['SRV_NAME']=$this->config['SRV_NAME'];
 $out['API_MAC']=$this->config['API_MAC'];
 $out['API_SERVER']=$this->config['API_SERVER'];
 $out['API_PORT']=$this->config['API_PORT'];
 $out['EVERY']=$this->config['EVERY'];
 
 if (!$out['UUID']) {
	 $out['UUID'] = md5(microtime() . rand(0, 9999));
	 $this->config['UUID'] = $out['UUID'];
	 $this->saveConfig();
 }
 
 if ($this->view_mode=='update_settings') {
	global $srv_name;
	$this->config['SRV_NAME']=$srv_name;	 

	global $api_server;
	$this->config['API_SERVER']=$api_server;	 

	global $api_port;
	$this->config['API_PORT']=$api_port;	 

	global $api_mac;
	$this->config['API_MAC']=$api_mac;

	global $every;
	$this->config['EVERY']=$every;
   
   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 
 if ($this->tab=='' || $this->tab=='outdata') {
   $this->outdata_search($out);
 }  
 if ($this->tab=='indata') {
   $this->indata_search($out); 
 }
 if ($this->view_mode=='test') {
		$this->sendData();
		$this->readData();
		$this->redirect("?");
 }
 if ($this->view_mode=='outdata_edit') {
   $this->outdata_edit($out, $this->id);
 }
 if ($this->view_mode=='outdata_del') {
   $this->outdata_del($this->id);
   $this->redirect("?data_source=$this->data_source&view_mode=node_edit&id=$pid&tab=outdata");
 }	
 if ($this->view_mode=='indata_edit') {
   $this->indata_edit($out, $this->id);
 }
 if ($this->view_mode=='indata_del') {
   $this->indata_del($this->id);
   $this->redirect("?data_source=$this->data_source&view_mode=node_edit&id=$pid&tab=indata");
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
/**
* OutData search
*
* @access public
*/
 function outdata_search(&$out) {	 
  require(DIR_MODULES.$this->name.'/outdata.inc.php');
 }
/**
* InData search
*
* @access public
*/ 
 function indata_search(&$out) {	 
  require(DIR_MODULES.$this->name.'/indata.inc.php');
 }
/**
* OutData edit/add
*
* @access public
*/
 function outdata_edit(&$out, $id) {	
  require(DIR_MODULES.$this->name.'/outdata_edit.inc.php');
 } 
/**
* OutData delete record
*
* @access public
*/
 function outdata_del($id) {
  $rec=SQLSelectOne("SELECT * FROM lgps_out WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM lgps_out WHERE ID='".$rec['ID']."'");
 }
/**
* InData edit/add
*
* @access public
*/
 function indata_edit(&$out, $id) {	
  require(DIR_MODULES.$this->name.'/indata_edit.inc.php');
 } 
/**
* InData delete record
*
* @access public
*/
 function indata_del($id) {
  $rec=SQLSelectOne("SELECT * FROM lgps_in WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM lgps_in WHERE ID='".$rec['ID']."'");
 }
 
 function propertySetHandle($object, $property, $value) {
   $this->getConfig();
   $table='lgps_out';
   $properties=SQLSelect("SELECT ID FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     //to-do
    }
   }
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
	$this->sendData();
	$this->readData();
		 
	$this->config['LATEST_UPDATE']=time();
	$this->saveConfig();
   } 
 }

 function sendData() {
	$this->getConfig();

	$table='lgps_out';
	$properties=SQLSelect("SELECT * FROM $table WHERE active=1;");
	$total=count($properties);
	if ($total) {
		$send="#".$this->config['API_MAC'];
		if ($this->config['SRV_NAME'])
			$send.="#".$this->config['SRV_NAME'];
			$send.="\n";
			for($i=0;$i<$total;$i++){
				$val = round( getGlobal($properties[$i]['LINKED_OBJECT'].'.'.$properties[$i]['LINKED_PROPERTY']), 2);
				
				$send.="#".$properties[$i]['MAC']."#".$val."#".$properties[$i]['TITLE']."\n";
				
				$properties[$i]['UPDATED'] = date('Y-m-d H:i:s');
				SQLUpdate($table, $properties[$i]);
			}
		$send.="##";

		$fp = @fsockopen("tcp://narodmon.ru", 8283, $errno, $errstr);
		if($fp) {
			fwrite($fp, $send);

			$result='';
			while (!feof($fp)) {
				$result.=fread($fp, 128);
			}
		}
		@fclose($fp);		
		
		echo date("Y-m-d H:i:s")." Send ok\n";		
	}
 }
 
 function sendVals($vals){ 
		$this->getConfig();
 
	  $total=count($vals);
		if ($total) {		
			$send="#".$this->config['API_MAC'];
			if ($this->config['SRV_NAME'])
				$send.="#".$this->config['SRV_NAME'];
			$send.="\n";
			for($i=0;$i<$total;$i++)
				$send.="#".$vals[$i]['MAC']."#".$vals[$i]['VALUE']."#".$vals[$i]['TITLE']."\n";
			$send.="##";		
		 
			$fp = @fsockopen("tcp://narodmon.ru", 8283, $errno, $errstr);
			if($fp) {
			 fwrite($fp, $send);

			 $result='';
			 while (!feof($fp)) {
				 $result.=fread($fp, 128);
			 }
			}
			@fclose($fp);		

			echo date("Y-m-d H:i:s")." Send vals ok\n";		
		}	
 }
 
 function readData() {
	$this->getConfig(); 

	$table='lgps_in';	
	$properties=SQLSelect("SELECT * FROM $table;");

foreach ($properties as $did)
{
$num=$did[DID];
$title=$did[TITLE];
//$urls[] = ['url' => 'http://livegpstracks.com/viewer_coos_s.php?code='.$num];
$urls[] = ['url' => 'http://livegpstracks.com/viewer_coos_s.php?code='.$num,'name'=>$title,'numer'=>$num];
}	



		
	 foreach ($urls as $url1) {
     
echo $url1['url'];
$title=$url1['name'];
$numer=$url1['numer'];
$content=getURL($url1['url'], 0);  
$data=json_decode($content,true);
//$objn=$data[0]['id'];
$objn=$data[0]['code'];
     
echo $objn.'----------------';
if ($objn<>'') {
addClassObject('livegpstracks',$objn);
$src=$data[0];
     
//Р В РЎвЂ”Р В РЎвЂўР В Р’В»Р РЋРЎвЂњР РЋРІР‚РЋР В Р’В°Р РЋР вЂ№ Р В РЎвЂ”Р В РЎвЂўР РЋР С“Р В Р’В»Р В Р’ВµР В РўвЂР В Р вЂ¦Р В Р’ВµР В Р’Вµ Р В Р’В·Р В Р вЂ¦Р В Р’В°Р РЋРІР‚РЋР В Р’ВµР В Р вЂ¦Р В РЎвЂР В Р’Вµ Р В Р вЂ Р РЋР вЂљР В Р’ВµР В РЎВР В Р’ВµР В Р вЂ¦Р В РЎвЂ Р В РЎвЂР В РЎВР В РЎвЂ”Р В РЎвЂўР РЋР вЂљР РЋРІР‚С™Р В РЎвЂР РЋР вЂљР В РЎвЂўР В Р вЂ Р В Р’В°Р В Р вЂ¦Р В РЎвЂР РЋР РЏ Р В Р вЂ  Р В Р’В±Р В РўвЂ     
$lud=gg($objn.'.d'); $lut=gg($objn.'.d');         
     
     
     sg( $objn.'.json',$content);
   
   sg( $objn.'.link','https://livegpstracks.com/dv_'.$objn.'.html'); 
    
  sg( $objn.'.title',$title);     



foreach ($src as $key=> $value ) {
   sg( $objn.'.'.$key,$value);
 echo $key;
$upd = false;
}     

$rec=SQLSelectOne("SELECT * FROM lgps_in WHERE DID='".$numer."'");
//$rec['VALUE'] = 'ok';
$smadr=$this->getaddrfromcoord(gg($objn.'.lat'),gg($objn.'.lng'));
//$smadr='СѓР»РёС†Р°';
//$smadr=$this->ga('56.836498','60.691435' );
$rec['VALUE'] = gg($objn.'.lat').','.gg($objn.'.lng') ;
//$rec['VALUE'] = $smadr ;
$rec['STREET'] =$smadr;
$rec['UPDATED'] = date('Y-m-d H:i:s');

SQLUpdate('lgps_in', $rec);


if ($lud<> gg($objn.'.d')   and  ($lut<> gg($objn.'.t'))) {
     
$url = BASE_URL . '/gps.php?latitude=' . gg($objn.'.lat')
        . '&longitude=' . gg($objn.'.lng')
        . '&altitude=' . gg($objn.'.altitude')
        . '&accuracy=' . gg($objn.'.gpsaccuracy') 
        . '&provider=' . gg($objn.'.cellid') 
        . '&speed='       .gg($objn.'.speed') 
        . '&battlevel=' . gg($objn.'.battery') 
        . '&charging=' . gg($objn.'.charging') 
        . '&deviceid=' . $objn ;

getURL($url, 0);
 
$adr=getadrfromxy(gg($objn.'.lat'),gg($objn.'.lng'));  
sg($objn.'.address', $adr); 
 
//$spl=split(',',$adr) ;
$spl=explode(',',$adr) ;
sg($objn.'.short_address', $spl[0]); 

 sg($objn.'.gpsupdate', 'updated'); 


}    
else {sg($objn.'.gpsupdate', 'no need'); }     
    }				

}
						

		}
function ga($x,$y)
{return "ok";}
	
 
   
function readHistory($id, $period, $offset)
{
	$this->getConfig(); 

	$request =
		array( 
			'cmd' => "sensorLog", 
			'id' => $id,
			'period' => $period,
			'offset' => $offset,
			'uuid' => $this->config['UUID'],
			'api_key' => $this->API_KEY
		);

	if($ch = curl_init('http://narodmon.ru/api')) {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'MajorDomo module');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
		$reply = curl_exec($ch); 

		if(!$reply or empty($reply)) 
		{
			echo date("Y-m-d H:i:s")."Request: Connect error : ".$reply."\n";
			return false;
		}

		$data = json_decode($reply, true);
		if(!$data or !is_array($data))
		{
			echo date("Y-m-d H:i:s")."Request: Wrong data\n";
			return false;
		}

		echo date("Y-m-d H:i:s")." Request: ok\n";
			
		curl_close($ch); 

		print_r($data);

		return ($data);
	}	

	return false;
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
  SQLExec('DROP TABLE IF EXISTS lgps_in');
  SQLExec('DROP TABLE IF EXISTS lgps_out');
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
/*
nm_outdata - 
*/
addClass('starline-online'); // Р В Р Р‹Р В РЎвЂўР В Р’В·Р В РўвЂР В Р’В°Р В Р’ВµР В РЎВ Р В РЎвЂќР В Р’В»Р В Р’В°Р РЋР С“Р РЋР С“
addClassMethod('starline-online','OnChange','SQLUpdate(\'objects\', array("ID"=>$this->id, "DESCRIPTION"=>gg(\'sysdate\').\' \'.gg(\'timenow\'))); ');
//addClassProperty('livegpstracks','t');


$prop_id=addClassProperty('starline-online', 'arm', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='на охране'; //   <-----------
SQLUpdate('properties',$property); }


$prop_id=addClassProperty('starline-online', 'battery', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Уровень заряда АКБ'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty('starline-online', 'ctemp', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Температура в салоне'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty('starline-online', 'etemp', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Температура двигателя'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty('starline-online', 'gsm_lvl', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Уровень сигнала GSM'; //   <-----------
SQLUpdate('properties',$property); } 

$prop_id=addClassProperty('starline-online', 'ign', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Двигатель заведен'; //   <-----------
SQLUpdate('properties',$property); } 


$prop_id=addClassProperty('starline-online', 'value', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Остаток средств на счете'; //   <-----------
SQLUpdate('properties',$property); } 


$prop_id=addClassProperty('starline-online', 'y', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='GPS координаты'; //   <-----------
SQLUpdate('properties',$property); } 



$prop_id=addClassProperty('starline-online', 'x', 10);
				  if ($prop_id) {
					  $property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
					  $property['ONCHANGE']='OnChange'; //   <-----------
$property['DESCRIPTION']='GPS координаты'; //   <-----------
					  SQLUpdate('properties',$property);
				  } 

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
