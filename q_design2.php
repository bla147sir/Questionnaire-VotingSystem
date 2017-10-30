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

$sql2="select * from QUESTIONNAIRE where ID='$id'";       
 
$stmt2=ociparse($con,$sql2); 
ociexecute($stmt2,OCI_DEFAULT); 
	  
$nrows2 = OCIFetchStatement($stmt2,$results2);

$done = $results2['DONE'][0] ;

if($done==4 || $done==3 || $done==2)
{
header("Location:index.php");
}

$k=$_SESSION['Username'];   // 問卷建立者 和 管理員權限
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
//*************************************按下button submit2後 (存part)
if(isset($_POST["submit2"])){
	$part=$_POST['part'];
	for($i=1;$i<=$part;$i++){
		$ins_part = "insert into question (ID, PART) values('$id',$i)";
		$stmt = OCIPARSE($con,$ins_part);
		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
			ocirollback($con);            
			exit();
		}
		else ocicommit($con);
	}
}

//*************************************按下button B2後
if(isset($_POST["B2"]))                
{	
	//********************************取有幾個部分  
	$sel_part="select DISTINCT part from question where ID='$id' ";
	$stmt=ociparse($con,$sel_part);     
	ociexecute($stmt,OCI_DEFAULT); 
	$part = OCIFetchStatement($stmt,$results);  
	//********************************取指導語 & 部分標題
	for($i=1;$i<=$part;$i++)  
	{
		$textarea=$_POST['textarea'];           
        $part_title=$_POST['part_title'];		
	}
	//********************************取問題種類 & 問題題目
	for($i=1;$i<=$part;$i++){  
		 
	    $arr_type= $_POST['select'];           	
		$arr_question= $_POST['question'];		
		for($j=1;$j<=count($arr_type[$i]);$j++){
		  
			$type_value[$i][$j]=$arr_type[$i][$j];
			$question_value[$i][$j]=$arr_question[$i][$j];
			//echo $question_value[$i][$j]."<br>";
		}
	}
//*************************************把值存到description table
	for($i=1;$i<=$part;$i++){ 
			if($part_title[$i] || $textarea[$i] )
			{ 
				$sql1= "insert into description values('$id',$i,'$part_title[$i]','$textarea[$i]')";
				$sql1 =iconv("UTF-8","BIG5", $sql1);
				$stmt = OCIPARSE($con,$sql1);

				if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
				{
				ocirollback($con);            
				exit();
				}
				else ocicommit($con);
			}
		}
	
//*************************************把值存到question table
for($i=1;$i<=$part;$i++) {   
	for($j=1;$j<=count($arr_type[$i]);$j++){
		if($j==1){                          //只有1題題目(update)
			if($type_value[$i][$j]<6)
				$sql2 = "update question set NO=$j, NON=1, QUESTION='".$question_value[$i][$j]."', TYPE=".$type_value[$i][$j]." where id='$id' and part='$i'";
			else {
				if($question_value[$i][$j]) {  //矩陣題的標題題目
					$arr_ques = "update question set NO=1, NON=0, QUESTION='".$question_value[$i][$j]."', TYPE=".$type_value[$i][$j].",LEAST=0, MOST=0, Q_NUM=0, OTHER=0, BR=0, OTHER_NUM=0 where id='$id' and part='$i'";
					//$arr_ques = "insert into question values('$id',$i,$j,0,"."'".$question_value[$i][$j]."',".$type_value[$i][$j].",0,0,0,0,0,0,0)" ;  
					$arr_ques =iconv("UTF-8","BIG5", $arr_ques);
					$stmt = OCIPARSE($con,$arr_ques);
					if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
					{
					ocirollback($con);            
					exit();
					}
					else ocicommit($con);
					$sql2 = "insert into question (ID, PART, NO, NON, TYPE, LEAST, MOST, Q_NUM, OTHER, BR, OTHER_NUM  ) values('$id',$i,1,1,".$type_value[$i][$j].",0,0,0,0,0,0)" ;

				}
				//else  //矩陣題沒有標題題目
				//	$sql2 = "update question set NO=$j, NON=1, TYPE=".$type_value[$i][$j]." where id='$id' and part='$i'"; 
			}	
		}
		else {                              //超過1題題目(insert)
			if($type_value[$i][$j]<6)
				$sql2= "insert into question (ID, PART, NO, NON, TYPE) values('$id',$i,$j,1,".$type_value[$i][$j].")";
			else{
				if($question_value[$i][$j]){	//矩陣題的標題題目	
					$arr_ques = "insert into question values('$id',$i,$j,0,"."'".$question_value[$i][$j]."',".$type_value[$i][$j].",0,0,0,0,0,0,0)";  
					$arr_ques =iconv("UTF-8","BIG5", $arr_ques);
					$stmt = OCIPARSE($con,$arr_ques);
					if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
					{
					ocirollback($con);            
					exit();
					}
					else ocicommit($con);
					$sql2 = "insert into question (ID, PART, NO, NON, QUESTION, TYPE) values('$id',$i,$j,1,"."'".$question_value[$i][$j]."',".$type_value[$i][$j].")";
				}
				//else  //矩陣題沒有標題題目
					//$sql2 = "insert into question (ID, PART, NO, NON, TYPE) values('$id',$i,$j,1,".$type_value[$i][$j].")";
			}
		}
		//echo $sql2."<br>";
		$sql2 =iconv("UTF-8","BIG5", $sql2);
		$stmt = OCIPARSE($con,$sql2);

		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);
	}
}
//*************************************update questionnaire done=2
		/*$sql2 = "update questionnaire set DONE=2 where id='$id' ";
		
		$stmt = OCIPARSE($con,$sql2);

		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);
*/
	$url2 = "q_design3.php?ID=$id";
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url2'";
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
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 40%;">
    完成度 : 40%
  </div>
</div>

		<div id="panel-calendar" class="panel panel-primary">
			<p class="bg-primary" style=" font-size: large;">&nbsp第二步-<? echo "<br>&nbsp".$title; ?></p>
				<div class="panel-body">
					<div class="container">
							<div class="col-md-8">   
		    
				
				<table class="table table-bordered">
						<tr class="success"><td>
						<h5><strong>第一步驟: 選擇您要幾個部分<br><br>第二步驟: 選擇每個部份的題目選項<br><br>第三步驟: 選擇每題的問題類型，並填上題目<br><br><font color="gray">ps. 此步驟的矩陣題題目為矩陣題的大題目</font></strong></h5>	
						</tr>
						</td>
				</table>
						
			<form name='form2' method='POST'  action=""  class="form-horizontal">
            
           <p><label class="control-label">部分:</label>
			<?  
				//****************************************選擇幾個部分
				$part = $_POST['part']; 
				$arr_part = array_fill(1,6,"");
				$arr_part[$part]="selected";
			?>
				 
				<select  name="part" style="width:100px">
				<option value="1" <?=$arr_part[1]?>>1</option>
				<option value="2" <?=$arr_part[2]?>>2</option>
				<option value="3" <?=$arr_part[3]?>>3</option>
				<option value="4" <?=$arr_part[4]?>>4</option>
				<option value="5" <?=$arr_part[5]?>>5</option>
				</select>
				<button class="btn btn-default" type="submit" name="submit">送出</button>
			
			</p>

         
	     <?
		    //****************************************選擇問題數量
		    if(isset($_POST['submit']))
		    {
		    	for($i=1;$i<=$_POST['part'];$i++)       
		       {	
		            echo "<div class=\"col-xs-2\"><label class=\"control-label\">第".$i."部分:</label>" ;
					echo "
							<div class=\"form-group\">
							<div class=\"input-group\">
							<input type=\"text\" class=\"form-control quantity\" name=\"quantity[$i]\" value=\" \" style='width:70px' maxlength=\"3\">
								<div class=\"input-group-addon\" style='width:20px'>題</div>
							</div></div></div>";
				
		
		       }
			   echo " <br><button class=\"btn btn-default\" type=\"submit\" name=\"submit2\">送出</button><br>";
		    }
	
		 ?>
		</form>  <!--form2-->		
			
		<form name='form1' method='POST' id='form1' action=""  class="form-horizontal">
         <?
			
			if(isset($_POST['submit2']))
			{
			//********************取得每個部份的問題數量
			for($i=1;$i<=$part;$i++)    
				$quantity_value[$i]=$_POST['quantity'][$i];	
			$f=0;
			for($i=1;$i<=$part;$i++)   
			{    
				echo "<br>";
				
				echo "<table  class=\"table table-bordered\">"; 
				echo "<tr>";
				echo " <td width=\"66\">第</td>";
				echo " <td width=\"137\">問題類型</td>";
				echo " <td width=\"137\">題目(*矩陣題部分為矩陣題大題目)</td>";
				echo "</tr>";
				
					for($j=1;$j<=$quantity_value[$i];$j++)
					{
						//******************題號
						echo "<tr>";
						echo "<td >$j</td>";
						//******************問題種類
						echo "<td ><select name=\"select[$i][$j]\" id=\"type$f\" class=\"form-control\" >";               
						echo "<option value=\"1\" selected>單行文字</option>";
						echo "<option value=\"2\">多行文字</option>";
						echo "<option value=\"3\">下拉式選單</option>";
						echo "<option value=\"4\">多選題</option>";
						echo "<option value=\"5\">單選題</option>";
						echo "<option value=\"6\">矩陣題</option>";
						echo "</select> </td>";
						//******************題目 (最多填200個字)
						echo  "<td><div class=\"form-group col-xs-12\">
						<input type=\"text\" class=\"form-control check \" name=\"question[$i][$j]\"  >";  				   
						echo " </div></td></tr>";
						$f++;
						
					}
					echo "<br>";
			
				echo "</table>";
			}
			echo "<div class=\"text-right \" >";
			echo " <button class=\"btn btn-default\" type=\"submit\" name=\"B2\">下一步</button>";
			echo "</div>";
			}
			
			
		?>
		</form> <!--form1-->
									
							</div>	 <!--col-md-8--> 	
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
                    }
					
                }
            }
		}
    });
}); 
</script>
    

</body>

</html>