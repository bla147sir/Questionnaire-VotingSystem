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

if($done==4 || $done==3)
{
header("Location:index.php");
}

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
$sql =iconv("BIG5","UTF-8", $sql);
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
//********************************每個部分的題數
for($i=1;$i<=$part;$i++){    

	$sql1[$i]="select DISTINCT no from question where id='$id' and part='$i' order by NO ASC";
	$stmt1=ociparse($con,$sql1[$i]);     
	ociexecute($stmt1,OCI_DEFAULT); 
	$qno[$i] = OCIFetchStatement($stmt1,$results1); 
}
//********************************每個部分的問題種類 & 題目
for($i=1;$i<=$part;$i++){                     
   
	$sql2="select * from question where id='$id' and PART='$i' order by NO ASC";
	$sql2 =iconv("BIG5","UTF-8", $sql2);
	$stmt2=ociparse($con,$sql2);     
	ociexecute($stmt2,OCI_DEFAULT); 
	$qno2[$i] = OCIFetchStatement($stmt2,$results2); 
		
		for($j=1;$j<=$qno2[$i];$j++){
			$type[$i][$j]=$results2['TYPE'][$j-1];
			$question[$i][$j]=$results2['QUESTION'][$j-1];
			$question[$i][$j] =iconv("BIG5","UTF-8", $question[$i][$j]);
		}
}

if(isset($_POST['B2'])){
//********************************最多最少轉換成陣列
for($i=1;$i<=$part;$i++){ 
		$arr_check_least= $_POST['check_least'];			
		for($j=1;$j<=$qno[$i];$j++){
		    if($type[$i][$j]==4){
				$most[$i][$j]=$_POST['most'][$i][$j];
				$least[$i][$j]=$_POST['least'][$i][$j];
			}
			else {
				if($arr_check_least[$i][$j]=='on')
					$least[$i][$j]=1;
				else $least[$i][$j]=0;
				//********************限制字數 
				if($type[$i][$j]<3)
					$most[$i][$j]=$_POST['most'][$i][$j];
				else $most[$i][$j]=1;
						
				}
			}
		}

//********************************每題的選項數量 & 是否要有"其他"選項 
for($i=1;$i<=$part;$i++){           	
	    $arr_num= $_POST['num']; 
		$arr_other=$_POST['other'];

		for($j=1;$j<=$qno[$i];$j++){
			//********************其他選項
			if($arr_other[$i][$j]=='on')
				$other_value[$i][$j]=1;
			else
				$other_value[$i][$j]=0;	
			
			//********************每題的選項數量
			if($arr_num[$i][$j])
				$num_value[$i][$j]=$arr_num[$i][$j];
			else 
				$num_value[$i][$j]=0;
			
		}
 }
 //********************************矩陣題的選項內容 (ex.非常滿意...)
for($i=1;$i<=$part;$i++){           	
	    $array_opt= $_POST['arr_opt']; 	
		
		for($j=1;$j<=$qno[$i];$j++){
			if($type[$i][$j]==6){
					for($k=1;$k<=5;$k++){
						if($array_opt[$i][$j][$k]){
							$arr_value[$i][$j][$k]=$array_opt[$i][$j][$k];	
						}
					}
					
				
			}
							
		}

 }

//********************************存最多最少值& 選項數量 & 其他 到question table (update)
for($i=1;$i<=$part;$i++) {  
	for($j=1;$j<=$qno[$i];$j++){
		$sql2 = "update question set LEAST=".$least[$i][$j].",MOST=".$most[$i][$j].",Q_NUM=".$num_value[$i][$j].",OTHER=".$other_value[$i][$j]." where id='$id' and part='$i' and no='$j' and non='1'" ;
		//echo $sql2."<br>";
		
		$stmt = OCIPARSE($con,$sql2);

		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);
		
		//********************************存矩陣題選項到opt table
		if($type[$i][$j]==6)
		{
			for($k=1;$k<=count($arr_value[$i][$j]);$k++)    //選項數
			{
				$sql3 = "insert into opt values('$id',$i,$j,$k,"."'".$arr_value[$i][$j][$k]."')";
				//echo $sql3."<br>";
				$sql3 =iconv("UTF-8","BIG5", $sql3);
				$stmt = OCIPARSE($con,$sql3);
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
//*************************************update questionnaire done=3
		$sql2 = "update questionnaire set DONE=3 where id='$id' ";
		
		$stmt = OCIPARSE($con,$sql2);

		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);

$url = "q_design4.php?ID=$id";
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
    <!--<link href="css/bootstrap.min.css" rel="stylesheet">-->
    <!-- Custom CSS -->
    <!--<link href="css/shop-item.css" rel="stylesheet">-->
    <link href="css/index_label.css" rel="stylesheet">
	<link href="css/ncue.css" rel="stylesheet">
	<!-- menu -->
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<script type="text/javascript" src="./js.JScript"></script>

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
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
    完成度 : 60%
  </div>
</div>

	<form name='form1' method='POST' id='form1' action=""  class="form-horizontal">
		<div id="panel-calendar" class="panel panel-primary">
			
			<p class="bg-primary" style=" font-size: large;">&nbsp第三步-<? echo "<br>&nbsp".$title; ?></p>
				<div class="panel-body">
					<div class="container">
					<div class="col-md-8"> 
					<table class="table table-bordered">
						<tr class="success"><td>
						<h5><strong><li> 填寫每題的選項數量(不包括"其他" 選項)</li><br><li>選擇是否為必選</li><br><li>選項最多能選幾個</li></strong></h5>	
						</tr>
						</td>
				    </table>
							  
<? 
//********************************每個部分	
for($i=1;$i<=$part;$i++){
    echo "</br>";
    echo "<span class=\"label label-success\">"."第".$i."部分: <br>"."</span>";
	echo "</br>";
	echo "<table name=\"$i\" class=\"table table-bordered\">"; 
	echo "<tr>";
	echo "<td>題號:</td>";
    echo "<td>題目:</td>";
	echo "<td>選項數量:</td>";
	echo "<td>其他:</td>";
	echo "<td>必選:</td>";
	echo "<td>最多選幾個:</td>";
    echo "</tr>";
	//********************************每題的問題種類
	for($j=1;$j<=$qno[$i];$j++){      

		echo "<tr>";
		echo "<td>".$j."</td>";
	
		//********************************每題題目
		echo "<td>".$question[$i][$j]."</td>";
        //********************************讓user輸入選項數量 (最大填到99)   
		if ($type[$i][$j]>2){	
			
			echo "<td>
				<div class=\"form-group col-xs-3\">
					<div class=\"input-group\">
						<input type=\"text\" class=\"form-control check\" name=\"num[$i][$j]\" value=\" \" style='width:70px' >
					<div class=\"input-group-addon\" style='width:30px'>個</div>
					</div>
				</div></td>";
		}
		else{
			echo "<td>沒有選項</td>";
		}
		//********************************其他欄位
		if ($type[$i][$j]>2&&$type[$i][$j]<6){		
			echo "<td><input type=\"checkbox\" name=\"other[$i][$j]\" /></td>"; 
			}
		else echo "<td></td>";
		
		//********************************user填入最少最多選幾個 (最大填到999)
		if($type[$i][$j]==4){
			echo "<td>
				<div class=\"form-group col-xs-3\">
				 <div class=\"input-group\">
				  <input type=\"text\" class=\"form-control check\" name=\"least[$i][$j]\" value=\" \" style='width:70px'  >
					<div class=\"input-group-addon\" style='width:30px'>個</div>
				</div></div></td>";
			echo "<td>
				<div class=\"form-group col-xs-3\">
				 <div class=\"input-group\">
				  <input type=\"text\" class=\"form-control check\" name=\"most[$i][$j]\" value=\" \" style='width:70px'  >
					<div class=\"input-group-addon\" style='width:30px'>個</div>
				</div></div></td>";

		}
		else {
			echo "<td><input type=\"checkbox\" name=\"check_least[$i][$j]\" ></td>";
		
			 
			if($type[$i][$j]<3){
				echo "<td>
				<div class=\"form-group col-xs-3\">
				<div class=\"input-group\">
					<input type=\"text\" class=\"form-control check\" name=\"most[$i][$j]\" value=\" \" style='width:70px' >
					<div class=\"input-group-addon\" style='width:30px'>字</div>
				</div></div></td>";
			}
			else 
				echo "<td></td>";
		}
		
		echo "</tr>";
		//********************************矩陣題的選項 (最多填10個字)
		if($type[$i][$j]==6){
			echo "<td></td>";
			echo "<td>矩陣題的選項:</td>&nbsp&nbsp";
			echo "<td colspan=\"4\">
				<div class=\"form-group col-xs-12\"><input type=\"text\" name=\"arr_opt[$i][$j][1]\" class=\"form-control check_array\" value=\"非常滿意 \"  />&nbsp&nbsp";
			echo "<input type=\"text\" name=\"arr_opt[$i][$j][2]\" class=\"form-control check_array\"  value=\"滿意 \"   >&nbsp&nbsp";
			echo "<input type=\"text\" name=\"arr_opt[$i][$j][3]\" class=\"form-control\" value=\"普通 \"   maxlength=\"11\"/>&nbsp&nbsp";
			echo "<input type=\"text\" name=\"arr_opt[$i][$j][4]\" class=\"form-control\" value=\"不滿意 \" maxlength=\"11\"/>&nbsp&nbsp";
			echo "<input type=\"text\" name=\"arr_opt[$i][$j][5]\" class=\"form-control\" value=\"非常不滿意 \" maxlength=\"11\"  /></td></div>";
		}
	}
	echo "</table>";
	}
	 
	 echo "</br>";  
	 
?>
		<div class="text-right " >
		<button class="btn btn-default" type="submit" name="B2">下一步</button>
		</div>
	
									</div>	 <!--col-md-8--> 
								</form>
						</div> <!--container-->
					</div> <!--panel-body-->
				</div> <!--panel-calendar-->
			</div> <!--col-md-10-->
    </div> <!--row-->
	
	<!-- jQuery -->
    <script src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>	
	<script type="text/javascript" src="js/bootstrapValidator.js"></script>
    <script src="js/bootstrap.min.js"></script>
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
								max: 4,
								message: '最多填3位數'
							}
                }
            },
			check_array: 
			{
				selector:'.check_array',
                validators: {
                    notEmpty: {
                        message: '需填!!'
                    },
					stringLength: {
								max: 10,
								message: '最多填10個字'
					}
                }
            }
		}
    });
}); 
</script>
</body>

</html>
