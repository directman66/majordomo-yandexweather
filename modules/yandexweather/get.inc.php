<?php	
$timestamp = time();
$token = md5('eternalsun'.$timestamp);
 
$uuid = "0b122ce93c77f68831839ca1d7cbf44a";
$deviceid = "3fb4aa04ac896f1b51dd48d643d9e76e";

$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='FORECAST_DAY'");
$forecast_day=$cmd_rec['VALUE'];
	
	
	$properties=SQLSelect("SELECT * FROM `yaweather_cities` where `check`=1   ");

//sg('test.starline', 'start '.date());
	

	
	
foreach ($properties as $did)
{
//header('Content-type: text/json'); 
$didr= $did['ID'];
echo "<br>идем по циклу $didr<br>"; 
//sg('test.vm', $didr);
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
	
$cityid=$did['ID'];
$latlon=$did['latlon'];	
	
 //ID города узнаем тут: https://pogoda.yandex.ru/static/cities.xml
//region="11162" id="28440
//$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid=54&lang=ru', false, $context);
//$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid=53&lang=ru', false, $context);
//$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid='.$cityid.'&lang=ru', false, $context);	
//if (isset($cityid)) {$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid='.$cityid.'&lang=ru', false, $context);}
//if (isset($latlon)) {$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?'.$latlon.'&lang=ru', false, $context);}	
//$file = file_get_contents('https://api.weather.yandex.ru/v1/locations?lang=ru', false, $context);
 
if (strlen($latlon)>5) {$url='https://api.weather.yandex.ru/v1/forecast?'.$latlon.'&lang=ru';}	
else {$url='https://api.weather.yandex.ru/v1/forecast?geoid='.$didr.'&lang=ru';}

echo $url;

$file = file_get_contents($url, false, $context);
	
  //echo 	$file;

//echo gzdecode($file);
$otvet=gzdecode($file);
print_r($otvet);
$data=json_decode($otvet,true);
//$objn=$data[0]['id'];
$objn=$data['info']['slug'];
$src=$data['info'];

//////////////info
echo $objn;
//проверяем, нужен ли новый объект	
$new=0;	
$error='0';
//sql="select * from objects where class_id = (select id from classes where title = 'YandexWeather') and objects.TITLE='".$objn."'"	;
//if (empty(SQLSelectOne(sql)['TITLE']))
//    {
if ($objn<>"") {
addClassObject('YandexWeather',$objn);
$new=1;
} else $error=1; 

///////////////////////////
//sg( 'test.vm',$otvet);
$src=$data['info'];
sg( $objn.'.now',gg('sysdate').' '.gg('timenow')); 
//echo time();
//echo "<br>"; 
if ($error==0){	
foreach ($src as $key=> $value ) { 
if (is_array($value)) {
foreach ($value as $key2=> $value2 ) {
	
//if (gg($objn.'.'.$key.'_'.$key2)<>$value2) 
sg( $objn.'.'.$key.'_'.$key2,$value2); 
// echo $objn.'.'.$key.'_'.$key2.":".$value2;
		     }
}	
else	
{
//if (gg($objn.'.'.$key.'_'.$key)<>$value)
	sg( $objn.'.'.$key,$value); 
//echo  $objn.'.'.$key.":".$value;  
//echo "<br>"; 
}
} }    
////////////////////////////////
//////////////geo_object
$src=$data['geo_object'];
if ($error==0){
foreach ($src as $key=> $value ) {
if (is_array($value)) {
foreach ($value as $key2=> $value2 ) {
//if (gg($objn.'.'.$key.'_'.$key2)!=$value2) 	
	sg( $objn.'.'.$key.'_'.$key2,$value2); 
//echo $objn.'.'.$key.'_'.$key2.":".$value2;  
/// echo "<br>";
}
}	
else	
{
//if (gg($objn.'.'.$key.'_'.$key)!=$value)
	sg( $objn.'.'.$key,$value); 
///	echo $objn.'.'.$key.":".$value;  
}     
}	}
	
////////////////////////////////	
/////fact
///////////////////////////////////////////////////	
$src=$data['fact'];
if ($error==0){
	foreach ($src as $key=> $value ) { 
if (!is_array($value)){sg( $objn.'.'.$key,$value);
if (($key=='pressure_mm')&&($value=='')) $error=1;
} 

}
}
///////////////////////////////////////////////////	///////////////////////////////////////////////////	
///////////////////////////////////////////////////	
///////////////////////////////////////////////////	
///forecast
//////////////
	$fobjn= $objn;
	$src=$data['forecasts'][0]['parts'];
if ($error==0){
foreach ($data['forecasts'] as $day=> $value ) 
{ 
foreach ($data['forecasts'][$day]['parts'] as $key=> $value ) {    
if  ($day<=$forecast_day)
{

sg( $fobjn.'.'."forecast_".$day."_date",date("d-m-Y", time()+3600*24*$day));
sg( $fobjn.'.'."forecast_".$day."_".$key.'_temp_avg',$data['forecasts'][$day]['parts'][$key]['temp_avg']);
sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_speed',$data['forecasts'][$day]['parts'][$key]['wind_speed']);
sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_gust',$data['forecasts'][$day]['parts'][$key]['wind_gust']);
sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_dir',$data['forecasts'][$day]['parts'][$key]['wind_dir']);
sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_mm',$data['forecasts'][$day]['parts'][$key]['pressure_mm']);
sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_pa',$data['forecasts'][$day]['parts'][$key]['pressure_pa']);
sg( $fobjn.'.'."forecast_".$day."_".$key.'_humidity',$data['forecasts'][$day]['parts'][$key]['humidity']);
sg( $fobjn.'.'."forecast_".$day."_".$key.'condition',$data['forecasts'][$day]['parts'][$key]['condition']);
sg( $fobjn.'.'."forecast_".$day."_".$key.'daytime',$data['forecasts'][$day]['parts'][$key]['daytime']); 

}
}
}}
//////////////////////////////


/*
//mycity	
$objmycity='yw_mycity';
//проверяем, нужен ли новый объект	
//sql="select * from objects where class_id = (select id from classes where title = 'YandexWeather') and objects.TITLE='".$objmycity."'"	;
//if (empty(SQLSelectOne(sql)['TITLE']))
//    {
addClassObject('YandexWeather',$objmycity);	
    $new=1;
//    } 	
$mycity1=SQLSelectOne("SELECT ID FROM `yaweather_cities` where `mycity`=1 ");
$mycity=$mycity1['ID'];	
sg($objmycity.'.cityID', $mycity);
	
if (($mycity==$cityid)&&($error=='0')&&($fobjn<>"")){
$objprops=get_props($fobjn);
foreach ($objprops as $value){ 
	if (gg($objmycity.'.'.$value)<>gg($fobjn.".".$value));
	sg($objmycity.'.'.$value,gg($fobjn.".".$value));
				}	
			}
*/	
	

	
}

