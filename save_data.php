<?
session_start(); 

include("connect.php");
$str=$_GET['value'];

$str_array=explode(',',$str);
//foreach($str_array as $index => $value)
$num=count($str_array);   // 人員數量

$n=1;
$id=$_SESSION["get_id"];

//-----------------------------------------------------   修改人員名單
if($_SESSION["count_elector"]!=0)
{

$sql="delete from ELECTOR where ID='$id' " ;

	  $stmt = OCIPARSE($con,$sql);

		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);
}


if($_SESSION["count_candidate"]!=0)
{

$sql3="delete from CANDIDATE where ID='$id' " ;

	  $stmt3 = OCIPARSE($con,$sql3);

		if(!OCIEXECUTE($stmt3,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);
}



//-----------------------------------------------------

$sql9="select * from vote where ID='$id' ";                //查 $done

$stmt9=ociparse($con,$sql9); 
ociexecute($stmt9,OCI_DEFAULT); 
	  
$nrows9 = OCIFetchStatement($stmt9,$results9);

$done=$results9["DONE"][0];


for($i=0;$i<$num;$i++)
{
$str_array1=explode('a',$str_array[$i]);
$dep=$str_array1[0];
$pno=$str_array1[1];


	$sql="select distinct(EMPL_CHN_NAME),DEPT_FULL_NAME,a.email from psfempl a,psfcrjb b,stfdept c where EMPL_NO ='$pno' and EMPL_NO=CRJB_EMPL_NO and DEPT_NO='$dep' ";

	$stmt=ociparse($con,$sql); 
	ociexecute($stmt,OCI_DEFAULT); 
	  
	$nrows = OCIFetchStatement($stmt,$results);

	for($j=0;$j<$nrows;$j++)
	{

	  $chn_name[$j]=$results['EMPL_CHN_NAME'][$j];
	  $dept_name[$j]=$results['DEPT_FULL_NAME'][$j];

	  $em=$results['EMAIL'][$j];   // account

	  $email_array=explode('@',$em);

	  $email=$email_array[0];

		
	  if($done!=1 && $done!=2)
	{
	  $sql2="insert into ELECTOR (ID,NAME,DEPARTMENT,P_NO,IDENTITY) values('$id','$chn_name[$j]','$dept_name[$j]','$pno','$email')";

	  $stmt2 = OCIPARSE($con,$sql2);

		if(!OCIEXECUTE($stmt2,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);

		echo "<script type='text/javascript'>";
		echo "window.close();";
		echo "</script>";
	}
	else if ($done==1 || $done==2)
	{
	  
	  $sql2="insert into CANDIDATE (ID,NO,NAME,DEPARTMENT,P_NO) values('$id','$n','$chn_name[$j]','$dept_name[$j]','$pno')";

	  $stmt2 = OCIPARSE($con,$sql2);

		if(!OCIEXECUTE($stmt2,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);

		$n=$n+1;


	}

	$_SESSION["count_candidate"]==0;

	echo "<script type='text/javascript'>";
	echo "window.close();";
	echo "</script>";

}


}
?>


