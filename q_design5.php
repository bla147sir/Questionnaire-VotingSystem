<?php session_start(); 
include("connect.php");

				$k=$_SESSION['Username'];

				$sql8="select * from member where IDENTITY='$k'";       
 
				 $stmt8=ociparse($con,$sql8); 
				 ociexecute($stmt8,OCI_DEFAULT); 
	  
				$nrows8 = OCIFetchStatement($stmt8,$results8);

				if($nrows8!=1)
				{	
				  header("Location:index.php");
				}

$id=$_GET['ID'];

$k=$_SESSION['Username'];  // 問卷建立者 和 管理員權限
       
$sql3="select * from QUESTIONNAIRE where ID='$id' and ACCOUNT='$k'";       
 
$stmt3=ociparse($con,$sql3); 
ociexecute($stmt3,OCI_DEFAULT); 
	  
$nrows3 = OCIFetchStatement($stmt3,$results3);

$sql4="select * from MEMBER where IDENTITY='$k'";       
 
$stmt4=ociparse($con,$sql4); 
ociexecute($stmt4,OCI_DEFAULT); 
	  
$nrows4 = OCIFetchStatement($stmt4,$results4);

$identity = $results4['IDENTITY'][0] ;    //帳號
$status = $results4['STATUS'][0] ;   //身分

if($nrows3!=1 && $status!=1)
{
header("Location:index.php");
}

//*************************************取問卷題目  
$sql="select title from questionnaire where ID='$id' ";
$stmt=ociparse($con,$sql);     
ociexecute($stmt,OCI_DEFAULT); 
$t= OCIFetchStatement($stmt,$results);
$title=$results['TITLE'][0];
$title =iconv("BIG5","UTF-8", $title);

if($_POST["button2"])
{
	unset($_SESSION['Username']);
	echo "<script language=\"javascript\">"; 
    echo "alert(\"登出成功！\")"; 
	echo "</script>";
}

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

<!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <!--<link href="css/bootstrap.min.css" rel="stylesheet">-->
    <!-- Custom CSS -->
    <!--<link href="css/shop-item.css" rel="stylesheet">-->
    <link href="css/index_label.css" rel="stylesheet">
	<link href="css/ncue.css" rel="stylesheet">
	<script src="main.js">  </script>
	<script src="bootstrap-datepicker.js">  </script>
	<!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

	<!-- menu -->
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

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

<div class="progress"  style="background-color:#cccccc;">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
    完成度 : 100%
  </div>
</div>

	<form name='form1' method='POST' id='form1' action=""  class="form-horizontal">
		<div id="panel-calendar" class="panel panel-primary">
			
			<p class="bg-primary" style=" font-size: large;">&nbsp第五步-<? echo "<br>&nbsp".$title; ?></p>
				<div class="panel-body">
					<div class="container">
							<div class="col-md-8">
							
          <h1>新增完成！</h1>
		  
		  <br><br>
         產生的問卷網址:
		<?
			echo	"<a href=\"q_home.php?ID=$id\">120.107.186.109/ncue/q_home.php?ID=$id</a>"; 
		?>
									</div>	 <!--col-md-8--> 
								</form>
						</div> <!--container-->
					</div> <!--panel-body-->
				</div> <!--panel-calendar-->
			</div> <!--col-md-10-->
    </div> <!--row-->
	
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
