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
//select  nameid,titlename,descr,max(typename) tip, max(alias) alias, max(device_id) device_id, max(mayak_temp) mayak_temp,
//max(status) status, max(arm) arm
//from (
//select nameid,titlename,descr
//,if (tip='typename', VALUE,null) typename
//,if (tip='alias', VALUE,null) alias
//,if (tip='device_id', VALUE,null) device_id
//,if (tip='mayak_temp', VALUE,null) mayak_temp    
//,if (tip='status', VALUE,null) status        
//,if (tip='arm', VALUE,null) arm        
 //from   (
//SELECT objects.ID nameid, objects.TITLE titlename, objects.DESCRIPTION descr, properties.TITLE tip, pvalues.VALUE
//FROM `objects`, `properties`, `pvalues`
 //WHERE objects.ID=properties.OBJECT_ID and objects.class_id = (SELECT ID FROM `classes` WHERE title='starline-online') 
//and properties.ID=pvalues.PROPERTY_ID
//    ) a
//    )b 
//    group by  nameid,titlename,descr

  $res=SQLSelect("SELECT `TITLE`,`DESCRIPTION` FROM `objects` WHERE `CLASS_ID` in(SELECT ID  FROM `classes` WHERE title='starline-online')");
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
