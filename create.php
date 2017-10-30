<?php include("check.php"); ?>
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
	<link href="css/ncue.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<?php  
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

?>
<body onLoad="document.forms.form.user_id.focus()">

   <div class="container container_ncue"  valign="bottom"  > 
		<div class="row">
            <div class="col-md-12" valign=\" bottom \">				
				<div id="banner">
				
			
				</div>	<?	include("test_menu.php");?>
			</div>
		</div>
<br><br><br>

        <div class="row" style=" height:500px; "  >

			
            <div class="col-md-12" >
            <div class="contain" valign="middle">
		<table width="300"  >
		<tr> <td> 　　 </td></tr><tr> <td> 　　 </td></tr>
                     <tr align="center" bgcolor="#CEE0EC" >
							<th  scope="row" height="30" >&nbsp;</th>
							<td align="center" >問卷建立者帳號</td>
						</tr>
											
<? 

				$sql1="select IDENTITY from member";       
 
				 $stmt1=ociparse($con,$sql1); 
				 ociexecute($stmt1,OCI_DEFAULT); 
	  
				$nrows1 = OCIFetchStatement($stmt1,$results1); 
				for($i=0; $i<$nrows1 ;$i++) 
				{											
					$c = $results1['IDENTITY'][$i] ;
					$array[$i]=$c;
					$ii=$ii+1;
					echo "<tr align=\"center\" bgcolor=\"#FFFFFF\" >";
					echo "<th width=\"30\" scope=\"row\">$ii</th>";
					echo "<td align=\"center\" >$array[$i]</td>";
					echo "</tr>";
				}
?>
   </table>
<br>
<form id="form1" name="form1" method="post">
  <p>
    <label for="textfield">輸入帳號:</label>
    <input type="text" name="t1" id="textfield">
  </p>
  <p>
    <input type="submit" name="B1" id="button" value="新增">
	<input type="submit" name="B2" id="button" value="刪除">
  </p>

</form>

<?
$t = trim($_POST['t1']) ;

if(isset($_POST['B1']))
{
		$sql= "insert into member values('$t',2)";
		$stmt = OCIPARSE($con,$sql);

		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);

}

if(isset($_POST['B2']))
{
		$sql= "delete from member where IDENTITY='$t'";
		$stmt = OCIPARSE($con,$sql);

		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);

}
?>
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
