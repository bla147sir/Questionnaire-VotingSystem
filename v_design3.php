<?php session_start(); 

$_SESSION["count_elector"]=0;   // mimi
$_SESSION["count_candidate"]=0; 

include("connect.php");

				$account=$_SESSION['Username'];
				$undertaker=$_SESSION['NAME'];
				$department=$_SESSION['department'];

				$sql8="select * from member where IDENTITY='$account'";       
 
				 $stmt8=ociparse($con,$sql8); 
				 ociexecute($stmt8,OCI_DEFAULT); 
	  
				$nrows8 = OCIFetchStatement($stmt8,$results8);

				if($nrows8!=1)
				{	
				  header("Location:index.php");
				}
$id=$_GET['ID'];

//******************************************************select type
$select="select * from vote where ID='$id' ";
$stmt=ociparse($con,$select);     
ociexecute($stmt,OCI_DEFAULT); 
$t= OCIFetchStatement($stmt,$result);
$type=$result['TYPE'][0];
	
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
	<!-- Custom CSS -->
    <link href="css/index_label.css" rel="stylesheet">
	<link href="css/ncue.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>


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

			
            <div class="col-md-12" >
			<div class="progress"  style="background-color:#cccccc;">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
    完成度 : 100%
  </div>
</div>
	
		<div id="panel-calendar" class="panel panel-primary">
			<p class="bg-primary" style=" font-size: large;">&nbsp第三步-完成</p>
				<div class="panel-body">
					<div class="container">
							<div class="col-md-10">
								<form name="form" method='POST'  >

							
								
			
							 <h1>新增完成！</h1>
		  
							 <br><br>
							 產生的投票網址:
							<?
								echo	"<a href=\"v_home.php?ID=$id\">120.107.186.109/ncue/v_home.php?ID=$id</a>"; 
							 ?>			

								</form>
							</div>	 <!--col-md-9-->	
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

 <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrapValidator.js"></script>


</body>
</html>
