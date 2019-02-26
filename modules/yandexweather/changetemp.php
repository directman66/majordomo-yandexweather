<?php	
$par="yw_mycity.temp";
$curt=gg($par);
$period="-5 hour";
$period3="-3 hour";
$prevt=getHistoryAvg($par, strtotime($period));
//echo $prevt.":".$curt ;
if ($prevt>$curt) { sg("yw_mycity.trandtemp","down");sg("yw_mycity.trandtempfa","fa-arrow-circle-down");}
else if ($prevt=$curt) { sg("yw_mycity.trandtemp","=");sg("yw_mycity.trandtempfa","fa-pause-circle");}
else if ($prevt<$curt) { sg("yw_mycity.trandtemp","up");sg("yw_mycity.trandtempfa","fa-arrow-circle-up");}
sg("yw_mycity.trandtemp-3",getHistoryAvg($par, strtotime($period3)) );
$par="yw_mycity.pressure_mm";
$curt=gg($par);
$prevt=getHistoryAvg($par, strtotime($period));
//echo $prevt.":".$curt ;
if ($prevt>$curt) { sg("yw_mycity.trandpres","down");sg("yw_mycity.trandpresfa","fa-arrow-circle-down");}
else if ($prevt=$curt) { sg("yw_mycity.trandpres","=");sg("yw_mycity.trandpresfa","fa-pause-circle");}
else if ($prevt<$curt) { sg("yw_mycity.trandpres","up");sg("yw_mycity.trandpresfa","fa-arrow-circle-up");}
$par="yw_mycity.humidity";
$curt=gg($par);
$prevt=getHistoryAvg($par, strtotime($period));
//echo $prevt.":".$curt ;
if ($prevt>$curt) { sg("yw_mycity.trandhum","down");sg("yw_mycity.trandhumfa","fa-arrow-circle-down");}
else if ($prevt=$curt) { sg("yw_mycity.trandhum","=");sg("yw_mycity.trandhumfa","fa-pause-circle");}
else if ($prevt<$curt) { sg("yw_mycity.trandhum","up");sg("yw_mycity.trandhumfa","fa-arrow-circle-up");}
$par="yw_mycity.wind_speed";
$curt=gg($par);
$prevt=getHistoryAvg($par, strtotime($period));
//echo $prevt.":".$curt ;
if ($prevt>$curt) { sg("yw_mycity.trandwind_speed","down");}
else if ($prevt=$curt) { sg("yw_mycity.trandwind_speed","=");}
else if ($prevt<$curt) { sg("yw_mycity.trandwind_speed","up");}
