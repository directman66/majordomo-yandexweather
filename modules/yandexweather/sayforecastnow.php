<?php
$text='Сегодня хорошая погода';
$return_full="";
$status="";

$condition=$this->getconditionrus(gg('yw_mycity.condition'));				
	
$status.="Сейчас ".$condition.".";
$return_full.=$status." ";
    
$status="";
$w=round(gg("yw_mycity.temp"));
$tempw=$w;
if($tempw >= 11 and $tempw <= 14) {
  $tempcels="градусов";
} else {
  while ($tempw > 9) {
    $tempw=$tempw-10;
  }
    
  if($tempw == 0 or $tempw >= 5 and $tempw <= 9) { $tempcels= 'градусов' ; }
  if($tempw == 1) { $tempcels= 'градус' ; }
  if($tempw >= 2 and $tempw <= 4) { $tempcels= 'градуса' ; }
}
$tNew = abs((float)gg('yw_mycity.temp'));
$status.='По данным метеослужб температура воздуха '.gg('yw_mycity.temp')." ".$tempcels." цельсия. ";
//Датчики на балконе показывают " . chti(round(gg("zaoknom")), 'градус', 'градуса', 'градусов')  . " цельсия." ;
$return_full.=$status." ";
$tempw="";
$tempcels="";
    
$status="";  
$h=round(gg("yw_mycity.humidity"));
$tempw=$h;
if($tempw >= 11 and $tempw <= 14){
  $tempcels="процентов";
} else {
  while ($tempw > 9){
    $tempw=$tempw-10;
  }
  if($tempw == 0 or $tempw >= 5 and $tempw <= 9) { $tempcels= 'процентов' ; }
  if($tempw == 1) { $tempcels= 'процент' ; }
  if($tempw >= 2 and $tempw <= 4) { $tempcels= 'процента' ; }
}
$status.=" Относительная влажность ".gg("yw_mycity.humidity")." ".$tempcels. ".";
$return_full.=$status." ";
$tempw="";
$tempcels="";
    
$status="";
$pressure=round(gg("yw_mycity.pressure_mm"));
if ($pressure<728) {
  $status.=' Атмосферное давление пониженное';
} elseif ($pressure>768) {
  $status.=' Атмосферное давление повышенное.';
} else {
  $status.=' Атмосферное давление нормальное.';
}
$return_full.=$status." ";
    
$status="";
//ветер
$WindSpeed=(float)gg("yw_mycity.wind_speed");
if ($WindSpeed<1) {
  $status.='Ветра нет.';
} elseif ($WindSpeed<4) {
  $status.='Ветер слабый.';
} elseif ($WindSpeed<6) {
  $status.='Ветер сильный.';
} elseif ($WindSpeed<9) {
  $status.='Ветер очень сильный.';
} else {
  $status.='Ветер очень! Очень сильный.';
}
$return_full.=$status." ".round(gg("yw_mycity.wind_speed"))." метра в секунду. ";
	
$cmd_rec = SQLSelectOne("SELECT VALUE FROM yaweather_config where parametr='MSG_LEVEL'");
$msglevel=$cmd_rec['VALUE'];	

sg('yw_forecast',$status);
sg('yw_mycyty.forecasttext',$status);
	
//say($return_full,$msglevel);
