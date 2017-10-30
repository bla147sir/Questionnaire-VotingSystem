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

$sql5="select * from QUESTIONNAIRE where ID='$id'";       
 
$stmt5=ociparse($con,$sql5); 
ociexecute($stmt5,OCI_DEFAULT); 
	  
$nrows5 = OCIFetchStatement($stmt5,$results5);

$done = $results5['DONE'][0] ;

if($done==4)
{
header("Location:index.php");
}

$k=$_SESSION['Username'];   // 問卷建立者 和 管理員權限

$sql6="select * from QUESTIONNAIRE where ID='$id' and ACCOUNT='$k'";       
 
$stmt6=ociparse($con,$sql6); 
ociexecute($stmt6,OCI_DEFAULT); 
	  
$nrows6 = OCIFetchStatement($stmt6,$results6);

$sql44="select * from MEMBER where IDENTITY='$k'";       
 
$stmt44=ociparse($con,$sql44); 
ociexecute($stmt44,OCI_DEFAULT); 
	  
$nrows44 = OCIFetchStatement($stmt44,$results44);

$identity = $results44['IDENTITY'][0] ;    //帳號
$status = $results44['STATUS'][0] ;   //身分

if($nrows6!=1 && $status!=1)
{
header("Location:index.php");
}

if($_POST["B2"])
	header("Location:design5.php?ID=$id");

//*************************************取問卷題目
$sql="select title from questionnaire where ID='$id' ";
$stmt=ociparse($con,$sql);     
ociexecute($stmt,OCI_DEFAULT); 
$t= OCIFetchStatement($stmt,$results);
$title=$results['TITLE'][0];
$title =iconv("BIG5","UTF-8", $title);
//********************************取有幾個部分
$sql="select DISTINCT part from question where ID='$id' ";
$stmt=ociparse($con,$sql);     
 ociexecute($stmt,OCI_DEFAULT); 
$part = OCIFetchStatement($stmt,$results); 
//echo $part."<br>";
//********************************每個部分的題數 
for($i=1;$i<=$part;$i++){    

	$sql1[$i]="select DISTINCT no from question where id='$id' and part='$i' ";
	$stmt1=ociparse($con,$sql1[$i]);     
	ociexecute($stmt1,OCI_DEFAULT); 
	$qno[$i] = OCIFetchStatement($stmt1,$results);
    //	echo $qno[$i]."<br>";
}    
//********************************每個部分的問題種類 題目
for($i=1;$i<=$part;$i++){    

	$sql3[$i]="select * from question where id='$id' and part='$i' order by NO ASC";
	$stmt1=ociparse($con,$sql3[$i]);     
	ociexecute($stmt1,OCI_DEFAULT); 
	$qno3[$i] = OCIFetchStatement($stmt1,$results);
    	for($j=1;$j<=$qno[$i];$j++){
			//********************取出矩陣題的least, most
			$type[$i][$j]=$results['TYPE'][$j-1];
			$question[$i][$j]=$results['QUESTION'][$j-1];
			$question[$i][$j] =iconv("BIG5","UTF-8", $question[$i][$j]);

			if($type[$i][$j]==6)
			{
				$least[$i][$j]=$results['LEAST'][$j-1];
				$most[$i][$j]=$results['MOST'][$j-1];
			}

		}
}
//********************************每一題的選項數量
for($i=1;$i<=$part;$i++){     
                  
	for($j=1;$j<=$qno[$i];$j++){
		$sql2[$i][$j]="select * from question where id='$id' and part='$i' and no='$j' and non='1' order by NO ASC";
		$stmt2=ociparse($con,$sql2[$i][$j]);     
		ociexecute($stmt2,OCI_DEFAULT); 
		$qno2[$i][$j] = OCIFetchStatement($stmt2,$results2); 
		//if($qno2[$i][$j] ){
			$num[$i][$j]=$results2['Q_NUM'][0];
			$other[$i][$j]=$results2['OTHER'][0];
	}
}

if(isset($_POST["B2"]))                
{
//********************************每一題的選項內容
for($i=1;$i<=$part;$i++){            	         	
		for($j=1;$j<=count($qno2[$i]);$j++){
			$arr_option= $_POST['option'];
			for($k=1;$k<=$num[$i][$j];$k++){
				$option_value[$i][$j][$k]=$arr_option[$i][$j][$k];
				//echo $option_value[$i][$j][$k]."<br>";
			}
		}
}
//********************************單選多選題 選項是否換行 & 是否要填寫其他 & 其他字數限制 
for($i=1;$i<=$part;$i++)                     
{
	$arr_br=$_POST['br'];
	$arr_other_require=$_POST['other_require'];
	$other_num=$_POST['other_num'];
	for($j=1;$j<=$qno[$i];$j++)    
	{
		//************************選項是否換行
		if($arr_br[$i][$j]=='on')
			$br_value[$i][$j]=1;
		else $br_value[$i][$j]=0;
		//************************其他字數限制
		if($other_num[$i][$j])
			$o_num[$i][$j]=$other_num[$i][$j];
		else $o_num[$i][$j]=0;
		
	}
}
//********************************把題目存到OPT table 
for($i=1;$i<=$part;$i++)                    //部分
{
	for($j=1;$j<=count($qno2[$i]);$j++)     //題號
	{   
		if($type[$i][$j]<6){
			for($k=1;$k<=$num[$i][$j];$k++)    //選項數
			{
				$sql2 = "insert into opt values('$id',$i,$j,$k,"."'".$option_value[$i][$j][$k]."')";
				$sql2 =iconv("UTF-8","BIG5", $sql2);
				$stmt = OCIPARSE($con,$sql2);
				//echo $sql2."<br>";
				if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
				{
				ocirollback($con);            
				exit();
				}
				else ocicommit($con);
			}	
		}
	}
}

//********************************存值到question table
for($i=1;$i<=$part;$i++)                          //部分
{
	for($j=1;$j<=$qno[$i];$j++)     //題數
	{   
		//********************存非矩陣題的其他字數 &是否換行 (update)
		if($type[$i][$j]<6){
			$s = "update question set OTHER_NUM=".$o_num[$i][$j].",BR=".$br_value[$i][$j]."   where id='$id' and part='$i' and no='$j'";
			//echo $s."<br>";
			$stmt = OCIPARSE($con,$s);
			if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
			{
			ocirollback($con);            
			exit();
			}
			else ocicommit($con);
		}
		else{
			//********************存矩陣題第一題的題目 (update)
			$sql3 = "update question set OTHER_NUM=0, QUESTION= '".$option_value[$i][$j][1]."',BR=0  where id='$id' and part='$i' and no='$j' and non='1' " ;
			$sql3 =iconv("UTF-8","BIG5", $sql3);
			//echo $sql3."<br>";
			$stmt = OCIPARSE($con,$sql3);
			if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
			{
			ocirollback($con);            
			exit();
			}
			else ocicommit($con);
			
			//********************存第 二 ~ n 題的題目 (insert)
			for($k=2;$k<=$num[$i][$j];$k++)    //題目數
			{
				$sql4 = "insert into question	values('$id',$i,$j,$k,"."'".$option_value[$i][$j][$k]."',6,".$least[$i][$j].",".$most[$i][$j].",".$num[$i][$j].",0,0,1,0)";
				$sql4 =iconv("UTF-8","BIG5", $sql4);
				//echo $sql4."<br>";
				$stmt = OCIPARSE($con,$sql4);
				if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
				{
				ocirollback($con);            
				exit();
				}
				else ocicommit($con);
			}
		}	
	}
}
//*************************************update questionnaire done=4
		$sql2 = "update questionnaire set DONE=4 where id='$id' ";
		
		$stmt = OCIPARSE($con,$sql2);

		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);

		$url = "q_design5.php?ID=$id";
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url'";
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
    <!-- Custom CSS -->   	
	<link href="css/ncue.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>

	<!-- jQuery-------------------------------------------------------------------------------------------------->
    <!-- Bootstrap Core JavaScript -->
    <script type="text/javascript" src="js/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/dist/js/bootstrapValidator.js"></script>
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

<div class="progress"  style="background-color:#cccccc;">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 80%;">
    完成度 : 80%
  </div>
</div>


	<form name='form1' method='POST' id='form1' action=""  class="form-horizontal">
		<div id="panel-calendar" class="panel panel-primary">
			
			<p class="bg-primary" style=" font-size: large;">&nbsp第四步-<? echo "<br>&nbsp".$title; ?></p>
				<div class="panel-body">
					<div class="container">
							<div class="col-md-8"> 
							
								<table class="table table-bordered">
									<tr class="success"><td>
										<h5><strong><li> 其他選項的字數限制</li><br><li>選擇是否要換行</li><br><li>填寫選項內容</li><br>&nbsp&nbsp&nbsp<font color="gray">ps. 矩陣題的選項內容為題目</font></strong></h5>	
									</tr></td>	
								</table>
<? 
//********************************每個部分
for($i=1;$i<=$part;$i++){
    echo "</br>";
    echo "<span class=\"label label-success\">"."第".$i."部分: </br></br>"."</span>";
	echo "</br>";
	echo "<table name=\"$i\" class=\"table table-bordered\" >"; 
	echo "<tr>";
	echo "<td>題號:</td>";
	echo "<td>題目:</td>";
	echo "<td>其他字數限制:</td>";
	echo "<td>選項是否要換行:</td>";
	echo "<td>選項內容:</td>";
    echo "</tr>";
	//********************************每個部分的題數 問題種類
	for($j=1;$j<=$qno[$i];$j++){ 
		echo "<tr>";
		echo "<td>".$j."</td>";
		//********************************每題題目
		echo "<td>".$question[$i][$j]."</td>";
		//********************************其他字數限制(最大填到99)
		if ($other[$i][$j]==1){		
			echo "<td><div class=\"form-group col-xs-2\">
									<div class=\"input-group\">
									<input type=\"text\" class=\"form-control check\" name=\"other_num[$i][$j]\" value=\" \" style='width:70px' >
									<div class=\"input-group-addon\" style='width:30px'>字</div>
									</div></div></td>"; 
			}
		else echo "<td></td>";
		
		//********************************選項是否要換行
		
		if($type[$i][$j]==4||$type[$i][$j]==5)
			echo "<td><input type=\"checkbox\" name=\"br[$i][$j]\" /></td>";
		else echo "<td></td>";
		//********************************題目的選項內容 (最多填100個字)
		if($num[$i][$j])
		{
			for($k=1;$k<=$num[$i][$j];$k++){
			
				if($k==1)
					echo "<td><div class=\"form-group col-xs-12\"><input type=\"text\" class=\"form-control check_opt \" name=\"option[$i][$j][$k]\"  value=\" \"  /></div></td>"; 
				else 
				{
					echo "<tr><td></td><td></td><td></td><td></td>"; 
					echo "<td><div class=\"form-group col-xs-12\"><input type=\"text\" class=\"form-control check_opt\" name=\"option[$i][$j][$k]\"  value=\" \"  /></div></td>"; 
					echo "</tr>";
				}
			}
		}
		else echo "<td></td></tr>";
		
	}echo "</table>";
	}
	 
	 echo "</br>";  

?>
		<div class="text-right " >
        <button type="submit" name="B2" class="btn btn-default" >下一步</button>
		</div>
		 
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

<script type="text/javascript">

$(document).ready(function() {
  
	
    $('#form1').bootstrapValidator({
		
        message: 'This value is not valid',
        
        fields: {
          
			check: 
			{
				selector:'.check',
                validators: {
                    notEmpty: {
                        message: '需填!!'
                    },
					stringLength: {
								max: 3,
								message: '最多填2位數'
							}
                }
            },
			check_opt: 
			{
				selector:'.check_opt',
                validators: {
                    notEmpty: {
                        message: '需填!!'
                    },
					stringLength: {
								max: 100,
								message: '最多填100個字'
							}
                }
            }
		}
    });
}); 
</script>
</body>

</html>
