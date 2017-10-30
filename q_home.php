<?php 
include("check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>國立彰化師範大學線上問卷及投票系統</title>
<?
$id=$_GET['ID'];

include("connect.php");

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
	$due_flag=1;
	//echo "<script language = JavaScript>";
	//echo "alert(\"此問卷已截止！\");";
	//echo "window.location.href='index.php';";
	//echo "</script>"; 
	//exit;
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
	$finish_flag=1;
	//echo "<script language = JavaScript>";
	//echo "alert(\"您已填過此問卷，謝謝您的參與！\");"; 
	//echo "window.location.href='index.php';";
	//echo "</script>"; 
	//exit;
}
//檢查結束
//***************************************************************************
$style = $results['STYLE'][0];
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
$sql1="select * from questionnaire where ID='$id' ";    

 $stmt1=ociparse($con,$sql1);     //取出資料固定格式
 ociexecute($stmt1,OCI_DEFAULT); 
 $nrows1 = OCIFetchStatement($stmt1,$results1); //算取出來的有幾筆
				

			//標題-----------------------------------------------------------------------
				for($i=0; $i<$nrows1 ;$i++) 
    			{  
					$title = $results1['TITLE'][$i] ;
					$title= iconv("BIG5","UTF-8",$title);
					echo "$title";
					
   	 			}
			?>

			</h3></div></div> 
			
			<div class="row-fluid">
				<div class="span12" >
					<p>
						<?php
						//首頁活動說明-----------------------------------------------------------------------
							for($i=0; $i<$nrows1 ;$i++) 
    						{ 
	 							$des = $results1['INTRODUCE'][$i] ;
								$des= iconv("BIG5","UTF-8",$des);
	 							echo "$des <br>";
   							}
						?>
					</p>
			  </div>
			  <?  
				$address="q_fillin.php?ID=$id"; 
				$msg="開始填答";

				if($finish_flag==1) $msg="已完成填答";

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

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
