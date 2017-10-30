<?php include("check.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>國立彰化師範大學線上問卷及投票系統</title>
	 
<?php  
include("connect.php");
 $id=$_GET['ID'];  // 取得網址後面的ID #  

$_SESSION['vid']=$id;


$sql_vote="select * from vote where ID='$id' ";  //取出該筆投票    
 $stmt_vote=ociparse($con,$sql_vote);     //取出資料固定格式
 ociexecute($stmt_vote,OCI_DEFAULT); 
 $nrows_vote = OCIFetchStatement($stmt_vote,$results_vote); //算取出來的有幾筆

if($nrows_vote<=0)
{
	echo "<script language = JavaScript>";
	echo "alert(\"無此投票活動！\");"; 
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
if($date<$results_vote['DATETIME'][0])
{
	echo "<script language = JavaScript>";
	echo "alert(\"此投票活動尚未開放！\");"; 
	echo "window.location.href='index.php'";
	echo "</script>"; 
	exit;
}
if($date>$results_vote['DUE'][0])
{	
	$due_flag=1;
	//echo "<script language = JavaScript>";
	//echo "alert(\"此投票活動已截止！\");";
	//echo "window.location.href='index.php';";
	//echo "</script>"; 
	//exit;
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
	echo "<script language = JavaScript>";
	echo "alert(\"此投票活動僅開放【 $partcipant 】填寫\");"; 
	echo "window.location.href='index.php';";
	echo "</script>"; 
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
$username=$_SESSION['Username'];
$sql_id="select * from election_info where ID='$id' and IDENTITY='$username' " ;     //已投過票的紀錄
 $stmt_id=ociparse($con,$sql_id);     
 ociexecute($stmt_id,OCI_DEFAULT); 
 $nrows_id = OCIFetchStatement($stmt_id,$results_id);
if ($nrows_id>0  && !$status==1) 
{
	$finish_flag=1;
	//echo "<script language = JavaScript>";
	//echo "alert(\"您已投過票，謝謝您的參與！\");"; 
	//echo "window.location.href='index.php';";
	//echo "</script>"; 
	//exit;
}
//檢查結束
//***************************************************************************


$style = $results_vote['STYLE'][0];

?>
<!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />   
    <!-- Custom CSS -->   	
	<link rel="stylesheet" type="text/css" href="css/Style<?=$style?>.css" />
	<link href="css/ncue.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>

<!-- Bootstrap Core JavaScript -->
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/minwt.auto_full_height.mini.js"></script>

</head>

<body>
  <div class="container container_ncue"  valign="bottom"  > 
		<div class="row">
            <div class="col-md-12" valign=\" bottom \">				
				<div id="banner">
				
			
				</div>	<?	include("test_menu.php");?>
			</div>
		</div>
<br>

        <div class="row" style=" height:500px; "  >
            <div class="col-md-12"  >

		   <div id="panel-calendar" class="panel panel-primary dd">			
				<!--<div class="panel-body">-->
					<div class="container-fluid">
							<div class="col-md-12">
				<div id="a1"><h3 style="font-weight: bolder;" class="inline">
   		<?php
			//標題-------------------------------------------------------------------------------------
				$title = $results_vote['TITLE'][0];
				$title = iconv("BIG5","UTF-8",$title);
				echo "$title";	
				
		?>
			</h3></div></div>  
			<div class="row-fluid">
				<div class="span12" >
					<p>
						<?php
						//介紹詞-----------------------------------------------------------------------
							$introduction = $results_vote['INTRODUCE'][0] ;
							$introduction= iconv("BIG5","UTF-8",$introduction);
	 						echo "$introduction <br>";
   							
						?>
					</p>
			  </div>
		<?php
			  $type = $results_vote['TYPE'][0]; 
			  if($type=="1")
				  $address="v_member.php?ID=$id";
			  else 
				  $address="v_work.php?ID=$id";

			  $msg="前往投票";

			  if($finish_flag==1) $msg="已完成投票";

			  if($due_flag==1) $msg="活動已截止"; 
			   
			 
		?>			     
			  <div class="row-fluid"><form action="<?=$address?>" method="POST" name="form1">
				<div align="center"><button name="Start" type="submit" class="btn btn-md btn-primary" <?=($due_flag==1 || $finish_flag==1)?"disabled":""?>><?=$msg?></button></div></form><br>                
		  </div> </div></div> 
            

</div>
                

            </div>

        </div>

    </div>
    <!-- /.container -->

    <div class="container">

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p></p>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->


</body>

</html>
