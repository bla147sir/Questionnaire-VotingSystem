<?session_start();
include("connect.php");
include("check.php");
$dd=$_GET['ID'];

$k=$_SESSION['Username'];
$commit=1;

$sql="select * from questionnaire where ID='$dd'";
$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
 OCIFetchStatement($stmt,$results); 
  $undertaker=$results['ACCOUNT'][0];
  $ana=$results['ANALYZE'][0];

$aa="select * from member where IDENTITY='$k'"; 
$stmtaa=ociparse($con,$aa); 
 ociexecute($stmtaa,OCI_DEFAULT); 
$nrowsaa = OCIFetchStatement($stmtaa,$resultsaa); 
 
$QV=$_SESSION['QV'];
?>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">




<?
//if($_SESSION['Username']==$undertaker||($nrowsaa==1&&$resultsaa['STATUS'][0] ==1))
//限管理者
	


if($nrowsaa==1&&$resultsaa['STATUS'][0]==1 )
{?>

  			<?
				if ($QV==1)
			{ $ID=$_GET['ID'];
				$strSQL="delete from vote where ID='$ID' ";
				$objParse = oci_parse($con, $strSQL); 
				if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "vote 刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

				$strSQL="delete from candidate where ID='$ID'  ";
				$objParse = oci_parse($con, $strSQL);  
				if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "candidate刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }


				$strSQL="delete from election_info where ID='$ID' ";
				$objParse = oci_parse($con, $strSQL);  
				if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "election_info  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }


				$strSQL="delete from elector where ID='$ID'";
				$objParse = oci_parse($con, $strSQL);  

				if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "elector  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }



				$strSQL="delete from masterpiece where  ID='$ID'";
				$objParse = oci_parse($con, $strSQL);  
								if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "masterpiece  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

				
				$strSQL="delete from vote_chose where ID='$ID'";
			    $objParse = oci_parse($con, $strSQL);  
				if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "vote_chose  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

		ocicommit($con);
	
			}
			else{
		
			$ID=$_GET['ID'];

			$strSQL="delete from ANS_OPT where ID='$ID'";
			$objParse = oci_parse($con, $strSQL);  
			if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "ANS_OPT  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

			$strSQL="delete from DESCRIPTION where ID='$ID'";
			$objParse = oci_parse($con, $strSQL);  
			if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "DESCRIPTION  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

			
			$strSQL="delete from IDENTITY where ID='$ID'";
			$objParse = oci_parse($con, $strSQL);  
			if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "IDENTITY  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

			
			$strSQL="delete from JUMP where ID='$ID'";
			$objParse = oci_parse($con, $strSQL);  
			if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "JUMP  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

			
			$strSQL="delete from OPT where ID='$ID'";
			$objParse = oci_parse($con, $strSQL);  
			if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "OPT  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

			
			$strSQL="delete from QUESTION where ID='$ID'";
			$objParse = oci_parse($con, $strSQL);  
			if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "QUESTION  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

			
			$strSQL="delete from QUESTIONNAIRE where ID='$ID'";      
  
			$objParse = oci_parse($con, $strSQL);  
			if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "QUESTIONNAIRE  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

			
		
			$strSQL="delete from TEXT where ID='$ID'";
			$objParse = oci_parse($con, $strSQL);  
			if(!OCI_EXECUTE($objParse)) //參考OCI_DEFAULT參數說明
				 {
					$oci_err=OCIError($stmt_choose);
					echo "TEXT  刪除失敗，訊息：" . $oci_err[message] ;
					mail('bob@cc.ncue.edu.tw', $_SERVER['PHP_SELF'], $sql_choose . $oci_err[message] , $headers);
					ocirollback($con);   
					exit();
				 }

			ocicommit($con);

			}
			


echo "<script type='text/javascript'>;";
	echo "alert(\"刪除成功\");";
	echo "window.location.href='voteAll.php';";
	echo "</script>";
			 } //if
			else{?>
			
				<script type='text/javascript'>
						alert("刪除失敗");
				window.location.href='index.php';
				</script>
			<?}?>

			
 </BODY>
</HTML>
