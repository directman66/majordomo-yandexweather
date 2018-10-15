<?php	
function getconditionrusincl($conditioneng){
$condition=$conditioneng;
if ($conditioneng=="overcast") {$condition="пасмурно";}
if ($conditioneng=="clear") {$condition="ясно";}	
if ($conditioneng=="cloudy-and-light-rain") {$condition="пасмурно и небольшой дождь";}
if ($conditioneng=="cloudy-and-rain") {$condition="пасмурно и  дождь";}
if ($conditioneng=="cloudy") {$condition="облачно";}
if ($conditioneng=="overcast-and-light-rain") {$condition="моросящий дождь";}
if ($conditioneng=="overcast-and-light-snow") {$condition="небольшой снег";}
if ($conditioneng=="partly-cloudy-and-light-rain") {$condition="переменная облачность и небольшой дождь";}
if ($conditioneng=="partly-cloudy-and-light-snow") {$condition="переменная облачность и небольшой снег";}
if ($conditioneng=="partly-cloudy-and-rain") {$condition="переменная облачность с дождем";}
if ($conditioneng=="partly-cloudy-and-snow") {$condition="переменная облачность со снегом";}
if ($conditioneng=="partly-cloudy") {$condition="переменная облачность";}
if ($conditioneng=="overcast-and-rain") {$condition="ливень";}	
if ($conditioneng=="overcast-thunderstorms-with-rain") {$condition="гроза";}	
if ($conditioneng=="cloudy-and-snow") {$condition="Облачно с прояснениями";}	
	
	
	
//sg("yw_mycity.lastcondition",$conditioneng) ;
//sg("yw_mycity.lastconditionrus",$condition) ; 
return $condition;
}
