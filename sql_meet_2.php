<?
	//編制內職員(不含兼行政職教師、行政助理及專案助理)
	$sql_meet_2="select crjb_depart,empl_chn_name,crjb_officials,empl_no from psfempl,psfcrjb where empl_no=crjb_empl_no and crjb_quit_date is null and substr(empl_no,1,1)='0' and crjb_seq='1' minus select crjb_depart,empl_chn_name,crjb_officials,empl_no from psfempl,psfcrjb where empl_no=crjb_empl_no and crjb_quit_date is null and substr(empl_no,1,1)='0' and crjb_seq='1' and substr(crjb_title,1,1)='B' order by crjb_depart,empl_no";
	
	$stmt_meet_2=ociparse($con,$sql_meet_2); 
	ociexecute($stmt_meet_2,OCI_DEFAULT); 
	  
	$nrows_meet_2= OCIFetchStatement($stmt_meet_2,$results_meet_2);
	
	for($i=0;$i<$nrows_meet_2;$i++)
	{
	$c[$i]=$results_meet_2['EMPL_CHN_NAME'][$i] ;
	$e[$i]=$results_meet_2['EMPL_NO'][$i] ;
	$dep[$i]=$results_meet_2['CRJB_DEPART'][$i] ;
	$str1="$dep[$i]"."a"."$e[$i]";
	$c[$i]=iconv("BIG5","UTF-8", $c[$i]);
	
	echo "document.all.list1.options.add(new Option('".$c[$i]."','".$str1."'));";
	}

?>