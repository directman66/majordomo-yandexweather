<?php	
  global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }	
  $qry="0";
  // search filters
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['lgps_in'];
  } else {
   $session->data['lgps_in']=$qry;
  }
	
  if (!$qry) $qry="1";
  // SEARCH RESULTS
//$res=SQLSelect("select  titlename TITLE ,descr DESCRIPTION ,max(typename) tip, max(ign) ign, max(status) status, max(arm) arm,max(etemp) etemp,max(ctemp) ctemp,max(mayak_temp) mayak_temp,max(device_id) device_id from (select titlename,descr,if (tip='typename', VALUE,null) typename,if (tip='alias', VALUE,null) alias,if (tip='device_id', VALUE,null) device_id,if (tip='etemp', VALUE,null) etemp,if (tip='ctemp', VALUE,null) ctemp,if (tip='mayak_temp', VALUE,null) mayak_temp ,if (tip='status', VALUE,null) status ,if (tip='arm', VALUE,null) arm ,if (tip='ign', VALUE,null) ign  from   (SELECT objects.TITLE titlename , objects.DESCRIPTION descr, substring(pvalues.PROPERTY_NAME, position('.' in pvalues.PROPERTY_NAME)+1) tip, pvalues.VALUE fROM `objects`,  `pvalues`WHERE  objects.class_id = (SELECT ID FROM `classes` WHERE title='starline-online') and objects.ID=pvalues.OBJECT_ID     )a    )b       group by  titlename,descr");

//  $res=SQLSelect("SELECT `TITLE`,`DESCRIPTION` FROM `objects` WHERE `CLASS_ID` in(SELECT ID  FROM `classes` WHERE title='starline-online')");

$sql=<<<EOD
select  titlename TITLE ,descr DESCRIPTION ,
max(temp) temp ,  max(conditionn) conditionn, max(wind_speed) wind_speed, max(wind_dir) wind_dir, 
max(pressure_mm) pressure_mm, max(humidity) humidity,  max(uv_index) uv_index,
max(forecast_1_day_temp_avg) forecast_1_day_temp_avg, max(forecast_2_day_temp_avg) forecast_2_day_temp_avg,
max(forecast_3_day_temp_avg) forecast_3_day_temp_avg, max(forecast_4_day_temp_avg) forecast_4_day_temp_avg,
max(forecast_5_day_temp_avg) forecast_5_day_temp_avg, max(forecast_6_day_temp_avg) forecast_6_day_temp_avg,
max(forecast_7_day_temp_avg) forecast_7_day_temp_avg,max(forecast_1_daycondition)  forecast_1_daycondition ,  
max(forecast_2_daycondition)  forecast_2_daycondition,  max(forecast_3_daycondition)  forecast_3_daycondition,  
max(forecast_4_daycondition)  forecast_4_daycondition,  max(forecast_5_daycondition)  forecast_5_daycondition,  
max(forecast_6_daycondition)  forecast_6_daycondition,  max(forecast_7_daycondition)  forecast_7_daycondition from (
	select titlename,descr,if (tip='temp', VALUE,null) temp,if (tip='condition', VALUE,null) conditionn,
	if (tip='wind_speed', VALUE,null) wind_speed,if (tip='wind_dir', VALUE,null) wind_dir,if (tip='pressure_mm', VALUE,null)
	pressure_mm,
	if (tip='humidity', VALUE,null) humidity,  
	if (tip='uv_index', VALUE,null) uv_index,  
	if (tip='forecast_1_day_temp_avg', VALUE,null) forecast_1_day_temp_avg,  
	if (tip='forecast_2_day_temp_avg', VALUE,null) forecast_2_day_temp_avg,  
	if (tip='forecast_3_day_temp_avg', VALUE,null) forecast_3_day_temp_avg,   
	if (tip='forecast_4_day_temp_avg', VALUE,null) forecast_4_day_temp_avg,  
	if (tip='forecast_5_day_temp_avg', VALUE,null) forecast_5_day_temp_avg,  
	if (tip='forecast_6_day_temp_avg', VALUE,null) forecast_6_day_temp_avg,  
	if (tip='forecast_7_day_temp_avg', VALUE,null) forecast_7_day_temp_avg,
	if (tip='forecast_1_daycondition', VALUE,null) forecast_1_daycondition,
	if (tip='forecast_2_daycondition', VALUE,null) forecast_2_daycondition,
	if (tip='forecast_3_daycondition', VALUE,null) forecast_3_daycondition,
	if (tip='forecast_4_daycondition', VALUE,null) forecast_4_daycondition,
	if (tip='forecast_5_daycondition', VALUE,null) forecast_5_daycondition,
	if (tip='forecast_6_daycondition', VALUE,null) forecast_6_daycondition,
	if (tip='forecast_7_daycondition', VALUE,null) forecast_7_daycondition 
	from   (SELECT objects.TITLE titlename , objects.DESCRIPTION descr, substring(pvalues.PROPERTY_NAME, position('.' in pvalues.PROPERTY_NAME)+1) tip, pvalues.VALUE fROM `objects`,  `pvalues`WHERE  objects.class_id = (SELECT ID FROM `classes` WHERE title='YandexWeather') and objects.ID=pvalues.OBJECT_ID     )a    )b       group by  titlename,descr
EOD;	
//$res=SQLSelect("select  titlename TITLE ,descr DESCRIPTION ,max(temp) temp ,  max(conditionn) conditionn, max(wind_speed) wind_speed, max(wind_dir) wind_dir, max(pressure_mm) pressure_mm, max(humidity) humidity,  max(uv_index) uv_index,max(forecast_1_day_temp_avg) forecast_1_day_temp_avg, max(forecast_2_day_temp_avg) forecast_2_day_temp_avg,max(forecast_3_day_temp_avg) forecast_3_day_temp_avg, max(forecast_4_day_temp_avg) forecast_4_day_temp_avg,max(forecast_5_day_temp_avg) forecast_5_day_temp_avg, max(forecast_6_day_temp_avg) forecast_6_day_temp_avg,max(forecast_7_day_temp_avg) forecast_7_day_temp_avg,max(forecast_1_daycondition)  forecast_1_daycondition ,  max(forecast_2_daycondition)  forecast_2_daycondition,  max(forecast_3_daycondition)  forecast_3_daycondition,  max(forecast_4_daycondition)  forecast_4_daycondition,  max(forecast_5_daycondition)  forecast_5_daycondition,  max(forecast_6_daycondition)  forecast_6_daycondition,  max(forecast_7_daycondition)  forecast_7_daycondition from (select titlename,descr,if (tip='temp', VALUE,null) temp,if (tip='condition', VALUE,null) conditionn,if (tip='wind_speed', VALUE,null) wind_speed,if (tip='wind_dir', VALUE,null) wind_dir,if (tip='pressure_mm', VALUE,null) pressure_mm,if (tip='humidity', VALUE,null) humidity,  if (tip='uv_index', VALUE,null) uv_index,  if (tip='forecast_1_day_temp_avg', VALUE,null) forecast_1_day_temp_avg,  if (tip='forecast_2_day_temp_avg', VALUE,null) forecast_2_day_temp_avg,  if (tip='forecast_3_day_temp_avg', VALUE,null) forecast_3_day_temp_avg,   if (tip='forecast_4_day_temp_avg', VALUE,null) forecast_4_day_temp_avg,  if (tip='forecast_5_day_temp_avg', VALUE,null) forecast_5_day_temp_avg,  if (tip='forecast_6_day_temp_avg', VALUE,null) forecast_6_day_temp_avg,  if (tip='forecast_7_day_temp_avg', VALUE,null) forecast_7_day_temp_avg,if (tip='forecast_1_daycondition', VALUE,null) forecast_1_daycondition,if (tip='forecast_2_daycondition', VALUE,null) forecast_2_daycondition ,if (tip='forecast_3_daycondition', VALUE,null) forecast_3_daycondition,if (tip='forecast_4_daycondition', VALUE,null) forecast_4_daycondition,if (tip='forecast_5_daycondition', VALUE,null) forecast_5_daycondition ,if (tip='forecast_6_daycondition', VALUE,null) forecast_6_daycondition,if (tip='forecast_7_daycondition', VALUE,null) forecast_7_daycondition      from   (SELECT objects.TITLE titlename , objects.DESCRIPTION descr, substring(pvalues.PROPERTY_NAME, position('.' in pvalues.PROPERTY_NAME)+1) tip, pvalues.VALUE fROM `objects`,  `pvalues`WHERE  objects.class_id = (SELECT ID FROM `classes` WHERE title='YandexWeather') and objects.ID=pvalues.OBJECT_ID     )a    )b       group by  titlename,descr");  

$res=SQLSelect($sql);


	if ($res[0]['TITLE']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    $tmp=explode(' ', $res[$i]['UPDATED']);
    $res[$i]['UPDATED']=fromDBDate($tmp[0])." ".$tmp[1];
   }
   $out['RESULT']=$res;
  }
