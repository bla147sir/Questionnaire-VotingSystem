<?
	//各學院專任教師  B教師  C諮商與輔導中心研究人員
	$sql_meet_3="select dept_full_name,empl_chn_name,empl_no,substr(a.email,1,instr(a.email,'@',1,1)-1) email,crjb_depart from psfempl a,psfcrjb b,stfdept c where empl_no=crjb_empl_no and crjb_quit_date is null and substr(empl_no,1,1)='0' and crjb_seq='1' and   dept_no=substr(crjb_depart,1,2)||'0' minus select dept_full_name,empl_chn_name,empl_no,substr(a.email,1,instr(a.email,'@',1,1)-1) email,crjb_depart from psfempl a,psfcrjb b,stfdept c where empl_no=crjb_empl_no and crjb_quit_date is null and substr(empl_no,1,1)='0' and crjb_seq='1' and   dept_no=substr(crjb_depart,1,2)||'0' and substr(crjb_title,1,1) not in('B','C')  order by crjb_depart";
	
	$stmt_meet_3=ociparse($con,$sql_meet_3); 
	ociexecute($stmt_meet_3,OCI_DEFAULT); 
	  
	$nrows_meet_3= OCIFetchStatement($stmt_meet_3,$results_meet_3);
	
	for($i=0;$i<$nrows_meet_3;$i++)
	{
	$c[$i]=$results_meet_3['EMPL_CHN_NAME'][$i] ;
	$e[$i]=$results_meet_3['EMPL_NO'][$i] ;
	$dep[$i]=$results_meet_3['CRJB_DEPART'][$i] ;
	$str1="$dep[$i]"."a"."$e[$i]";
	$c[$i]=iconv("BIG5","UTF-8", $c[$i]);
	
	echo "document.all.list1.options.add(new Option('".$c[$i]."','".$str1."'));";
	}

?>