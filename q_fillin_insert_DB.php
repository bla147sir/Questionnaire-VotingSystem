<?php  
include("check.php");
include("connect.php");
date_default_timezone_set('Asia/Taipei');

$id=$_POST['qid'];   

$sql="select * from questionnaire where ID='$id' ";    
$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT); 
$nrows = OCIFetchStatement($stmt,$results); 
if($nrows<=0)
{
	echo "<script language = JavaScript>";
	echo "alert(\"無此問卷！\");"; 
	echo "window.location.href='index.php'";
	echo "</script>"; 
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
if($date<$results['DATETIME'][0])
{
	echo "<script language = JavaScript>";
	echo "alert(\"此問卷尚未開放！\");"; 
	echo "window.location.href='index.php'";
	echo "</script>"; 
	exit;
}
if($date>$results['DUE'][0])
{	
	echo "<script language = JavaScript>";
	echo "alert(\"此問卷已截止！\");";
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;
}
//*******************************************
//權限檢查
//*******************************************
$arr_identity= array("[開放]","教師","職員","學生","[限定]");
$p = $results['PARTICIPANT'][0] ; //01110    [開放]/教師/職員/學生/[限定]
for($i=0;$i<5;$i++)
	if (substr($p,$i,1)=="1") $partcipant .= " " . $arr_identity[$i];

$user_type=$_SESSION['user_type'] ;
if (!substr($p,$user_type,1)=="1" && !$status==1 && !substr($p,0,1)=="1") 
{
	echo "<script language = JavaScript>";
	echo "alert(\"此問卷活動僅開放【 $partcipant 】填寫\");"; 
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;

}
//********************************************
//檢查是否已完成填答
//********************************************
$username=$_SESSION['username'];
$sql_id="select * from identity where ID='$id' and IDENTITY='$username' " ;     //已投過票的紀錄
 $stmt_id=ociparse($con,$sql_id);     
 ociexecute($stmt_id,OCI_DEFAULT); 
 $nrows_id = OCIFetchStatement($stmt_id,$results_id);
if ($nrows_id>0  && !$status==1) 
{
	echo "<script language = JavaScript>";
	echo "alert(\"您已填過此問卷，謝謝您的參與！\");"; 
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;
}
//檢查結束
//***************************************************************************
//用於後面取出各部分問題的sql
$sql1="select * from questionnaire where ID='$id' ";      
 $stmt1=ociparse($con,$sql1);     //取出資料固定格式
 ociexecute($stmt1,OCI_DEFAULT); 
 $nrows1 = OCIFetchStatement($stmt1,$results1); //算取出來的有幾筆

$sqlt="select * from question where ID='$id' ";    //取出全部問題
 $stmtt=ociparse($con,$sqlt);     
 ociexecute($stmtt,OCI_DEFAULT); 
 $nrowst = OCIFetchStatement($stmtt,$resultst); 

$sqlpart="select distinct part from question where ID='$id' ";    //取出全部部分
 $stmtpart=ociparse($con,$sqlpart);     
 ociexecute($stmtpart,OCI_DEFAULT); 
 $nrowspart = OCIFetchStatement($stmtpart,$resultspart); 


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

//add by boblee!! encode using MD5
if ($results1[ENCRYPT][0]==1) $user_encode = md5($identity); //改為md5加密
else $user_encode = $identity ;


	
	$i2=0;
	//取答案存答案--------------------------------------------------------------------------------------
	for($i=0;$i<$nrowst;$i++)
	{
		$part = $resultst['PART'][$i];
        $no = $resultst['NO'][$i];
		$non = $resultst['NON'][$i];
		$type = $resultst['TYPE'][$i];
		$other = $resultst['OTHER'][$i];
		
		if($type==1)
			$ans[$i]=$_POST['textfield'][$part][$no];    //將值存成陣列
			    
		if($type==2)
			$ans[$i]=$_POST['textarea'][$part][$no];    //將值存成陣列	
		
		if($type==3)
			$ans[$i]=$_POST['select'][$part][$no];

		if($type==4){
				$sqlnu="select NUM from opt where NO='$no' and ID='$id' and PART='$part'";
				$stmtnu=ociparse($con,$sqlnu);
				ociexecute($stmtnu,OCI_DEFAULT);
				$nrowsnu = OCIFetchStatement($stmtnu,$resultsnu); 

				for($j=0;$j<$nrowsnu;$j++){
					$num = $resultsnu['NUM'][$j];
					$ansch[$j]=$_POST['checkbox'][$part][$no][$num];
					$ans_ctext[$j]=$_POST['textfield'][$part][$no];
				
					if($ansch[$j] && $j!=$nrowsnu-1)
					{
						$sqlach[$j]= "insert into ANS_OPT values('$user_encode','$id',$part,$no,$non,'$ansch[$j]')";   
						$sqlach[$j] = iconv("UTF-8","BIG5",$sqlach[$j]);
						$stmtach = OCIPARSE($con,$sqlach[$j]);
						if(!OCIEXECUTE($stmtach,OCI_DEFAULT)) //參考OCI_DEFAULT參數說明
						{
							$oci_err=OCIError($stmtach);
							echo "1,問卷資料寫入失敗，訊息：" . $oci_err[message] ;
							mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sqlach[$j]. $oci_err[message], $headers);
							ocirollback($con); 
							exit();
						}
					}//if(ansch)

					//存其他文字
					if($other==1 && $ans_ctext[$j] && $j==$nrowsnu-1)   
					{				
						$num=$num+1;
						$sqlach[$j]= "insert into ANS_OPT values('$user_encode','$id',$part,$no,$non,'$num')";   
						$sqlach[$j] = iconv("UTF-8","BIG5",$sqlach[$j]);
						$stmtach = OCIPARSE($con,$sqlach[$j]);
						if(!OCIEXECUTE($stmtach,OCI_DEFAULT)) //參考OCI_DEFAULT參數說明
						{
							$oci_err=OCIError($stmtach);
							echo "1,問卷資料寫入失敗，訊息：" . $oci_err[message] ;
							mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sqlach[$j]. $oci_err[message], $headers);
							ocirollback($con); 
							exit();
						}

						$ans_ctext[$j]=strip_tags($ans_ctext[$j]);           //防止tags
						$ans_ctext[$j]=htmlspecialchars($ans_ctext[$j],ENT_QUOTES);
						$sql_ctext[$j]= "insert into TEXT values('$user_encode','$id',$part,$no,$non,'$ans_ctext[$j]')"; 
						$sql_ctext[$j] = iconv("UTF-8","BIG5",$sql_ctext[$j]);
						$stm_ctext = OCIPARSE($con,$sql_ctext[$j]);
						if(!OCIEXECUTE($stm_ctext,OCI_DEFAULT)) //參考OCI_DEFAULT參數說明
						{
							$oci_err=OCIError($stm_ctext);
							echo "1,問卷資料寫入失敗，訊息：" . $oci_err[message] ;
							mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_ctext[$j]. $oci_err[message], $headers);
							ocirollback($con); 
							exit();
						}
					}//if(ans)
				}//for($j)
		}//if(4)

		if($type==5){
			$ans[$i]=$_POST['radio'][$part][$no];
			if($other==1){
				$ans_rtext[$i]=$_POST['textfield'][$part][$no];
				if($ans_rtext[$i])   //存其他文字
				{
					$ans_rtext[$i]=strip_tags($ans_rtext[$i]);           //防止tags
					$ans_rtext[$i]=htmlspecialchars("$ans_rtext[$i]",ENT_QUOTES);
					$sql_rtext[$i]= "insert into TEXT values('$user_encode','$id',$part,$no,$non,'$ans_rtext[$i]')"; 
					$sql_rtext[$i] =  iconv("UTF-8","BIG5",$sql_rtext[$i]);					
					$stm_rtext = OCIPARSE($con,$sql_rtext[$i]);
					if(!OCIEXECUTE($stm_rtext,OCI_DEFAULT)) //參考OCI_DEFAULT參數說明
					{
						$oci_err=OCIError($stm_rtext);
						echo "1,問卷資料寫入失敗，訊息：" . $oci_err[message] ;
						mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_rtext[$i]. $oci_err[message], $headers);
						ocirollback($con);  
						exit();
					}
				}//if(ans)
			}//if(other=1)
				
		}//if(type=5)

		if($type==6){
			$sqlam="SELECT * FROM question where NO='$no' and ID='$id' and PART='$part' order by NON ASC";
			 $stmtam=ociparse($con,$sqlam);
			 ociexecute($stmtam,OCI_DEFAULT);
			 $nrowsam=OCIFetchStatement($stmtam,$resultsam); 
			
			$non = $resultsam['NON'][$i2];
			$ans[$i]=$_POST['matrix'][$part][$no][$non];
			$i2++;
		}//if(type=6)
        
//存進DB-----------------------------------------------------------------------------------------------------------
		if($type==1||$type==2)  //文字存值
		{
			if($ans[$i])
			{
				$ans[$i]=strip_tags($ans[$i]);           //防止tags
				$ans[$i]=htmlspecialchars("$ans[$i]",ENT_QUOTES);  //防止單雙引號

				$sqla[$i]= "insert into TEXT values('$user_encode','$id',$part,$no,$non,'$ans[$i]')";     //存入DB
				$sqla[$i] =  iconv("UTF-8","BIG5",$sqla[$i]);				 
				$stmta = OCIPARSE($con,$sqla[$i]);
				if(!OCIEXECUTE($stmta,OCI_DEFAULT)) //參考OCI_DEFAULT參數說明
				{
					$oci_err=OCIError($stmta);
					echo "1,問卷資料寫入失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sqla[$i]. $oci_err[message], $headers);
					ocirollback($con); 
					exit();
				}				
			}//if($ans)
		}//if(1.2)

       	if($type==3||$type==5||$type==6)   //選擇存值
		{
		    if($ans[$i])
			{
				$sqla [$i]= "insert into ANS_OPT values('$user_encode','$id',$part,$no,$non,'$ans[$i]')";     //存入DB
				$sqla[$i] =  iconv("UTF-8","BIG5",$sqla[$i]);
				$stmta = OCIPARSE($con,$sqla[$i]);
				
				if(!OCIEXECUTE($stmta,OCI_DEFAULT)) //參考OCI_DEFAULT參數說明
				{
					$oci_err=OCIError($stmta);
					echo "1,問卷資料寫入失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sqla[$i]. $oci_err[message], $headers);
					ocirollback($con); 
					exit();
				}
			}//if($ans)
		}//if(3.5.6)

	}//for($i)


 $sqlid="insert into IDENTITY values('$id','$identity','$cip','$date')";
 $sqlid= iconv("UTF-8","BIG5",$sqlid);
 $stmtid= OCI_PARSE($con,$sqlid);
 if(!OCI_EXECUTE($stmtid)) //參考OCI_DEFAULT參數說明
 {
	$oci_err=OCIError($stmtid);
	echo "1,問卷資料寫入失敗，訊息：" . $oci_err[message] ;
	mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sqlid . $oci_err[message] , $headers);
	ocirollback($con);   
	exit();
 }

//檢查沒問題才存進DB
	ocicommit($con);   
	
	echo "0,問卷填答完成，謝謝您的參與！"; 
	


?>