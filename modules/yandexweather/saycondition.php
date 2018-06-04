include_once(DIR_MODULES . "yandexweather/yandexweather.class.php");
$yw= new yandexweather();
$yw->getConfig();
$ee=$yw->config["ENABLE_EVENTS"];
if ($ee=="1"){

if (($this->object_title=="yw_mycity") and ($this->getProperty("condition")<>"")){
//if ($this->object_title=="yw_mycity") {
$lastcondition=gg("yw_mycity.lastcondition");
$conditioneng=gg("yw_mycity.condition");
if ($lastcondition<>$conditioneng){
$condition=$conditioneng; 
if ($conditioneng=="overcast") {$condition="ясно";}
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
sg("yw_mycity.lastcondition",$conditioneng) ;
sg("yw_mycity.lastconditionrus",$condition) ; 
say(" На улице ".$condition,2);}}
}
