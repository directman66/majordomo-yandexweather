<?php	
include_once(DIR_MODULES . "yandexweather/yandexweather.class.php");
$yw= new yandexweather();
$yw->getConfig();
$ee=$yw->config["ENABLE_EVENTS"];
if ($ee=="1"){

if (($this->object_title=="yw_mycity") and ($this->getProperty("condition")<>"")){
$lastcondition=gg("yw_mycity.lastcondition");
$conditioneng=gg("yw_mycity.condition");
$condition1eng=gg("yw_mycity.forecast_0_daycondition");  
$condition2eng=gg("yw_mycity.forecast_1_daycondition ");  
$condition3eng=gg("yw_mycity.forecast_2_daycondition ");    

  if ($lastcondition<>$conditioneng){
$condition=conditionrus($conditioneng);
$condition1=conditionrus($condition1eng);    
$condition2=conditionrus($condition2eng);        
$condition3=conditionrus($condition3eng);        
sg("yw_mycity.lastcondition",$conditioneng);
sg("yw_mycity.lastconditionrus",$condition); 
    
sg("yw_mycity.lastcondition1rus",$condition1) ;     
sg("yw_mycity.lastcondition2rus",$condition2) ;     
sg("yw_mycity.lastcondition3rus",$condition3) ;         

    
    say(" На улице ".$condition,2);
}
}
}
