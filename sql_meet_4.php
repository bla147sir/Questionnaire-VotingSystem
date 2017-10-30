<?
	//國立彰化師範大學性別平等教育委員會《職工代表》網路投票
	//(含職員、助教(B60)，約用行政助理) , 不含研究人員(Cxx)、教官(Dxx)及學務處校醫(8xxxxxx)，)
	$sql_meet_4="select dept_full_name,empl_chn_name,empl_no,substr(a.email,1,instr(a.email,'@',1,1)-1) email,crjb_depart from psfempl a,psfcrjb b,stfdept c where empl_no=crjb_empl_no and crjb_quit_date is null and substr(empl_no,1,1) in('0','7','8') and crjb_seq='1'  and   dept_no=substr(crjb_depart,1,2)||'0'  and not (substr(crjb_depart,1,2)='M3' and  substr(empl_no,1,1)='8') minus select dept_full_name,empl_chn_name,empl_no,substr(a.email,1,instr(a.email,'@',1,1)-1) email,crjb_depart from psfempl a,psfcrjb b,stfdept c where  empl_no=crjb_empl_no and crjb_quit_date is null and substr(empl_no,1,1)='0' and crjb_seq='1'   and   dept_no=substr(crjb_depart,1,2)||'0' and substr(crjb_title,1,1) in ('B','C','D') and crjb_title <>'B60' order by crjb_depart,empl_no";
	
	$stmt_meet_4=ociparse($con,$sql_meet_4); 
	ociexecute($stmt_meet_4,OCI_DEFAULT); 
	  
	$nrows_meet_4 = OCIFetchStatement($stmt_meet_4,$results_meet_4);
	
	for($i=0;$i<$nrows_meet_4;$i++)
	{
	$c[$i]=$results_meet_4['EMPL_CHN_NAME'][$i] ;
	$e[$i]=$results_meet_4['EMPL_NO'][$i] ;
	$dep[$i]=$results_meet_4['CRJB_DEPART'][$i] ;
	$str1="$dep[$i]"."a"."$e[$i]";
	$c[$i]=iconv("BIG5","UTF-8", $c[$i]);
	
	echo "document.all.list1.options.add(new Option('".$c[$i]."','".$str1."'));";
	}

?>