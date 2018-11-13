<?php	
$timestamp = time();
$token = md5('eternalsun'.$timestamp);
 
$uuid = "0b122ce93c77f68831839ca1d7cbf44a";
$deviceid = "3fb4aa04ac896f1b51dd48d643d9e76e";

$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='FORECAST_DAY'");
$forecast_day=$cmd_rec['VALUE'];
	
	
//sg('test.starline', 'start '.date());
	


$column2=array();
$column=SQLSelect(' SHOW COLUMNS FROM yaweather_main');
//print_r($column);
//echo "---<br>---<br>";
    $total = count($column);
    for ($i = 0; $i < $total; $i++) {
$column2[]=$column[$i]['Field'];
            }


$mycityid=SQLSelectOne("SELECT * FROM `yaweather_cities` where `mycity`=1 ")['ID'];

//print_r($column2);
//echo "<br>array_search:";

//echo array_search('temp', $column2);
//if (in_array('temp', $column2)) echo 'good'; else  echo 'false';



	
$properties=SQLSelect("SELECT * FROM yaweather_cities where `CHECK`='1'");
foreach ($properties as $did)
{
//header('Content-type: text/json'); 
//$didr= $did['ID'];
//echo "<br>идем по циклу $didr<br>"; 
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
else {$url='https://api.weather.yandex.ru/v1/forecast?geoid='.$cityid.'&lang=ru';}

//echo $url;

$file = file_get_contents($url, false, $context);
	
  //echo 	$file;

//echo gzdecode($file);
$otvet=gzdecode($file);
//print_r($otvet);
$data=json_decode($otvet,true);
//$objn=$data[0]['id'];


$objn=$data['info']['slug'];
$src=$data['info'];

//////////////info
//echo $objn;


//проверяем, нужен ли новый объект	

$new=0;	
$error='0';
//sql="select * from objects where class_id = (select id from classes where title = 'YandexWeather') and objects.TITLE='".$objn."'"	;
//if (empty(SQLSelectOne(sql)['TITLE']))
//    {
if ($objn<>"") {
addClassObject('YandexWeather',$objn);

//  SQLExec("insert into yaweather_main (locality_name  , district_name  , TITLE   ,DESCRIPTION   ,temp   , conditionn   , wind_speed  ,  wind_dir   ,pressure_mm ,   humidity   , uv_index   , forecast_1_day_temp_avg   , forecast_2_day_temp_avg   , forecast_3_day_temp_avg   , forecast_4_day_temp_avg   , forecast_5_day_temp_avg   , forecast_6_day_temp_avg   , forecast_7_day_temp_avg   , forecast_1_daycondition   , forecast_2_daycondition   , forecast_3_daycondition   , forecast_4_daycondition   , forecast_5_daycondition   , forecast_6_daycondition   , forecast_7_daycondition   , forecast_0_morningcondition,    forecast_0_daycondition   , forecast_0_eveningcondition  ,  forecast_0_nightcondition   , forecast_0_morning_temp_avg,    forecast_0_day_temp_avg   , forecast_0_evening_temp_avg  ,  forecast_0_night_temp_avg   , forecast_0_morning_wind_speed,    forecast_0_day_wind_speed   , forecast_0_evening_wind_speed,    forecast_0_night_wind_speed ,   forecast_0_morning_wind_dir,    forecast_0_day_wind_dir   , forecast_0_evening_wind_dir  ,  forecast_0_night_wind_dir   , forecast_0_morning_pressure_mm,    forecast_0_day_pressure_mm   , forecast_0_evening_pressure_mm   , forecast_0_night_short_pressure_mm   ) select  max(locality_name) locality_name, max(district_name) district_name, titlename TITLE ,descr DESCRIPTION ,max(temp) temp ,  max(conditionn) conditionn, max(wind_speed) wind_speed, max(wind_dir) wind_dir, max(pressure_mm) pressure_mm, max(humidity) humidity,  max(uv_index) uv_index,max(forecast_1_day_temp_avg) forecast_1_day_temp_avg, max(forecast_2_day_temp_avg) forecast_2_day_temp_avg,max(forecast_3_day_temp_avg) forecast_3_day_temp_avg, max(forecast_4_day_temp_avg) forecast_4_day_temp_avg,max(forecast_5_day_temp_avg) forecast_5_day_temp_avg, max(forecast_6_day_temp_avg) forecast_6_day_temp_avg,max(forecast_7_day_temp_avg) forecast_7_day_temp_avg,max(forecast_1_daycondition)  forecast_1_daycondition ,  max(forecast_2_daycondition)  forecast_2_daycondition,  max(forecast_3_daycondition)  forecast_3_daycondition,  max(forecast_4_daycondition)  forecast_4_daycondition,  max(forecast_5_daycondition)  forecast_5_daycondition,  max(forecast_6_daycondition)  forecast_6_daycondition,max(forecast_7_daycondition)  forecast_7_daycondition,max(forecast_0_morningcondition)  forecast_0_morningcondition,max(forecast_0_daycondition)  forecast_0_daycondition  ,max(forecast_0_eveningcondition)  forecast_0_eveningcondition ,max(forecast_0_nightcondition)  forecast_0_nightcondition ,max(forecast_0_morning_temp_avg)  forecast_0_morning_temp_avg ,max(forecast_0_day_temp_avg)  forecast_0_day_temp_avg ,max(forecast_0_evening_temp_avg)  forecast_0_evening_temp_avg ,max(forecast_0_night_temp_avg)  forecast_0_night_temp_avg ,max(forecast_0_morning_wind_speed)  forecast_0_morning_wind_speed ,max(forecast_0_day_wind_speed)  forecast_0_day_wind_speed ,max(forecast_0_evening_wind_speed)  forecast_0_evening_wind_speed ,max(forecast_0_night_wind_speed)  forecast_0_night_wind_speed,max(forecast_0_morning_wind_dir)  forecast_0_morning_wind_dir ,max(forecast_0_day_wind_dir)  forecast_0_day_wind_dir ,max(forecast_0_evening_wind_dir)  forecast_0_evening_wind_dir ,max(forecast_0_night_wind_dir)  forecast_0_night_wind_dir ,max(forecast_0_morning_pressure_mm)  forecast_0_morning_pressure_mm ,max(forecast_0_day_pressure_mm)  forecast_0_day_pressure_mm ,max(forecast_0_evening_pressure_mm)  forecast_0_evening_pressure_mm ,max(forecast_0_night_short_pressure_mm)  forecast_0_night_short_pressure_mm from (select titlename,descr,if (tip='temp', VALUE,null) temp,if (tip='condition', VALUE,null) conditionn,if (tip='wind_speed', VALUE,null) wind_speed,if (tip='wind_dir', VALUE,null) wind_dir,if (tip='pressure_mm', VALUE,null) pressure_mm,if (tip='humidity', VALUE,null) humidity,  if (tip='uv_index', VALUE,null) uv_index,  if (tip='forecast_1_day_temp_avg', VALUE,null) forecast_1_day_temp_avg,  if (tip='forecast_2_day_temp_avg', VALUE,null) forecast_2_day_temp_avg,  if (tip='forecast_3_day_temp_avg', VALUE,null) forecast_3_day_temp_avg,   if (tip='forecast_4_day_temp_avg', VALUE,null) forecast_4_day_temp_avg,  if (tip='forecast_5_day_temp_avg', VALUE,null) forecast_5_day_temp_avg,  if (tip='forecast_6_day_temp_avg', VALUE,null) forecast_6_day_temp_avg,  if (tip='forecast_7_day_temp_avg', VALUE,null) forecast_7_day_temp_avg,if (tip='forecast_1_daycondition', VALUE,null) forecast_1_daycondition,if (tip='forecast_2_daycondition', VALUE,null) forecast_2_daycondition ,if (tip='forecast_3_daycondition', VALUE,null) forecast_3_daycondition,if (tip='forecast_4_daycondition', VALUE,null) forecast_4_daycondition,if (tip='forecast_5_daycondition', VALUE,null) forecast_5_daycondition ,if (tip='forecast_6_daycondition', VALUE,null) forecast_6_daycondition,if (tip='forecast_7_daycondition', VALUE,null) forecast_7_daycondition  ,if (tip='forecast_0_morningcondition', VALUE,null) forecast_0_morningcondition  ,if (tip='forecast_0_daycondition', VALUE,null) forecast_0_daycondition   ,if (tip='forecast_0_eveningcondition ', VALUE,null) forecast_0_eveningcondition    ,if (tip='forecast_0_nightcondition', VALUE,null) forecast_0_nightcondition ,if (tip='forecast_0_morning_temp_avg', VALUE,null) forecast_0_morning_temp_avg  ,if (tip='forecast_0_day_temp_avg', VALUE,null) forecast_0_day_temp_avg    ,if (tip='forecast_0_evening_temp_avg ', VALUE,null) forecast_0_evening_temp_avg    ,if (tip='forecast_0_night_temp_avg', VALUE,null) forecast_0_night_temp_avg  ,if (tip='forecast_0_morning_wind_speed', VALUE,null) forecast_0_morning_wind_speed   ,if (tip='forecast_0_day_wind_speed', VALUE,null) forecast_0_day_wind_speed    ,if (tip='forecast_0_evening_wind_speed ', VALUE,null) forecast_0_evening_wind_speed     ,if (tip='forecast_0_night_wind_speed', VALUE,null) forecast_0_night_wind_speed  ,if (tip='forecast_0_morning_wind_dir', VALUE,null) forecast_0_morning_wind_dir  ,if (tip='forecast_0_day_wind_dir', VALUE,null) forecast_0_day_wind_dir    ,if (tip='forecast_0_evening_wind_dir', VALUE,null) forecast_0_evening_wind_dir     ,if (tip='forecast_0_night_wind_dir', VALUE,null) forecast_0_night_wind_dir  ,if (tip='forecast_0_morning_pressure_mm', VALUE,null) forecast_0_morning_pressure_mm,if (tip='forecast_0_day_pressure_mm', VALUE,null) forecast_0_day_pressure_mm    ,if (tip='forecast_0_evening_pressure_mm', VALUE,null) forecast_0_evening_pressure_mm   ,if (tip='forecast_0_night_short_pressure_mm', VALUE,null) forecast_0_night_short_pressure_mm ,if (tip='district_name', VALUE,null) district_name,if (tip='locality_name', VALUE,null) locality_name     from   (SELECT objects.TITLE titlename , objects.DESCRIPTION descr, substring(pvalues.PROPERTY_NAME, position('.' in pvalues.PROPERTY_NAME)+1) tip, pvalues.VALUE fROM `objects`,  `pvalues`WHERE  objects.class_id = (SELECT ID FROM `classes` WHERE title='YandexWeather') and objects.ID=pvalues.OBJECT_ID and   objects.TITLE IS NOT NULL)a    )b    where titlename<>'' group by  titlename,descr");
        


$zapros="select * from yaweather_main where TITLE='$objn'";
//echo "<br>".$zapros;
//print_r (array_keys($sql));

$sql=SQLSelectOne($zapros);
//echo  (key($sql));




$sql['TITLE']=$objn;
$sql['CID']=$cityid;
$ccityid=$cityid;
/*

$sql['locality_name']=
$sql['district_name']=
$sql['DESCRIPTION']=
$sql['temp']=
$sql['conditionn']=
$sql['wind_speed']=
$sql['wind_dir']=
$sql['pressure_mm']=
$sql['humidity']=
$sql['uv_index']=
$sql['forecast_1_day_temp_avg']=
$sql['forecast_2_day_temp_avg']=
$sql['forecast_3_day_temp_avg']=
$sql['forecast_4_day_temp_avg']=
$sql['forecast_5_day_temp_avg']=
$sql['forecast_6_day_temp_avg']=
$sql['forecast_7_day_temp_avg']=
$sql['forecast_1_daycondition']=
$sql['forecast_2_daycondition']=
$sql['forecast_3_daycondition']=
$sql['forecast_4_daycondition']=
$sql['forecast_5_daycondition']=
$sql['forecast_6_daycondition']=
$sql['forecast_7_daycondition']=
$sql['forecast_0_morningcondition']=
$sql['forecast_0_daycondition']=
$sql['forecast_0_eveningcondition']=
$sql['forecast_0_nightcondition']=
$sql['forecast_0_morning_temp_avg']=
$sql['forecast_0_day_temp_avg']=
$sql['forecast_0_evening_temp_avg']=
$sql['forecast_0_night_temp_avg']=
$sql['forecast_0_morning_wind_speed']=
$sql['forecast_0_day_wind_speed']=
$sql['forecast_0_evening_wind_speed']=
$sql['forecast_0_night_wind_speed']=
$sql['forecast_0_morning_wind_dir']=
$sql['forecast_0_day_wind_speed']=
$sql['forecast_0_evening_wind_speed']=
$sql['forecast_0_night_wind_speed']=
$sql['forecast_0_morning_wind_dir']=
$sql['forecast_0_day_wind_dir']=
$sql['forecast_0_evening_wind_dir']=
$sql['forecast_0_night_wind_dir']=
$sql['forecast_0_morning_pressure_mm']=
$sql['forecast_0_day_pressure_mm']=
$sql['forecast_0_evening_pressure_mm']=
$sql['forecast_0_night_short_pressure_mm']=

*/


$new=1;
} else $error=1; 

///////////////////////////
//sg( 'test.vm',$otvet);

//print_r($sql);
$src=$data['info'];
sg( $objn.'.now',gg('sysdate').' '.gg('timenow')); 
//echo time();
//echo "<br>"; 
if ($error==0){	
foreach ($src as $key=> $value ) { 
if (is_array($value)) {
foreach ($value as $key2=> $value2 ) {
	
//if (gg($objn.'.'.$key.'_'.$key2)<>$value2) 
//if (isset($sql[$key.'_'.$key2])) 

//if (array_key_exists($sql[$key.'_'.$key2],$sql)) 
//if (array_key_exists($key.'_'.$key2,$sql)) 
if (in_array($key.'_'.$key2, $column2)) 
//if (array_search('green', $array)


{$sql[$key.'_'.$key2]=$value2; 
//echo '<br>ключ '.$key.'_'.$key2.' имеется  в массиве';
}
///else echo '<br>ключ '.$key.'_'.$key2.' отсутсвует в массиве';
///////sg( $objn.'.'.$key.'_'.$key2,$value2); 
// echo $objn.'.'.$key.'_'.$key2.":".$value2;
		     }
}	
else	
{
//if (gg($objn.'.'.$key.'_'.$key)<>$value)

//if (isset($sql[$key])) 
//if (array_key_exists($key,$sql)) 
if (in_array($key, $column2)) 
{ $sql[$key]=$value; 
//echo '<br>ключ '.$key.' имеется  в массиве';
}
//else echo '<br>ключ '.$key.' отсутсвует в массиве';
///////	sg( $objn.'.'.$key,$value); 
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
//if ($sql[$key.'_'.$key2])  
//if (array_key_exists($key.'_'.$key2,$sql)) 
if (in_array($key.'_'.$key2, $column2)) 
$sql[$key.'_'.$key2]=$value2;
///////	sg( $objn.'.'.$key.'_'.$key2,$value2); 
//echo $objn.'.'.$key.'_'.$key2.":".$value2;  
/// echo "<br>";
}
}	
else	
{
//if (gg($objn.'.'.$key.'_'.$key)!=$value)
//if ($sql[$key])  
//if (array_key_exists($key,$sql)) 
if (in_array($key, $column2)) 
$sql[$key]=$value;
///////	sg( $objn.'.'.$key,$value); 
///	echo $objn.'.'.$key.":".$value;  
}     
}	}
	
////////////////////////////////	
/////fact
///////////////////////////////////////////////////	
$src=$data['fact'];
if ($error==0){
	foreach ($src as $key=> $value ) { 
if (!is_array($value)){
//if ($sql[$key]) 


//if (array_key_exists($key,$sql)) 
//echo array_search($key, $column2);
if (in_array($key, $column2)) 

//if (array_search($key, $column2))

{
$sql[$key]=$value;
//echo '<br>ключ '.$key.' имеется  в массиве';
}
//else {echo '<br>ключ '.$key.' отсутсвует   в массиве';}
//sg( $objn.'.'.$key,$value);
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

//if ($sql["forecast_".$day."_".$key.'_temp_avg'])
if (in_array("forecast_".$day."_".$key.'_temp_avg', $column2)) 
        $sql["forecast_".$day."_".$key.'_temp_avg']=$data['forecasts'][$day]['parts'][$key]['temp_avg'];
///////sg( $fobjn.'.'."forecast_".$day."_".$key.'_temp_avg',$data['forecasts'][$day]['parts'][$key]['temp_avg']);

//if ($sql["forecast_".$day."_".$key.'_wind_speed'])
if (in_array("forecast_".$day."_".$key.'_wind_speed', $column2)) 
        $sql["forecast_".$day."_".$key.'_wind_speed']=$data['forecasts'][$day]['parts'][$key]['wind_speed'];
///////sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_speed',$data['forecasts'][$day]['parts'][$key]['wind_speed']);

//if ($sql["forecast_".$day."_".$key.'_wind_gust'])
if (in_array("forecast_".$day."_".$key.'_wind_gust', $column2)) 
        $sql["forecast_".$day."_".$key.'_wind_gust']=$data['forecasts'][$day]['parts'][$key]['wind_gust'];
///////sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_gust',$data['forecasts'][$day]['parts'][$key]['wind_gust']);

//if ($sql["forecast_".$day."_".$key.'_wind_gust'])
if (in_array("forecast_".$day."_".$key.'_wind_gust', $column2)) 
        $sql["forecast_".$day."_".$key.'_wind_dir']=$data['forecasts'][$day]['parts'][$key]['wind_dir'];
///////sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_dir',$data['forecasts'][$day]['parts'][$key]['wind_dir']);

 //if ($sql["forecast_".$day."_".$key.'_wind_gust'])
if (in_array("forecast_".$day."_".$key.'_pressure_mm', $column2)) 
        $sql["forecast_".$day."_".$key.'_pressure_mm']=$data['forecasts'][$day]['parts'][$key]['pressure_mm'];
///////sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_mm',$data['forecasts'][$day]['parts'][$key]['pressure_mm']);

//if ($sql["forecast_".$day."_".$key.'_pressure_pa'])
if (in_array("forecast_".$day."_".$key.'_pressure_pa', $column2)) 
        $sql["forecast_".$day."_".$key.'_pressure_pa']=$data['forecasts'][$day]['parts'][$key]['pressure_pa'];
///////sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_pa',$data['forecasts'][$day]['parts'][$key]['pressure_pa']);

//if ($sql["forecast_".$day."_".$key.'_pressure_pa'])
if (in_array("forecast_".$day."_".$key.'_humidity', $column2)) 
        $sql["forecast_".$day."_".$key.'_humidity']=$data['forecasts'][$day]['parts'][$key]['humidity'];
///////sg( $fobjn.'.'."forecast_".$day."_".$key.'_humidity',$data['forecasts'][$day]['parts'][$key]['humidity']);

//if ($sql["forecast_".$day."_".$key.'_pressure_pa'])
//if ($sql["forecast_".$day."_".$key.'condition'])
if (in_array("forecast_".$day."_".$key.'condition', $column2)) 
        $sql["forecast_".$day."_".$key.'condition']=$data['forecasts'][$day]['parts'][$key]['condition'];
///////sg( $fobjn.'.'."forecast_".$day."_".$key.'condition',$data['forecasts'][$day]['parts'][$key]['condition']);

if (in_array("forecast_".$day."_".$key.'daytime', $column2)) 
        $sql["forecast_".$day."_".$key.'daytime']=$data['forecasts'][$day]['parts'][$key]['daytime'];
///////sg( $fobjn.'.'."forecast_".$day."_".$key.'daytime',$data['forecasts'][$day]['parts'][$key]['daytime']); 

}
}


}
} 



//$sql['ID']=$sql['TITLE'];
//echo "<br><br>";
//print_r($sql);

if ($objn<>""){

if ($sql['ID']) 
{
sqlupdate('yaweather_main', $sql);
} 
else 
{
sqlinsert('yaweather_main', $sql);
} 

/////сохраним все свойства в объекте




addClassObject('YandexWeather',$sql['TITLE']);
addClassObject('YandexWeather','yw_mycity');
//    $total = count($sql);
foreach ($sql as $par=>$val){
//    for ($i = 0; $i < $total; $i++) {
if ($par<>"")  sg($sql['TITLE'].'.'.$par, $val);

//echo $mycityid.":".$cityid.'<br>';


if (($par<>"")&&($mycityid==$ccityid)) sg('yw_mycity.'.$par, $val);

//echo $sql['TITLE'].".".$par.":".$val."<br>";
            }





}


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
//////////////////
//end mycity
*/
	
	

	
}

