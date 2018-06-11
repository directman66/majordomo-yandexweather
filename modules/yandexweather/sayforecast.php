<?php	
//require(DIR_MODULES."yandexweather/ywext.inc.php");
//include_once(DIR_MODULES."yandexweather/ywext.inc.php");
include(DIR_MODULES."yandexweather/ywext.inc.php");
//$text=gettextforecast_long();

//$condition=$this->getconditionrus(gg('yw_mycity.condition'));	
$condition=getconditionrusincl(gg('yw_mycity.condition'));	
//getconditionrusincl			
//$condition=gg('yw_mycity.conditionrus');				
	
	
$status .= "Сейчас на улице по данным метеослужб " .$condition . ", ";
$w = round(gg("yw_mycity.temp"));
//$status .= 'температура ' . chti($w, 'градус', 'градуса', 'градусов')  . " цельсия, ";
$status .= 'температура '.$w." градусов цельсия, ";
//if (gg('all_in_one.zaoknom_actual')=='1' && (gg('all_in_one.zaoknom2_actual')=="1"))
//{$realFellTemp = round(min(gg("all_in_one.zaoknomtemp"),gg("all_in_one.zaoknom2temp")));}
//else if 
//(gg('all_in_one.zaoknom_actual')=='1' && (gg('all_in_one.zaoknom2_actual')=="0"))
//{$realFellTemp = round(gg("all_in_one.zaoknomtemp"));} 
//else if 
//(gg('all_in_one.zaoknom_actual')=='0' && (gg('all_in_one.zaoknom2_actual')=="1"))
//{$realFellTemp = round(gg("all_in_one.zaoknom2temp"));} 
//else if 
//(gg('all_in_one.zaoknom_actual')=='0' && (gg('all_in_one.zaoknom2_actual')=="0"))
//{$realFellTemp = round(gg("ow_fact.realFell"));} 
//else {$realFellTemp = round(gg("ow_fact.realFell"));     }
//if ($w != $realFellTemp) {
//    $status .= "на датчиках на балконе  " . chti($realFellTemp, 'градус', 'градуса', 'градусов')  . " цельсия, ";
//}
//$w = gg("ow_fact.realFell");
$w = gg("yw_mycity.temp");
if ($w < -40) {
    $status .= 'мы морозов не боимся! ';
} elseif ($w < -30) { 
     $status .= 'одевайтесь теплее, очень холодно, ';
} elseif ($w < -20) {
    $status .= 'самое время есть мороженое, ';
} elseif ($w < -10) {
    $status .= 'холодновато, ';
} elseif ($w <- 3)  {
    $status .= 'не особо холодно, ';
} elseif ($w < 3) {
    $status .= 'значит, возможно, гололёд, ';
} elseif ($w < 10) {
    $status .= 'прохладно, ';
} elseif ($w < 22)  {
    $status .= 'тепло, ';
} elseif ($w < 30) {
    $status .= 'жарко, ';
} elseif ($w > 30) {
    $status .= 'ташкент, ';
} 
// Сравнение со вчерашним днем
$tNew = round((float) getGlobal('yw_mycity.temp'));
//$tOld = round((float) getHistoryAvg("tsrearyard.temp", strtotime("-1 day")) ('ow_fact.tempYesterday'));
$tOld = round((float) getHistoryAvg("yw_mycity.temp", strtotime("-1 day")));
$tDelta = abs($tNew - $tOld);
if ($tNew > $tOld) {
     //$status .= "теплее, чем вчера на " . chti($tDelta, 'градус', 'градуса', 'градусов') . ". ";
$status .= "теплее, чем вчера на " . $tDelta. ' градуса. ';     
} elseif ($tNew < $tOld) {
//     $status .= "холоднее, чем вчера на " . chti($tDelta, 'градус', 'градуса', 'градусов') . ". ";
     $status .= "холоднее, чем вчера на " . $tDelta . ' градуса. ';
} elseif ($tNew == $tOld) {
     $status .= "так же как и вчера. ";
}
$h = round(gg("yw_mycity.humidity"));
//$status .= "Относительная влажность " . chti($h, 'процент', 'процента', 'процентов') . ". ";
$status .= "Относительная влажность " . $h.' процентов.';	
$pressure = (float) gg("yw_mycity.pressure_mm");
if ($pressure < 738) {
    $status .= 'Атмосферное давление пониженное';
} elseif ($pressure > 768) {
    $status .='Атмосферное давление повышенное';
} else {
    $status .= 'Атмосферное давление в пределах нормы';
}
 //$status .= " (" . chti(round($pressure), 'миллиметр', 'миллиметра', 'миллиметров') . " ртутного столба). ";
$status .= " ". round($pressure). " (миллиметров ртутного столба). "; 
// ветер
$WindSpeed = (float) gg("yw_mycity.wind_speed");
if ($WindSpeed < 1) {
    $status .= "Ветра нет";
} elseif ($WindSpeed < 2) {
    $status .= "Легкий ветер, ";
} elseif ($WindSpeed < 5) {
    $status .= "Слабый ветер";
} elseif ($WindSpeed < 8) {
    $status .= "Умеренный ветер";
} elseif ($WindSpeed < 10) {
    $status .= "Свежий ветер";
} elseif ($WindSpeed < 14) {
    $status .= "Сильный ветер";
} elseif ($WindSpeed < 17) {
    $status .= "Очень сильный ветер";
} elseif ($WindSpeed < 21) {
    $status .= "Ветер очень-очень сильный";
} elseif ($WindSpeed < 28) {
    $status .= "Шторм";
} else {
    $status .= "Ураган";
}
if ($WindSpeed >= 1) {
//    $status .= " (" . chti(round($WindSpeed), 'метр', 'метра', 'метров') . " в секунду), ";
$status .= " ( " . round($WindSpeed) ." метра в секунду), ";
//    $windDirections = array('севера', 'северо-востока', 'востока', 'юго-востока', 'юга', 'юго-запада', 'запада', 'северо-запада', 'севера');
    
if (gg('yw_mycity.wind_dir')=='n') {$degree = 'севера';}
if (gg('yw_mycity.wind_dir')=='ne') {$degree = 'северо-востока';}     
if (gg('yw_mycity.wind_dir')=='e') {$degree = 'востока';}     
if (gg('yw_mycity.wind_dir')=='se') {$degree = 'юго-востока';}     
if (gg('yw_mycity.wind_dir')=='s') {$degree = 'юга';}     
if (gg('yw_mycity.wind_dir')=='sw') {$degree = 'юго-запада';}     
if (gg('yw_mycity.wind_dir')=='w') {$degree = 'запада';}     
if (gg('yw_mycity.wind_dir')=='nw') {$degree = 'северо-запада';}     
//     $WindDir = $windDirections[round($degree / 45)];
    $status .= " дующий с " . $degree;
}
$status .= ". ";
// Прогноз погоды на сегодня
if (timeBetween("01:00", "10:00")) {
    $status .= "Сегодня утром ожидается ";
    $w = round(gg("yw_mycity.forecast_0_morning_temp_avg"));

$condition=$this->getconditionrus(gg('yw_mycity.forecast_0_morningcondition'));				
} elseif (timeBetween("10:00", "14:00")) {
     $status .= "Сегодня днем ожидается ";
    $w = round(gg("yw_mycity.forecast_0_day_temp_avg"));

$condition=getconditionrusincl(gg('yw_mycity.forecast_0_daycondition'));			
    
} elseif (timeBetween("14:00", "20:00")) {
     $status .= "Сегодня вечером ожидается ";
    $w = round(gg("yw_mycity.forecast_0_evening_temp_avg"));

$condition=getconditionrusincl(gg('yw_mycity.forecast_0_eveningcondition'));				
    
} else {
     $status .= "Сегодня ночью ожидается ";
    $w = round(gg("yw_mycity.forecast_0_night_temp_avg")); 
$condition=getconditionrusincl(gg('yw_mycity.forecast_0_nightcondition'));	
}
//$status .= chti($w, 'градус', 'градуса', 'градусов') . " цельсия, " . gg("ow_day0.weather_type") . ". ";
$status .= $w ." градусов цельсия, " . $condition . ". ";
// Погода на завтра
$w = round(gg("yw_mycity.forecast_1_day_temp_avg"));
//$status .= 'Завтра ожидается ' . chti($w, 'градус', 'градуса', 'градусов') . " цельсия, ";
$status .= 'Завтра ожидается ' . $w. " градусов цельсия, ";

$condition=getconditionrusincl(gg('yw_mycity.forecast_1_daycondition'));
$status .= $condition . ".";	

$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='MSG_LEVEL'");
$msglevel=$cmd_rec['VALUE'];	
	
say($status,$msglevel);



