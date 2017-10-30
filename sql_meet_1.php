<?
	//考績/甄審委員名單
	$sql_meet_1="select dept_full_name,empl_chn_name,empl_no,substr(a.email,1,instr(a.email,'@',1,1)-1) email,crjb_depart from  psfempl a,psfcrjb b,stfdept c where  empl_no=crjb_empl_no and crjb_seq='1' and crjb_quit_date is null and  substr(crjb_depart,1,2) not in('M8','M9') and  substr(crjb_title,1,1) in ('A','F','E')	and   dept_no=substr(crjb_depart,1,2)||'0' order by substr(crjb_depart,1,2),empl_chn_name   ";
	
	$stmt_meet_1=ociparse($con,$sql_meet_1); 
	ociexecute($stmt_meet_1,OCI_DEFAULT); 
	  
	$nrows_meet_1= OCIFetchStatement($stmt_meet_1,$results_meet_1);
	
	for($i=0;$i<$nrows_meet_1;$i++)
	{
	$c[$i]=$results_meet_1['EMPL_CHN_NAME'][$i] ;
	$e[$i]=$results_meet_1['EMPL_NO'][$i] ;
	$dep[$i]=$results_meet_1['CRJB_DEPART'][$i] ;
	$str1="$dep[$i]"."a"."$e[$i]";

	$c[$i]=iconv("BIG5","UTF-8", $c[$i]);
	
	echo "document.all.list1.options.add(new Option('".$c[$i]."','".$str1."'));";
	}

?>