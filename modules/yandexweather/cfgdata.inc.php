<?php	
sg('test.yaload',2);
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

if ($this->mode=='update') {
   $ok=1;
sg('test.yammode',$this->mode);

   global $ID;
   $rec['ID']=$ID;
//   if (($rec['ID']=='') || (!is_numeric($rec['ID']))) {
//   if ($rec['ID']=='')  {
//    $out['ERR_ID']=1;
//    $ok=0;
//   }
	 
   global $country;
   $rec['country']=$country;
//   if ($rec['country']=='') {
//    $out['ERR_country']=1;
//    $ok=0;
//   }
  
   global $cityname;
  $rec['cityname']=$cityname;
//   if ($rec['cityname']=='') {
//    $out['ERR_cityname']=1;
//    $ok=0;
//   }

   global $part;
   $rec['part']=$part;
///   if ($rec['part']=='') {
//    $out['ERR_part']=1;
//    $ok=0;
//   }
   global $check;
   $rec['check']=$check;

   global $latlon;
   $rec['latlon']=$latlon;

//   if ($rec['check']=='') {
//    $out['ERR_check']=1;
//    $ok=0;
//   }
sg('test.yaok',$ok);
   //UPDATING RECORD
   if ($ok) {
//    if ($rec['ID']) {
//     SQLUpdate($table_name, $rec); // update
  //  } else 
{
     $new_rec=1;
//     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
     SQLInsert($table_name, $rec); // adding new record
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
