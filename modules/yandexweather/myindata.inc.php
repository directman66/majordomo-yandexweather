<?php	
//////////////////////////
//////////////////////////
///my city
//////////////////////////
//////////////////////////
$trandtempfa='fa-pause-circle';
$trandhumfa='fa-pause-circle';
$trandpresfa='fa-pause-circle';
$told = round((float) getHistoryAvg("yw_mycity.temp", strtotime("-1 day")));





if (gg('yw_mycity.trandtempfa')<>"") { $trandtempfa=gg('yw_mycity.trandtempfa');}
if (gg('yw_mycity.trandhumfa')<>"") {$trandhumfa=gg('yw_mycity.trandhumfa');}
if (gg('yw_mycity.trandpresfa')<>"") {$trandpresfa=gg('yw_mycity.trandpresfa');}
if (gg('yw_mycity.conditionrus')<>"")  {$conditionrus=gg('yw_mycity.conditionrus');}
if (gg('yw_mycity.condition1rus')<>"") {$condition1rus=gg('yw_mycity.condition1rus');}
if (gg('yw_mycity.condition2rus')<>"") {$condition2rus=gg('yw_mycity.condition2rus');}
if (gg('yw_mycity.condition3rus')<>"") {$condition3rus=gg('yw_mycity.condition3rus');}
$sql="select  ".$skin2." SKIN, '".$conditionrus."' conditionrus, '".$condition1rus."' condition1rus, '".$trandtempfa."' trandtempfa,'".$trandhumfa."' trandhumfa,'".$trandpresfa."' trandpresfa, max(locality_name) locality_name, max(district_name) district_name, DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 DAY),'%d/%m') d1, DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 2 DAY),'%d/%m') d2, DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 3 DAY),'%d/%m') d3, DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 4 DAY),'%d/%m') d4, DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 5 DAY),'%d/%m')d5, DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 6 DAY),'%d/%m') d6, DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 DAY),'%d/%m') d7, titlename TITLE ,descr DESCRIPTION ,max(temp) temp ,  max(conditionn) conditionn, max(wind_speed) wind_speed, max(wind_dir) wind_dir, max(pressure_mm) pressure_mm, max(humidity) humidity,  max(uv_index) uv_index,max(forecast_1_day_temp_avg) forecast_1_day_temp_avg, max(forecast_2_day_temp_avg) forecast_2_day_temp_avg,max(forecast_3_day_temp_avg) forecast_3_day_temp_avg, max(forecast_4_day_temp_avg) forecast_4_day_temp_avg,max(forecast_5_day_temp_avg) forecast_5_day_temp_avg, max(forecast_6_day_temp_avg) forecast_6_day_temp_avg,max(forecast_7_day_temp_avg) forecast_7_day_temp_avg,max(forecast_1_daycondition)  forecast_1_daycondition ,  max(forecast_2_daycondition)  forecast_2_daycondition,  max(forecast_3_daycondition)  forecast_3_daycondition,  max(forecast_4_daycondition)  forecast_4_daycondition,  max(forecast_5_daycondition)  forecast_5_daycondition,  max(forecast_6_daycondition)  forecast_6_daycondition,max(forecast_7_daycondition)  forecast_7_daycondition,max(forecast_0_morningcondition)  forecast_0_morningcondition,max(forecast_0_daycondition)  forecast_0_daycondition  ,max(forecast_0_eveningcondition)  forecast_0_eveningcondition ,max(forecast_0_nightcondition)  forecast_0_nightcondition ,max(forecast_0_morning_temp_avg)  forecast_0_morning_temp_avg ,max(forecast_0_day_temp_avg)  forecast_0_day_temp_avg ,max(forecast_0_evening_temp_avg)  forecast_0_evening_temp_avg ,max(forecast_0_night_temp_avg)  forecast_0_night_temp_avg ,max(forecast_0_morning_wind_speed)  forecast_0_morning_wind_speed ,max(forecast_0_day_wind_speed)  forecast_0_day_wind_speed ,max(forecast_0_evening_wind_speed)  forecast_0_evening_wind_speed ,max(forecast_0_night_wind_speed)  forecast_0_night_wind_speed,max(forecast_0_morning_wind_dir)  forecast_0_morning_wind_dir ,max(forecast_0_day_wind_dir)  forecast_0_day_wind_dir ,max(forecast_0_evening_wind_dir)  forecast_0_evening_wind_dir ,max(forecast_0_night_wind_dir)  forecast_0_night_wind_dir ,max(forecast_0_morning_pressure_mm)  forecast_0_morning_pressure_mm ,max(forecast_0_day_pressure_mm)  forecast_0_day_pressure_mm ,max(forecast_0_evening_pressure_mm)  forecast_0_evening_pressure_mm ,max(forecast_0_night_short_pressure_mm)  forecast_0_night_short_pressure_mm, '$told' told from (select titlename,descr,if (tip='temp', VALUE,null) temp,if (tip='condition', VALUE,null) conditionn,if (tip='wind_speed', VALUE,null) wind_speed,if (tip='wind_dir', VALUE,null) wind_dir,if (tip='pressure_mm', VALUE,null) pressure_mm,if (tip='humidity', VALUE,null) humidity,  if (tip='uv_index', VALUE,null) uv_index,  if (tip='forecast_1_day_temp_avg', VALUE,null) forecast_1_day_temp_avg,  if (tip='forecast_2_day_temp_avg', VALUE,null) forecast_2_day_temp_avg,  if (tip='forecast_3_day_temp_avg', VALUE,null) forecast_3_day_temp_avg,   if (tip='forecast_4_day_temp_avg', VALUE,null) forecast_4_day_temp_avg,  if (tip='forecast_5_day_temp_avg', VALUE,null) forecast_5_day_temp_avg,  if (tip='forecast_6_day_temp_avg', VALUE,null) forecast_6_day_temp_avg,  if (tip='forecast_7_day_temp_avg', VALUE,null) forecast_7_day_temp_avg,if (tip='forecast_1_daycondition', VALUE,null) forecast_1_daycondition,if (tip='forecast_2_daycondition', VALUE,null) forecast_2_daycondition ,if (tip='forecast_3_daycondition', VALUE,null) forecast_3_daycondition,if (tip='forecast_4_daycondition', VALUE,null) forecast_4_daycondition,if (tip='forecast_5_daycondition', VALUE,null) forecast_5_daycondition ,if (tip='forecast_6_daycondition', VALUE,null) forecast_6_daycondition,if (tip='forecast_7_daycondition', VALUE,null) forecast_7_daycondition  ,if (tip='forecast_0_morningcondition', VALUE,null) forecast_0_morningcondition  ,if (tip='forecast_0_daycondition', VALUE,null) forecast_0_daycondition   ,if (tip='forecast_0_eveningcondition ', VALUE,null) forecast_0_eveningcondition    ,if (tip='forecast_0_nightcondition', VALUE,null) forecast_0_nightcondition ,if (tip='forecast_0_morning_temp_avg', VALUE,null) forecast_0_morning_temp_avg  ,if (tip='forecast_0_day_temp_avg', VALUE,null) forecast_0_day_temp_avg    ,if (tip='forecast_0_evening_temp_avg ', VALUE,null) forecast_0_evening_temp_avg    ,if (tip='forecast_0_night_temp_avg', VALUE,null) forecast_0_night_temp_avg  ,if (tip='forecast_0_morning_wind_speed', VALUE,null) forecast_0_morning_wind_speed   ,if (tip='forecast_0_day_wind_speed', VALUE,null) forecast_0_day_wind_speed    ,if (tip='forecast_0_evening_wind_speed ', VALUE,null) forecast_0_evening_wind_speed     ,if (tip='forecast_0_night_wind_speed', VALUE,null) forecast_0_night_wind_speed  ,if (tip='forecast_0_morning_wind_dir', VALUE,null) forecast_0_morning_wind_dir  ,if (tip='forecast_0_day_wind_dir', VALUE,null) forecast_0_day_wind_dir    ,if (tip='forecast_0_evening_wind_dir', VALUE,null) forecast_0_evening_wind_dir     ,if (tip='forecast_0_night_wind_dir', VALUE,null) forecast_0_night_wind_dir  ,if (tip='forecast_0_morning_pressure_mm', VALUE,null) forecast_0_morning_pressure_mm,if (tip='forecast_0_day_pressure_mm', VALUE,null) forecast_0_day_pressure_mm    ,if (tip='forecast_0_evening_pressure_mm', VALUE,null) forecast_0_evening_pressure_mm   ,if (tip='forecast_0_night_short_pressure_mm', VALUE,null) forecast_0_night_short_pressure_mm ,if (tip='district_name', VALUE,null) district_name,if (tip='locality_name', VALUE,null) locality_name     from   (SELECT objects.TITLE titlename , objects.DESCRIPTION descr, substring(pvalues.PROPERTY_NAME, position('.' in pvalues.PROPERTY_NAME)+1) tip, pvalues.VALUE fROM `objects`,  `pvalues`WHERE  objects.class_id = (SELECT ID FROM `classes` WHERE title='YandexWeather') and objects.ID=pvalues.OBJECT_ID and   objects.TITLE IS NOT NULL)a    )b    where titlename is not null and titlename='yw_mycity' and titlename<>'' group by  titlename,descr order by 1";
debmes($sql, 'yandexweather');
$res=SQLSelect($sql);  


//$res=SQLSelect($sql);


	if ($res[0]['TITLE']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    $tmp=explode(' ', $res[$i]['UPDATED']);
    $res[$i]['UPDATED']=fromDBDate($tmp[0])." ".$tmp[1];
   }
   $out['RESULTMYCITY']=$res;
  }


