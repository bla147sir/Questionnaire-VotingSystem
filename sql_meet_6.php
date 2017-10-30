<?
	//校務會議職員代表(候選人要再手動排除人事及主計室主任，選舉人不需排除)
	$sql_meet_6="select dept_full_name,empl_chn_name,empl_no,substr(a.email,1,instr(a.email,'@',1,1)-1) email,crjb_depart from  psfempl a,psfcrjb b,stfdept c where  empl_no=crjb_empl_no and crjb_seq='1' and crjb_quit_date is null and  substr(crjb_title,1,1) in ('A','F','E')	  and   dept_no=substr(crjb_depart,1,2)||'0'    order by substr(crjb_depart,1,2),empl_chn_name  ";
	
	$stmt_meet_6=ociparse($con,$sql_meet_6); 
	ociexecute($stmt_meet_6,OCI_DEFAULT); 
	  
	$nrows_meet_6 = OCIFetchStatement($stmt_meet_6,$results_meet_6);
	
	for($i=0;$i<$nrows_meet_6;$i++)
	{
	$c[$i]=$results_meet_6['EMPL_CHN_NAME'][$i] ;
	$e[$i]=$results_meet_6['EMPL_NO'][$i] ;
	$dep[$i]=$results_meet_6['CRJB_DEPART'][$i] ;
	$str1="$dep[$i]"."a"."$e[$i]";
	$c[$i]=iconv("BIG5","UTF-8", $c[$i]);
	
	echo "document.all.list1.options.add(new Option('".$c[$i]."','".$str1."'));";
	}

?>