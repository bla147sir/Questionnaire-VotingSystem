<?php include("check.php");

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
	 
<?php  
include("connect.php");
 $id=$_GET['ID'];  // 取得網址後面的ID #  

$_SESSION['vid']=$id;


$sql_vote="select * from vote where ID='$id' ";  //取出該筆投票    
 $stmt_vote=ociparse($con,$sql_vote);     //取出資料固定格式
 ociexecute($stmt_vote,OCI_DEFAULT); 
 $nrows_vote = OCIFetchStatement($stmt_vote,$results_vote); //算取出來的有幾筆
$datetime= $results_vote['DATETIME'][0];
 $due= $results_vote['DUE'][0];

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

//排列順序-------------------------------------
		$order = $results_vote['ORDER_BY'][0];
		if($order==1)
		{
			$sql_can="select * from CANDIDATE where ID='$id' order by NAME ASC";    //取出全部候選人
			$order_by="姓名筆劃"; 
		}
		if($order==2)
		{
			$sql_can="select * from CANDIDATE where ID='$id' order by DEPARTMENT ASC";    //取出全部候選人
			$order_by="單位名稱"; 	
		}
		$stmt_can=ociparse($con,$sql_can);     
		 ociexecute($stmt_can,OCI_DEFAULT); 
		 $nrows_can = OCIFetchStatement($stmt_can,$results_can);

//***************************************************************************

$style = $results_vote['STYLE'][0];

//*********************************************************************add
if(isset($_POST["add"]))
{
	$url2 = "v_design2.php?ID=$id"; 
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url2'";
	echo "</script>";
}
//*********************************************************************modify
if(isset($_POST["modify"]))
{	
		//******************************************
		$title=$_POST['title'];
		$datetime=$_POST['datetime'];
		$due=$_POST['due'];

		//**************************************update table
		$update_vote = "update vote set TITLE='".$title."',DATETIME=$datetime, DUE=$due where id='$id'  ";
		$update_vote =iconv("UTF-8","BIG5", $update_vote);
		//echo $update_vote."<br>";
		$stmtach = OCIPARSE($con,$update_vote);
		if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
			{	
				ocirollback($con);            
				exit();
			}
			else ocicommit($con);

		
		$no=1;
		for($i=0;$i<$nrows_can;$i++){
			$candidate[$i]=$_POST['candidate'][$i];
			$department[$i]=$_POST['department'][$i];

			$update_candidate = "update candidate set NAME='".$candidate[$i]."',DEPARTMENT='".$department[$i]."' where id='$id' and no='$no' ";
			$update_candidate=iconv("UTF-8","BIG5", $update_candidate);
			//echo $candidate[$i]."<br>";
			$stmtach = OCIPARSE($con,$update_candidate);
		
			if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
			{	
				ocirollback($con);            
				exit();
			}
			else ocicommit($con);
			$no++;
		}


}
?>

<!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />   
	 <!-- Custom CSS -->   	
	<link rel="stylesheet" type="text/css" href="css/Style<?=$style?>.css" />
	<link href="css/ncue.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
	<link rel="stylesheet" type="text/css" href="css/bootstrapValidator.css" />   
    <link rel="stylesheet" type="text/css" href="css/tipped.css"/>
 <?php 
    	echo"<link rel=\"stylesheet\"  href=\"css/bg$style.css\" />";
    ?>	
<!-- Bootstrap Core JavaScript -->
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/dist/js/bootstrapValidator.js"></script>
	<script type="text/javascript" src="js/jquery.hoverpulse.js"></script>	
	<script type="text/javascript" src="js/imageAutoSize.js"></script>
	<script type="text/javascript" src="js/tipped.js"></script>

	
	<style type="text/css">
		.hover {
		   background: lightgrey; 
		}
	</style>

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

        <div class="row" style=" height:500px; " >
            <div class="col-md-12"  >
				<div id="panel-calendar" class="panel panel-primary dd">	<!--         -->
					<div class="container-fluid">
						<div class="span12">

			<form method='POST' name="form1" class="form-horizontal">
	
				<div id="a1"><h2 style="font-weight: bolder;" class="inline">
   		<?php
			//標題-------------------------------------------------------------------------------------
				$title = $results_vote['TITLE'][0];
				$title = iconv("BIG5","UTF-8",$title);
				echo "<input type=\"text\" class=\"form-control\" name=\"title\" value=\"$title\" size=\"50\"/>";	
		?>
			</h2></div></div>  
			
		<?php 
			// 開放時間 截止時間----------------------------------------
				echo "<table class=\"table table-bordered\">
						<tr class=\"success\"><td>
					   <div class=\"col-xs-3\">
						開放時間:&nbsp <input type='text' style='width:150px' class=\"form-control\" name='datetime'  value='$datetime' />
						</div>
						 <div class=\"col-xs-3\">
						截止時間:&nbsp <input type='text' style='width:150px' class=\"form-control\" name='due'  value='$due'  />
						</div>
						
					</td></tr>
    			</table>";

			//紀錄可投幾票 & 還剩下幾票-------------------------
			$most = $results_vote['MOST'][0];
			$least = $results_vote['LEAST'][0];
			echo"<div align=\"right\">
					最多可以投 $most 票，最少須投 $least 票			
				 </div>";
		?>

		 <div class="span12">
				<div id="a3">
   		
		<h6><font color="red">※依照<?echo $order_by;?>順序排列(共 <?echo $nrows_can; ?>人)</font></h6>
		
			</div></div>   <!--   <div class="span12">          <div id="a3">    -->
		<?php
		echo"<form method='POST' name=\"form_member\" id=\"form_member\" class=\"form-horizontal\" >
			  <div class=\"form-group\"><table class=\"table table-bordered\">";
		//印出候選人--------------------------------------------------
			$row = $results_vote['ROW_NUM'][0];
			
			$j=1;
			for($i=0;$i<$nrows_can;$i++)
			{
				$candidate = $results_can['NAME'][$i];
				$candidate = iconv("BIG5","UTF-8",$candidate);
				$department = $results_can['DEPARTMENT'][$i];
				$department = iconv("BIG5","UTF-8",$department);
				$no = $results_can['NO'][$i];
				$des = $results_can['DESCRIPTION'][$i];
				$des = iconv("BIG5","UTF-8",$des);

				if($most==1){
					$input_type="radio";
					$input_name="radio";
				}else{
					$input_type="checkbox";
					$input_name="checkbox[]";
				}//if(most==1)

				if($i%$row==0)
					echo "<tr>";

				echo "<td>	
							<div class='col-xs-1'><font color=\"blue\"> $j </font></div>
							<div class='col-xs-4'>
								<input type='text' class=\"form-control\" name='candidate[$i]'  value='$candidate' /></div>
							<div class='col-xs-6'>
								<input type='text' class=\"form-control\" name='department[$i]'  value='$department' /></div>
					  </td>";

				if($i%$row==($row-1))
					echo "</tr>";
				$j++;
			}//for(i)
			
			
			echo "</table></div>";// div=form-group
			
		?>
			<div class="form-group text-center">
			<button type="submit" class="btn btn-primary" name="modify" >修改完成</button></div>
			<div class="form-group text-right">
			<button type="submit" class="btn btn-default" name="add" >新增筆數</button></div>

	 </form>
     </div>     <!--<div class="container-fluid">  -->
     </div>		<!--  <div id="panel-calendar" class="panel panel-primary dd">	    -->
	 </div>		<!--  <div class="col-md-12"  > -->
     </div>		<!--  <div class="row" style=" height:500px; " > -->
     </div>		<!-- /.container --> 

    <div class="container">

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p></p>
                </div>	<!--  <div class="col-lg-12"> -->
            </div> <!--  <div class="row"> -->
        </footer>

    </div>
    <!-- /.container -->

	<!-- The go top sign -->
	<div id="gotop"><img src="./image/arrowup.png" height="50px"><br>回到上面</div>
	<!-- /.The go top sign -->

<script type="text/javascript">

//等比例縮小
$(window).load(function(){ $("div.resize").imageAutoSize(<? echo $height-150; ?>,<? echo $height-150; ?>);});

</script>  

<script type="text/javascript">
$(function(){
    $("#gotop").click(function(){
        jQuery("html,body").animate({
            scrollTop:0
        },1000);
    });
    $(window).scroll(function() {
        if ( $(this).scrollTop() > 300){
            $('#gotop').fadeIn("fast");
        } else {
            $('#gotop').stop().fadeOut("fast");
        }
    });
});
</script>


</body>
</html>