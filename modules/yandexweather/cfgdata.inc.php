<?php	
//sg('test.yaload',2);

$table_name='yaweather_cities';

$res=SQLSelect("SELECT * FROM `yaweather_cities` ");  

	if ($res[0]['cityname']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
    $tmp=explode(' ', $res[$i]['UPDATED']);
    $res[$i]['UPDATED']=fromDBDate($tmp[0])." ".$tmp[1];
   }
//   $out['RESULT']=$res;
   $out['CITYES']=$res;

  }

