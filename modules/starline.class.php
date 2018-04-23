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
class starline extends module {
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

 
 $out['LOGIN'] = $this->config['LOGIN'];
 $out['PWD']=$this->config['PWD'];
 $out['TOKEN']=$this->config['TOKEN'];
	
 $out['EVERY']=$this->config['EVERY'];
 
 if (!$out['UUID']) {
	 $out['UUID'] = md5(microtime() . rand(0, 9999));
	 $this->config['UUID'] = $out['UUID'];
	 $this->saveConfig();
 }
 
 if ($this->view_mode=='update_settings') {
	global $login;
	$this->config['LOGIN']=$login;	 

	global $pwd;
	$this->config['PWD']=$pwd;	 

	global $token;
	$this->config['TOKEN']=$token;	 

   
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
/**
* InData edit/add
*
* @access public
*/
/**
* InData delete record
*
* @access public
*/
 
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

 }
 
 function sendVals($vals){ 
 }
 
 function readData() {
$cookie_file = ROOT . 'cached/starline_cookie.txt'; //в этом файле будет храниться сессия
//$cookie_file = 'dialog_cookie.txt'; //в этом файле будет храниться сессия
// STEP 1 -- LOGIN

$url = 'https://starline-online.ru/user/login'; // ссылка, по которой нам надо зайти
// задаём поля, которые будут отправлены при логине     
$fields = array(
    'LoginForm[login]' => config['LOGIN'], // номер телефона
    'LoginForm[rememberMe]' => 'on', 
 'LoginForm[pass]' =>  config['PWD']

);
$fields_string = '';
foreach ($fields as $key => $value) {
    $fields_string .= urlencode($key) . '=' . urlencode($value) . '&';
}
rtrim($fields_string, '&');	 
	 
	 
//end main function 
}

	
	
function login() {
$cookie_file = ROOT . 'cached/starline_cookie.txt'; //в этом файле будет храниться сессия
//$cookie_file = 'dialog_cookie.txt'; //в этом файле будет храниться сессия
// STEP 1 -- LOGIN

$url = 'https://starline-online.ru/user/login'; // ссылка, по которой нам надо зайти
// задаём поля, которые будут отправлены при логине     
$fields = array(
    'LoginForm[login]' => config['LOGIN'], // номер телефона
    'LoginForm[rememberMe]' => 'on', 
 'LoginForm[pass]' =>  config['PWD']

);
$fields_string = '';
foreach ($fields as $key => $value) {
    $fields_string .= urlencode($key) . '=' . urlencode($value) . '&';
}
rtrim($fields_string, '&');	 
$ch = curl_init();
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
//curl_setopt($ch, CURLOPT_POST, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0',
'Accept: application/json, text/javascript, */*; q=0.01',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest'
));

$result = curl_exec($ch);
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
$cookies = array();
foreach($matches[1] as $item) {
    parse_str($item, $cookie);
    $cookies = array_merge($cookies, $cookie);
}
curl_close($ch);
}
	function getinfo() {
$ck=substr(file_get_contents ($cookie_file),stripos (file_get_contents ($cookie_file), "PHPSESSID"));
echo urldecode($ck);
echo "<br>";
echo "-------------------------";

echo "<br>";
//
$cck2='uechat_34028_first_time=1513103119079; _ym_uid=1513103122184396845; __utmc=219212379; __utmz=219212379.1513103122.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _ym_isad=2; __utma=219212379.1512894299.1513103122.1513103122.1513105346.2; __utmt=1; _ym_visorc_20868619=w; t=ff26ce75bf3bfd86c5a840cecd5209a5; PHPSESSID=fepm7v9s1cuian8fgego9udl67; dce05fce80d4d6404dc03cd4fad6e633=858133b9f2a7a198a16df43c76f3e4557d13dff8a%3A4%3A%7Bi%3A0%3Bs%3A6%3A%22183613%22%3Bi%3A1%3Bs%3A11%3A%22xpenn%40e1.ru%22%3Bi%3A2%3Bi%3A2592000%3Bi%3A3%3Ba%3A13%3A%7Bs%3A8%3A%22slid_uid%22%3Bs%3A6%3A%22182877%22%3Bs%3A10%3A%22first_name%22%3Bs%3A14%3A%22%D0%94%D0%BC%D0%B8%D1%82%D1%80%D0%B8%D0%B9%22%3Bs%3A9%3A%22last_name%22%3Bs%3A16%3A%22%D0%A1%D0%B0%D0%BD%D0%BD%D0%B8%D0%BA%D0%BE%D0%B2%22%3Bs%3A11%3A%22middle_name%22%3Bs%3A0%3A%22%22%3Bs%3A12%3A%22company_name%22%3Bs%3A0%3A%22%22%3Bs%3A3%3A%22sex%22%3Bs%3A1%3A%22M%22%3Bs%3A4%3A%22lang%22%3Bs%3A2%3A%22ru%22%3Bs%3A3%3A%22gmt%22%3Bs%3A2%3A%22%2B5%22%3Bs%3A6%3A%22avatar%22%3Bs%3A37%3A%22https%3A%2F%2Fid.starline.ru%2Favatar%2Fdefault%22%3Bs%3A8%3A%22contacts%22%3Ba%3A3%3A%7Bi%3A0%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22239257%22%3Bs%3A4%3A%22type%22%3Bs%3A5%3A%22email%22%3Bs%3A5%3A%22value%22%3Bs%3A16%3A%22sannikovdi%40ya.ru%22%3Bs%3A9%3A%22confirmed%22%3Bs%3A1%3A%221%22%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22D5vh6oIeepkkaNWXO5I57FGD7_Iu3ydK%22%3B%7Di%3A1%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22239258%22%3Bs%3A4%3A%22type%22%3Bs%3A5%3A%22phone%22%3Bs%3A5%3A%22value%22%3Bs%3A11%3A%2279122962262%22%3Bs%3A9%3A%22confirmed%22%3Bs%3A1%3A%221%22%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22IoRe78yYvOL2np8VKV83I%7EmULNa7SUeN%22%3B%7Di%3A2%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22512913%22%3Bs%3A4%3A%22type%22%3Bs%3A4%3A%22push%22%3Bs%3A5%3A%22value%22%3Bs%3A12%3A%22Redmi+Note+3%22%3Bs%3A9%3A%22confirmed%22%3BN%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22dg5YeRRY9VrZpHRc29BTaoDMSMtobTpI%22%3B%7D%7Ds%3A15%3A%22auth_contact_id%22%3BN%3Bs%3A10%3A%22user_token%22%3Bs%3A39%3A%222c43e3f93a8b506491c3a29ef139ed77%3A182877%22%3Bs%3A5%3A%22roles%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A4%3A%22user%22%3B%7D%7D%7D; uechat_34028_pages_count=4; __utmb=219212379.3.10.1513105346; lang=ru';
//
$cck='uechat_34028_first_time=1513103119079; _ym_uid=1513103122184396845; __utmc=219212379; __utmz=219212379.1513103122.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _ym_isad=2; __utma=219212379.1512894299.1513103122.1513103122.1513105346.2; __utmt=1; _ym_visorc_20868619=w; t=ff26ce75bf3bfd86c5a840cecd5209a5; PHPSESSID=fepm7v9s1cuian8fgego9udl67; dce05fce80d4d6404dc03cd4fad6e633=858133b9f2a7a198a16df43c76f3e4557d13dff8a:4:{i:0;s:6:"183613";i:1;s:11:"xpenn@e1.ru";i:2;i:2592000;i:3;a:13:{s:8:"slid_uid";s:6:"182877";s:10:"first_name";s:14:"Дмитрий";s:9:"last_name";s:16:"Санников";s:11:"middle_name";s:0:"";s:12:"company_name";s:0:"";s:3:"sex";s:1:"M";s:4:"lang";s:2:"ru";s:3:"gmt";s:2:"+5";s:6:"avatar";s:37:"https://id.starline.ru/avatar/default";s:8:"contacts";a:3:{i:0;O:8:"stdClass":5:{s:2:"id";s:6:"239257";s:4:"type";s:5:"email";s:5:"value";s:16:"sannikovdi@ya.ru";s:9:"confirmed";s:1:"1";s:5:"token";s:32:"D5vh6oIeepkkaNWXO5I57FGD7_Iu3ydK";}i:1;O:8:"stdClass":5:{s:2:"id";s:6:"239258";s:4:"type";s:5:"phone";s:5:"value";s:11:"79122962262";s:9:"confirmed";s:1:"1";s:5:"token";s:32:"IoRe78yYvOL2np8VKV83I~mULNa7SUeN";}i:2;O:8:"stdClass":5:{s:2:"id";s:6:"512913";s:4:"type";s:4:"push";s:5:"value";s:12:"Redmi Note 3";s:9:"confirmed";N;s:5:"token";s:32:"dg5YeRRY9VrZpHRc29BTaoDMSMtobTpI";}}s:15:"auth_contact_id";N;s:10:"user_token";s:39:"2c43e3f93a8b506491c3a29ef139ed77:182877";s:5:"roles";a:1:{i:0;s:4:"user";}}}; uechat_34028_pages_count=4; __utmb=219212379.3.10.1513105346; lang=ru';
$cck=urlencode($cck);
echo "<br>";
echo "----cck---------------------";
echo "<br>";
echo $cck ;
echo "<br>";
echo "----cck---------------------";
echo "<br>";
echo "----cck2---------------------";
echo "<br>";
echo $cck2 ;
echo "<br>";
echo "----cck2---------------------";


$url = 'https://starline-online.ru/device?tz=300&_=1512134458324'; 
// это собственно страница, на которую нам надо заходить уже залогинившись
   $ch = curl_init();   
// curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
//   curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
//curl_setopt($ch, CURLOPT_POST, count($fields));
//curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0',
//'Accept: application/json, text/javascript, */*; q=0.01',
//'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
//'X-Requested-With: XMLHttpRequest'

':authority:starline-online.ru',
':method:GET',
':path:/device?tz=300&_=1513105401911',
':scheme:https',
'accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
'accept-encoding:gzip, deflate, br',
'accept-language:ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
//'cookie:'.file_get_contents ($cookie_file), 
//'cookie:'.$ck, 
//'cookie:uechat_34028_first_time=1513103119079; _ym_uid=1513103122184396845; __utmc=219212379; __utmz=219212379.1513103122.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _ym_isad=2; __utma=219212379.1512894299.1513103122.1513103122.1513105346.2; __utmt=1; _ym_visorc_20868619=w; t=ff26ce75bf3bfd86c5a840cecd5209a5; PHPSESSID=fepm7v9s1cuian8fgego9udl67; dce05fce80d4d6404dc03cd4fad6e633=858133b9f2a7a198a16df43c76f3e4557d13dff8a%3A4%3A%7Bi%3A0%3Bs%3A6%3A%22183613%22%3Bi%3A1%3Bs%3A11%3A%22xpenn%40e1.ru%22%3Bi%3A2%3Bi%3A2592000%3Bi%3A3%3Ba%3A13%3A%7Bs%3A8%3A%22slid_uid%22%3Bs%3A6%3A%22182877%22%3Bs%3A10%3A%22first_name%22%3Bs%3A14%3A%22%D0%94%D0%BC%D0%B8%D1%82%D1%80%D0%B8%D0%B9%22%3Bs%3A9%3A%22last_name%22%3Bs%3A16%3A%22%D0%A1%D0%B0%D0%BD%D0%BD%D0%B8%D0%BA%D0%BE%D0%B2%22%3Bs%3A11%3A%22middle_name%22%3Bs%3A0%3A%22%22%3Bs%3A12%3A%22company_name%22%3Bs%3A0%3A%22%22%3Bs%3A3%3A%22sex%22%3Bs%3A1%3A%22M%22%3Bs%3A4%3A%22lang%22%3Bs%3A2%3A%22ru%22%3Bs%3A3%3A%22gmt%22%3Bs%3A2%3A%22%2B5%22%3Bs%3A6%3A%22avatar%22%3Bs%3A37%3A%22https%3A%2F%2Fid.starline.ru%2Favatar%2Fdefault%22%3Bs%3A8%3A%22contacts%22%3Ba%3A3%3A%7Bi%3A0%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22239257%22%3Bs%3A4%3A%22type%22%3Bs%3A5%3A%22email%22%3Bs%3A5%3A%22value%22%3Bs%3A16%3A%22sannikovdi%40ya.ru%22%3Bs%3A9%3A%22confirmed%22%3Bs%3A1%3A%221%22%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22D5vh6oIeepkkaNWXO5I57FGD7_Iu3ydK%22%3B%7Di%3A1%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22239258%22%3Bs%3A4%3A%22type%22%3Bs%3A5%3A%22phone%22%3Bs%3A5%3A%22value%22%3Bs%3A11%3A%2279122962262%22%3Bs%3A9%3A%22confirmed%22%3Bs%3A1%3A%221%22%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22IoRe78yYvOL2np8VKV83I%7EmULNa7SUeN%22%3B%7Di%3A2%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22512913%22%3Bs%3A4%3A%22type%22%3Bs%3A4%3A%22push%22%3Bs%3A5%3A%22value%22%3Bs%3A12%3A%22Redmi+Note+3%22%3Bs%3A9%3A%22confirmed%22%3BN%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22dg5YeRRY9VrZpHRc29BTaoDMSMtobTpI%22%3B%7D%7Ds%3A15%3A%22auth_contact_id%22%3BN%3Bs%3A10%3A%22user_token%22%3Bs%3A39%3A%222c43e3f93a8b506491c3a29ef139ed77%3A182877%22%3Bs%3A5%3A%22roles%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A4%3A%22user%22%3B%7D%7D%7D; uechat_34028_pages_count=4; __utmb=219212379.3.10.1513105346; lang=ru',
//'cookie:PHPSESSID=fepm7v9s1cuian8fgego9udl67; dce05fce80d4d6404dc03cd4fad6e633=858133b9f2a7a198a16df43c76f3e4557d13dff8a%3A4%3A%7Bi%3A0%3Bs%3A6%3A%22183613%22%3Bi%3A1%3Bs%3A11%3A%22xpenn%40e1.ru%22%3Bi%3A2%3Bi%3A2592000%3Bi%3A3%3Ba%3A13%3A%7Bs%3A8%3A%22slid_uid%22%3Bs%3A6%3A%22182877%22%3Bs%3A10%3A%22first_name%22%3Bs%3A14%3A%22%D0%94%D0%BC%D0%B8%D1%82%D1%80%D0%B8%D0%B9%22%3Bs%3A9%3A%22last_name%22%3Bs%3A16%3A%22%D0%A1%D0%B0%D0%BD%D0%BD%D0%B8%D0%BA%D0%BE%D0%B2%22%3Bs%3A11%3A%22middle_name%22%3Bs%3A0%3A%22%22%3Bs%3A12%3A%22company_name%22%3Bs%3A0%3A%22%22%3Bs%3A3%3A%22sex%22%3Bs%3A1%3A%22M%22%3Bs%3A4%3A%22lang%22%3Bs%3A2%3A%22ru%22%3Bs%3A3%3A%22gmt%22%3Bs%3A2%3A%22%2B5%22%3Bs%3A6%3A%22avatar%22%3Bs%3A37%3A%22https%3A%2F%2Fid.starline.ru%2Favatar%2Fdefault%22%3Bs%3A8%3A%22contacts%22%3Ba%3A3%3A%7Bi%3A0%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22239257%22%3Bs%3A4%3A%22type%22%3Bs%3A5%3A%22email%22%3Bs%3A5%3A%22value%22%3Bs%3A16%3A%22sannikovdi%40ya.ru%22%3Bs%3A9%3A%22confirmed%22%3Bs%3A1%3A%221%22%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22D5vh6oIeepkkaNWXO5I57FGD7_Iu3ydK%22%3B%7Di%3A1%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22239258%22%3Bs%3A4%3A%22type%22%3Bs%3A5%3A%22phone%22%3Bs%3A5%3A%22value%22%3Bs%3A11%3A%2279122962262%22%3Bs%3A9%3A%22confirmed%22%3Bs%3A1%3A%221%22%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22IoRe78yYvOL2np8VKV83I%7EmULNa7SUeN%22%3B%7Di%3A2%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22512913%22%3Bs%3A4%3A%22type%22%3Bs%3A4%3A%22push%22%3Bs%3A5%3A%22value%22%3Bs%3A12%3A%22Redmi+Note+3%22%3Bs%3A9%3A%22confirmed%22%3BN%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22dg5YeRRY9VrZpHRc29BTaoDMSMtobTpI%22%3B%7D%7Ds%3A15%3A%22auth_contact_id%22%3BN%3Bs%3A10%3A%22user_token%22%3Bs%3A39%3A%222c43e3f93a8b506491c3a29ef139ed77%3A182877%22%3Bs%3A5%3A%22roles%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A4%3A%22user%22%3B%7D%7D%7D; uechat_34028_pages_count=4; __utmb=219212379.3.10.1513105346; lang=ru', 
//'cookie:dce05fce80d4d6404dc03cd4fad6e633=858133b9f2a7a198a16df43c76f3e4557d13dff8a%3A4%3A%7Bi%3A0%3Bs%3A6%3A%22183613%22%3Bi%3A1%3Bs%3A11%3A%22xpenn%40e1.ru%22%3Bi%3A2%3Bi%3A2592000%3Bi%3A3%3Ba%3A13%3A%7Bs%3A8%3A%22slid_uid%22%3Bs%3A6%3A%22182877%22%3Bs%3A10%3A%22first_name%22%3Bs%3A14%3A%22%D0%94%D0%BC%D0%B8%D1%82%D1%80%D0%B8%D0%B9%22%3Bs%3A9%3A%22last_name%22%3Bs%3A16%3A%22%D0%A1%D0%B0%D0%BD%D0%BD%D0%B8%D0%BA%D0%BE%D0%B2%22%3Bs%3A11%3A%22middle_name%22%3Bs%3A0%3A%22%22%3Bs%3A12%3A%22company_name%22%3Bs%3A0%3A%22%22%3Bs%3A3%3A%22sex%22%3Bs%3A1%3A%22M%22%3Bs%3A4%3A%22lang%22%3Bs%3A2%3A%22ru%22%3Bs%3A3%3A%22gmt%22%3Bs%3A2%3A%22%2B5%22%3Bs%3A6%3A%22avatar%22%3Bs%3A37%3A%22https%3A%2F%2Fid.starline.ru%2Favatar%2Fdefault%22%3Bs%3A8%3A%22contacts%22%3Ba%3A3%3A%7Bi%3A0%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22239257%22%3Bs%3A4%3A%22type%22%3Bs%3A5%3A%22email%22%3Bs%3A5%3A%22value%22%3Bs%3A16%3A%22sannikovdi%40ya.ru%22%3Bs%3A9%3A%22confirmed%22%3Bs%3A1%3A%221%22%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22D5vh6oIeepkkaNWXO5I57FGD7_Iu3ydK%22%3B%7Di%3A1%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22239258%22%3Bs%3A4%3A%22type%22%3Bs%3A5%3A%22phone%22%3Bs%3A5%3A%22value%22%3Bs%3A11%3A%2279122962262%22%3Bs%3A9%3A%22confirmed%22%3Bs%3A1%3A%221%22%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22IoRe78yYvOL2np8VKV83I%7EmULNa7SUeN%22%3B%7Di%3A2%3BO%3A8%3A%22stdClass%22%3A5%3A%7Bs%3A2%3A%22id%22%3Bs%3A6%3A%22512913%22%3Bs%3A4%3A%22type%22%3Bs%3A4%3A%22push%22%3Bs%3A5%3A%22value%22%3Bs%3A12%3A%22Redmi+Note+3%22%3Bs%3A9%3A%22confirmed%22%3BN%3Bs%3A5%3A%22token%22%3Bs%3A32%3A%22dg5YeRRY9VrZpHRc29BTaoDMSMtobTpI%22%3B%7D%7Ds%3A15%3A%22auth_contact_id%22%3BN%3Bs%3A10%3A%22user_token%22%3Bs%3A39%3A%222c43e3f93a8b506491c3a29ef139ed77%3A182877%22%3Bs%3A5%3A%22roles%22%3Ba%3A1%3A%7Bi%3A0%3Bs%3A4%3A%22user%22%3B%7D%7D%7D; uechat_34028_pages_count=4; __utmb=219212379.3.10.1513105346; lang=ru', 
//'cookie:'.urlencode($cck),
 'cookie:'.$cck2,

 
 
 'upgrade-insecure-requests:1',
'user-agent:Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Mobile Safari/537.36'



));

   $result = curl_exec($ch);
   curl_close($ch);
//SaveFile(ROOT . 'cached/dialog_result.txt', $result); // сохранять в файл не обязательно, это я делаю просто для того чтобы посмотреть что внутри

@unlink($cookie_file);


echo $result;
echo "<br>";
echo "-------------------------";
echo "<br>";
$data=json_decode($result,true);
//$objn=$data[0]['id'];
//print_r($data);
//$objn=$data[0]['answer']['devices']['alias'];


//echo $ctemp;
//echo $etemp;
//echo $imei;
//echo $name;
//echo $objn.'----------------';
//addClassObject('livegpstracks',$objn);
//$src=$data[0];

echo "<br>";
echo "-------------------------";
echo "<br>";

$names=$data['answer']['devices'];

foreach ($names as $key=> $value ) {   
// echo $key.':'.$value. "<br>";
 foreach ($value as $key2=> $value2 ) {   
  if ($key2=='alias' )  {
   echo $key2.':'.$value2. "<br>";
   //$devicename=str_replace(" ","_",$value2);
$devicename=$value2;   
   if (gg($devicename."."."alias")<>$devicename) {
    echo "добавляем новое устройство ".$devicename;
   addClassObject('starline-online',$devicename);}
   }
  if (is_array($value2))
  {echo "это массив";
   echo "<br>";
foreach ($value2 as $key3=> $value3 ) { 

echo $key3.':'.$value3. "<br>";
 
 
sg($devicename.'.'.$key3,$value3);  
///                                       
  if (is_array($value3))
  {echo "это массив";
   echo "<br>";
foreach ($value3 as $key4=> $value4 ) { 
 echo $key4.':'.$value4. "<br>";
sg($devicename.'.'.$key4,$value4);  
}}
                                       
///                                       
                                       
}
    
   
  } else {
  echo $devicename.'.'.$key2."::::".$value2;
  echo "<br>";
  sg($devicename.'.'.$key2,$value2);
sg($devicename.'.updated',date('d.m.Y H:i:s'));

   
  }
}
$url = BASE_URL . '/gps.php?latitude=' . gg($devicename.'.y')
        . '&longitude=' . gg($devicename.'.x')
        . '&altitude=' . gg($devicename.'.altitude')
        . '&accuracy=' . gg($devicename.'.gpsaccuracy') 
        . '&provider=' . gg($devicename.'.gsm_lvl') 
        . '&speed='  .gg($devicename.'.speed') 
        . '&battlevel=' . gg($devicename.'.battery') 
        . '&charging=' . gg($devicename.'.charging') 
        . '&deviceid=' .gg($devicename.'.imei')  ;

getURL($url, 0);   
     
		
	 
	 
//end main function 
}	
 
   
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
