<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='yaweather_cities';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  if ($this->mode=='update') {
   $ok=1;
  
   global $id;
   $rec['ID']=$id;
   if (($rec['ID']=='') || (!is_numeric($rec['ID']))) {
    $out['ERR_ID']=1;
    $ok=0;
   }
	 
   global $country;
   $rec['country']=$country;
   if ($rec['country']=='') {
    $out['ERR_country']=1;
    $ok=0;
   }
  
   global $cityname;
   $rec['cityname']=$cityname;
   if ($rec['cityname']=='') {
    $out['ERR_cityname']=1;
    $ok=0;
   }

   global $part;
   $rec['part']=$part;
   if ($rec['part']=='') {
    $out['ERR_part']=1;
    $ok=0;
   }
   global $check;
   $rec['check']=$check;
   if ($rec['check']=='') {
    $out['ERR_check']=1;
    $ok=0;
   }



   
   //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }

  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
