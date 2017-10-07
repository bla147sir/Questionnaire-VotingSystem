<?php  
include("check.php");
include("connect.php");
date_default_timezone_set('Asia/Taipei');

//$id=$_POST['vid'];  // 取得網址後面的ID #  

$id=$_SESSION['vid'];

$sql_vote="select * from vote where ID='$id' ";  //取出該筆投票    
 $stmt_vote=ociparse($con,$sql_vote);     //取出資料固定格式
 ociexecute($stmt_vote,OCI_DEFAULT); 
 $nrows_vote = OCIFetchStatement($stmt_vote,$results_vote); //算取出來的有幾筆

if($nrows_vote<=0)
{
	echo "0,無此投票活動！"; 
	exit;
}

//是否為管理者
$sql_member="select * from MEMBER where IDENTITY='".$_SESSION['Username']."' and status='1'";       
$stmt_member=ociparse($con,$sql_member); 
ociexecute($stmt_member,OCI_DEFAULT);   
$nrows_member = OCIFetchStatement($stmt_member,$results_member);
if ($nrows_member>0) $status=1;

//*****************************************
//檢查是否為開放填答期間
//*****************************************
$date=date("YmdHis");
if($date<$results_vote['DATETIME'][0])
{
	echo "0,此投票活動尚未開放！"; 	
	exit;
}
if($date>$results_vote['DUE'][0])
{	
	echo "0,此投票活動已截止！";
	exit;
}
//*******************************************
//權限檢查
//*******************************************
$arr_identity= array("[開放]","教師","職員","學生","[限定]");
$p = $results_vote['PARTICIPANT'][0] ; //01110    [開放]/教師/職員/學生/[限定]
for($i=0;$i<5;$i++)
	if (substr($p,$i,1)=="1") $partcipant .= " " . $arr_identity[$i];

$user_type=$_SESSION['user_type'] ;
if (!substr($p,$user_type,1)=="1" && !$status==1 && !substr($p,0,1)=="1") 
{
	echo "0,此投票活動僅開放【 $partcipant 】填寫"; 
	exit;
}

//是否為可填寫者
$sql_elector_check="select * from ELECTOR where ID=$id";       //檢查是否有勾選   (elector 有無值)
 $stmt_elector_check=ociparse($con,$sql_elector_check); 
 ociexecute($stmt_elector_check,OCI_DEFAULT);   
 $nrows_elector_check = OCIFetchStatement($stmt_elector_check,$results_elector_check);

$sql_elector="select * from ELECTOR where IDENTITY='".$_SESSION['Username']."' and ID=$id";       
 $stmt_elector=ociparse($con,$sql_elector); 
 ociexecute($stmt_elector,OCI_DEFAULT);   
 $nrows_elector = OCIFetchStatement($stmt_elector,$results_elector);
if ($nrows_elector_check!=0 && $nrows_elector==0 && !$status==1)
{
	echo "<script language = JavaScript>";
	echo "alert(\"您不是本活動開放對象!\");"; 
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;
}
//********************************************
//檢查是否已完成填答
//********************************************
$sqlid="select * from election_info where ID='$id' and identity='".$_SESSION['Username']."'";
$stmtid=ociparse($con,$sqlid);     
ociexecute($stmtid,OCI_DEFAULT); 
$nrowsid = OCIFetchStatement($stmtid,$resultsid);
if ($nrowsid>0  && !$status==1) 
{
	echo "0,您已投過票，謝謝您的參與！"; 
	exit;
}
//檢查結束
//***************************************************************************
//取得填答者的IP---------------------------------------------------
if(!empty($_SERVER['HTTP_CLIENT_IP']))
   $cip = $_SERVER['HTTP_CLIENT_IP'];
else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
   $cip = $_SERVER['HTTP_X_FORWARDED_FOR'];
else
   $cip= $_SERVER['REMOTE_ADDR'];

//取填寫時間---------------------------------------
date_default_timezone_set('Asia/Taipei');
$date=date("YmdHis");

//取填答者身分-----------------------------------------------------------
$identity=$_SESSION['Username'];

//取已勾選選項
$type=$results_vote['TYPE'][0];
if($type=="1")
{
	$sql_elected="select * from candidate where ID='$id'";
	$stmt_elected=ociparse($con,$sql_elected);     
	ociexecute($stmt_elected,OCI_DEFAULT); 
	$nrows_elected= OCIFetchStatement($stmt_elected,$results_elected);
}
else
{
	$sql_elected="select * from masterpiece where ID='$id'";
	$stmt_elected=ociparse($con,$sql_elected);     
	ociexecute($stmt_elected,OCI_DEFAULT); 
	$nrows_elected= OCIFetchStatement($stmt_elected,$results_elected);
}

$most = $results_vote['MOST'][0];

//add by boblee!! encode using MD5
if ($results_vote[ENCRYPT][0]==1) $user_encode = md5($identity); //改為md5加密
else $user_encode = $identity ;

if($most=="1")
{
	$choose=$_POST['radio'];
	if($choose!="")
	{		 
		 $sql_choose="insert into vote_chose values('$id','$user_encode','$choose')";
		 $sql_choose= iconv("UTF-8","BIG5",$sql_choose);
		 $stmt_choose= OCI_PARSE($con,$sql_choose);
		 if(!OCI_EXECUTE($stmt_choose)) //參考OCI_DEFAULT參數說明
		 {
			$oci_err=OCIError($stmt_choose);
			echo "1,投票活動資料寫入失敗，訊息：" . $oci_err[message] ;
			mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
			ocirollback($con);   
			exit();
		 }
	}//if(choose!="")
}//for($nrows_elected=1)
else
{
	for($i=0;$i<$nrows_elected;$i++)
	{
		$choose[$i]=$_POST['checkbox'][$i];
		if($choose[$i]!="")
		{
			 $sql_choose="insert into vote_chose values('$id','$user_encode','$choose[$i]')";
			 $sql_choose= iconv("UTF-8","BIG5",$sql_choose);
			 $stmt_choose= OCI_PARSE($con,$sql_choose);
			 if(!OCI_EXECUTE($stmt_choose)) //參考OCI_DEFAULT參數說明
			 {
				$oci_err=OCIError($stmt_choose);
				echo "1,投票活動資料寫入失敗，訊息：" . $oci_err[message] ;
				mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
				ocirollback($con);   
				exit();
			 }
		}//if(choose!="")

	}//for(i)
}//else



 $sqlid="insert into election_info values('$identity','$id','$date','$cip')";
 $sqlid= iconv("UTF-8","BIG5",$sqlid);
 $stmtid= OCI_PARSE($con,$sqlid);
 if(!OCI_EXECUTE($stmtid)) //參考OCI_DEFAULT參數說明
 {
	$oci_err=OCIError($stmtid);
	echo "1,投票活動資料寫入失敗，訊息：" . $oci_err[message] ;
	mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sqlid . $oci_err[message] , $headers);
	ocirollback($con);   
	exit();
 }

//檢查沒問題才存進DB
	ocicommit($con);   
	
	echo "0,投票完成，謝謝您的參與！"; 
	


?>