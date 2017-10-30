<?php session_start(); 

include("connect.php");
include("check.php");

$id=$_GET['ID'];  // 取得網址後面的ID 

//限管理者
$user=$_SESSION['Username'];
$sql="select * from member where identity='$user' and status='1'";
$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
$nrows=OCIFetchStatement($stmt,$results); 
if ($nrows==0)
{
	echo "<script language = JavaScript>";
	echo "alert(\"Permission deny! \");"; 
	echo "window.location.href='index.php'";
	echo "</script>"; 
	exit;
}

$sql1="select * from questionnaire where ID='$id' ";      
 $stmt1=ociparse($con,$sql1);     //取出資料固定格式
 ociexecute($stmt1,OCI_DEFAULT); 
 $nrows1 = OCIFetchStatement($stmt1,$results1); //算取出來的有幾筆
 $datetime= $results1['DATETIME'][0];
 $due= $results1['DUE'][0];


//****************************************************取出全部部分
$sqlpart="select distinct part from question where ID='$id' ";    
 $stmtpart=ociparse($con,$sqlpart);     
 ociexecute($stmtpart,OCI_DEFAULT); 
 $nrows_part = OCIFetchStatement($stmtpart,$resultspart); 
 
//****************************************************取出每個部分的題數 & type
 for($part=1;$part<=$nrows_part;$part++){
	$sqlt="select * from question where ID='$id' and PART='$part'";    
	$stmtt=ociparse($con,$sqlt);     
	ociexecute($stmtt,OCI_DEFAULT); 
	$nrows_no[$part] = OCIFetchStatement($stmtt,$resultst); 
	//*****************************************取出question table中的 distinct no
	$sql_no="select DISTINCT no  from question where ID='$id' and PART='$part'";
	$stmt_no=ociparse($con,$sql_no);
	ociexecute($stmt_no,OCI_DEFAULT);
	$nrows_qno[$part]= OCIFetchStatement($stmt_no,$results_qno);
	
	//*****************************************取出opt table中的distinct no	
	$sql_dno="select DISTINCT NO from opt where ID='$id' and PART='$part'";
	$stmt_dno=ociparse($con,$sql_dno);
	ociexecute($stmt_dno,OCI_DEFAULT);
	$nrows_dno [$part]= OCIFetchStatement($stmt_dno,$results_dno);
	
		for($no=1;$no<= $nrows_no[$part]; $no++){
			//*****************************************取出question table中的 type
			$sql_type="select DISTINCT type  from question where ID='$id' and PART='$part' and NO='$no'";
			$stmt_type=ociparse($con,$sql_type);
			ociexecute($stmt_type,OCI_DEFAULT);
			$nrows_t[$part]= OCIFetchStatement($stmt_type,$results_type);			
			$type[$part][$no] = $results_type['TYPE'][0];
			
			//*****************************************取出question table中的non	
			$sql_non="select * from question where NO='$no' and ID='$id' and PART='$part'";
			$stmt_non=ociparse($con,$sql_non);
			ociexecute($stmt_non,OCI_DEFAULT);
			$nrows_non [$part][$no]= OCIFetchStatement($stmt_non,$results_non);
			//*****************************************取出question table中 type=6 的Q_NUM
			if($type[$part][$no]==6){
				$type6_no="select DISTINCT Q_NUM from question where ID='$id' and PART='$part' and no='$no' and type='6' ";
				$stmt_t6=ociparse($con,$type6_no);
				ociexecute($stmt_t6,OCI_DEFAULT);
				$t6_no[$part][$no]= OCIFetchStatement($stmt_t6,$results_t6);
			}
			//*****************************************取出opt table中的num	
			$sql_num="select * from opt where NO='$no' and ID='$id' and PART='$part'";
			$stmt_num=ociparse($con,$sql_num);
			ociexecute($stmt_num,OCI_DEFAULT);
			$nrows_num [$part][$no]= OCIFetchStatement($stmt_num,$results_num);	
		}
}


if(isset($_POST["modify"]))
{	
		//******************************************取title & description & part_title & part_description
		$title=$_POST['title'];
		$description=$_POST['description'];
		$datetime=$_POST['datetime'];
		$due=$_POST['due'];
		
		for($i=0;$i<$nrows_part ;$i++){
			$part_title[$i]=$_POST['part_title'][$i];
			$part_description[$i]=$_POST['part_description'][$i];
		}
		
		for($part=1;$part<=$nrows_part;$part++){
			for($no=1;$no<= $nrows_no[$part]; $no++){
				//*************************************************************取ques的題目
				for($non=1;$non<= $nrows_non[$part][$no]; $non++){
					$ques_value[$part][$no][$non]=$_POST['ques'][$part][$no][$non];
					//**********************非矩陣題題目
					if($type[$part][$no]<6){								
						//echo "a:".$ques_value[$part][$no][$non]."<br>";
					}
					//**********************矩陣題題目
					else {							  						
						//echo "a:".$ques_value[$part][$no][$non]."<br>";
					}
				}
				//*************************************************************取opti的選項
				for($num=1;$num<=$nrows_num[$part][$no];$num++){
					if($type[$part][$no]>2 && $type[$part][$no]<6) {
						$opti_value[$part][$no][$num]=$_POST['opti'][$part][$no][$num];
						//**********************非矩陣題選項
						//if($type[$part][$no]<6){
						//  echo "b:".$opti_value[$part][$no][$num]."<br>";
						//	echo $part."<br>";
						//	echo $no."<br>";
						//	echo $num."<br>";
						//}
						//**********************矩陣題選項
						//else{
						//	echo "b:".$opti_value[$part][$no][$num]."<br>";
						//	echo $part."<br>";
						//	echo $no."<br>";
						//	echo $num."<br>";
						//}
					}
				}
			
			}
		}
		
		//**************************************update questionnaire table
		$update_ques_naire = "update questionnaire set TITLE='".$title."',DESCRIPTION='".$description."',DATETIME=$datetime, DUE=$due where id='$id'  ";
		$update_ques_naire =iconv("UTF-8","BIG5", $update_ques_naire);
		//echo $update_ques_naire."<br>";
			$stmtach = OCIPARSE($con,$update_ques_naire);
			if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
			{	
				ocirollback($con);            
				exit();
			}
			else ocicommit($con);
		//**************************************update description table
		$j=0;
		for($i=1;$i<=$nrows_part ;$i++){
			$update_des = "update description set PART_TITLE='".$part_title[$j]."',DESCRIPTION='".$part_description[$j]."' where id='$id' and part='$i' ";
			$update_des =iconv("UTF-8","BIG5", $update_des);
			//echo $update_des."<br>";
			$stmtach = OCIPARSE($con,$update_des);
			if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
			{	
				ocirollback($con);            
				exit();
			}
			else ocicommit($con);
			$j++;
		}
		//**************************************update question table
		for($part=1;$part<=$nrows_part ;$part++){
			for($no=1;$no<=$nrows_no[$part] ;$no++){
				for($non=1;$non<= $nrows_non[$part][$no]; $non++){
					$update_ques = "update question set QUESTION='".$ques_value[$part][$no][$non]."' where id='$id' and part='$part'  and no='$no' and non='$non' ";
					$update_ques =iconv("UTF-8","BIG5", $update_ques);
					//echo $update_ques."<br>";
					$stmtach = OCIPARSE($con,$update_ques);
					if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
					{	
						ocirollback($con);            
						exit();
					}
					else ocicommit($con);
				}
			}
		}
		
		//**************************************update opt table
		for($part=1;$part<=$nrows_part ;$part++){
			for($no=1;$no<=$nrows_no[$part] ;$no++){
				for($num=1;$num<= $nrows_num[$part][$no]; $num++){
					$update_opt = "update opt set OPTION_VALUE='".$opti_value[$part][$no][$num]."' where id='$id' and part='$part'  and no='$no' and num='$num'  ";
					$update_opt =iconv("UTF-8","BIG5", $update_opt);
					//echo $update_opt."<br>";
					$stmtach = OCIPARSE($con,$update_opt);
					if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
					{	
						ocirollback($con);            
						exit();
					}
					else ocicommit($con);
				}
			}
		}
		
		
/*$url = "index.php";

echo "<script type='text/javascript'>";
echo "window.location.href='$url'";
echo "</script>";*/
}//if(isset)

if(isset($_POST['delete']))
{
	
		//******************************************取得刪除欄位
		$part_ques=$_POST['part_ques'];
		$no_ques=$_POST['no_ques'];
		$non_ques=$_POST['non_ques'];
		$part_opt=$_POST['part_opt'];
		$no_opt=$_POST['no_opt'];
		$num_opt=$_POST['num_opt'];
		//**************************************delete opt table
		if($part_opt && $no_opt && $num_opt){
			$delete_opt = "delete from opt where id='$id' and part='$part_opt' and no='$no_opt' and num='$num_opt'  ";
			//echo $delete_opt."<br>";
			$stmtach = OCIPARSE($con,$delete_opt);
			if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
			{	
				ocirollback($con);            
				exit();
			}
			else ocicommit($con);
			//**********************************更新 OPT 題號
				for($part=1;$part<=$nrows_part ;$part++){
					for($no=1;$no<=$nrows_no[$part] ;$no++){
						if($type[$part][$no]>2 && $type[$part][$no]<6){
							if($num_opt< $nrows_num [$part][$no]){
								for($i=$num_opt; $i<$nrows_num [$part][$no]; $i++){
									$num_opt++;
									$update_num_opt = "update opt set NUM=$i  where id='$id' and part='$part_opt' and no='$no_opt' and num='$num_opt' ";
									//echo $update_num_opt."<br>";
									$stmtach = OCIPARSE($con,$update_num_opt);
									if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
									{	
									ocirollback($con);            
									exit();
									}
									else ocicommit($con);
								}
							}
						}
					}
				}
			
		}
		//**************************************delete question table
		if($part_ques && $no_ques){
			//**************矩陣題小題
			if($non_ques){
				$delete_ques = "delete from question where id='$id' and part='$part_ques' and no='$no_ques' and non='$non_ques' ";
				//echo $delete_ques."<br>";
				$stmtach = OCIPARSE($con,$delete_ques);
				if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
				{	
					ocirollback($con);            
					exit();
				}
				else ocicommit($con);
				//**********************************更新 矩陣題小題 題號 
				for($part=1;$part<=$nrows_part ;$part++){
					for($no=1;$no<=$nrows_no[$part] ;$no++){
						if($type[$part][$no]==6){
							if($non_ques<$results_t6['Q_NUM'][0]){
								for($i=$non_ques; $i<$results_t6['Q_NUM'][0]; $i++){
									$non_ques++;
									$update_non_ques = "update question set NON=$i  where id='$id' and part='$part_ques' and no='$no_ques' and non='$non_ques' ";
									//echo $update_non_ques."<br>";
									$stmtach = OCIPARSE($con,$update_non_ques);
									if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
									{	
									ocirollback($con);            
									exit();
									}
									else ocicommit($con);
								}
							}
						}
					}
				}
			}
			//**************	
			else {
				$delete_ques = "delete from question where id='$id' and part='$part_ques' and no='$no_ques'  ";
			//	echo $delete_ques."<br>";
				$stmtach = OCIPARSE($con,$delete_ques);
				if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
				{	
					ocirollback($con);            
					exit();
				}
				else ocicommit($con);
				//**********************************if type3~5
				for($part=1;$part<=$nrows_part ;$part++){
					for($no=1;$no<=$nrows_no[$part] ;$no++){
						if($type[$part][$no]>2 && $type[$part][$no]<6){
							$delete_ques_opt = "delete from opt where id='$id' and part='$part_ques' and no='$no_ques'  ";
							//echo $delete_ques_opt."<br>";
							$stmtach = OCIPARSE($con,$delete_ques_opt);
							if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
							{	
								ocirollback($con);            
								exit();
							}
							else ocicommit($con);
							//**********************************更新 OPT 題號	
								for($part=1;$part<=$nrows_part ;$part++){
									for($no=1;$no<=$nrows_dno [$part] ;$no++){	
											if($no_ques< $nrows_dno [$part]){
												for($i=$no_ques; $i<$nrows_dno [$part]; $i++){
													$old_nq=$i+1;
													$update_dno = "update opt set NO=$i  where id='$id' and part='$part_ques' and no='$old_nq'  ";
												//	echo $update_dno."<br>";
													$stmtach = OCIPARSE($con,$update_dno);
													if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
													{	
													ocirollback($con);            
													exit();
													}
													else ocicommit($con);
												}
											}	
									}
								}
						}
					}
				}
			
				//**********************************更新 題目 題號
				for($part=1;$part<=$nrows_part ;$part++){			
						if($no_ques<$nrows_no[$part]){
							for($i=$no_ques; $i<$nrows_no[$part]; $i++){
								$old_noq=$i+1;
								$update_no_ques = "update question set NO=$i  where id='$id' and part='$part_ques' and no='$old_noq' ";
								//echo $update_no_ques."<br>";
								$stmtach = OCIPARSE($con,$update_no_ques);
								if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
								{	
								ocirollback($con);            
								exit();
								}
								else ocicommit($con);
							}
						}	
				}
			}
		}
}



    
$id = $results1['ID'][$nrows1-1];
$style = $results1['STYLE'][$nrows1-1];
 

$sqldes="select * from description where ID='$id' order by PART ASC";    
 $stmtdes=ociparse($con,$sqldes); 
 ociexecute($stmtdes,OCI_DEFAULT); 
 $nrowsdes = OCIFetchStatement($stmtdes,$resultsdes); 


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
    <!-- Custom CSS -->    
    <link rel="stylesheet"  href="datepicker.css" />
	<script src="main.js">  </script>
	
	<!-- Datetimepicker -->
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">



	<!--連結樣式---------------------------------------------------------------------------------------------------------->
    <?php 	
    	echo"<link rel=\"stylesheet\" type=\"text/css\" href=\"css\Style$style.css\" />";	
    ?>
    
    <!-- Bootstrap Core JavaScript -->
    <script type="text/javascript" src="vendor/jquery/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="dist/js/bootstrapValidator.js"></script>

</head>

<body onLoad="document.forms.form.user_id.focus()">
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
			<div class="container-fluid">
				<form method='POST' name="form1" id="form" class="form-horizontal">
					<div class="span12">
						<div id="a1"><h2 style="font-weight: bolder;" class="inline">
   		<?php
				//標題-------------------------------------------------------------------------------------
	 				$title = $results1['TITLE'][0] ;
					$title =iconv("BIG5","UTF-8", $title);
					echo "<input type=\"text\" class=\"form-control\" name=\"title\" value=\"$title\" size=\"50\"/>";
			?>
			</h2></div></div>

		 <div class="span12">
				<div id="a3" >
   		<?php
				//描述語------------------------------------------------------------------
					$description = $results1['DESCRIPTION'][0];
					$description =iconv("BIG5","UTF-8", $description);
					echo "<table class=\"table table-bordered\">";
						echo "<tr class=\"success\"><td>";
							echo "<h4><input type='text' class=\"form-control\" name='description'  value='$description' /></h4>";
							echo "<h5><font color=\"red\">打 * 表示為必填</font></h5>";

					   echo "<div class=\"col-xs-3\">";
						echo "開放時間:&nbsp <input type='text' style='width:150px' class=\"form-control\" name='datetime'  value='$datetime' />";
						echo "</div>";
						 echo "<div class=\"col-xs-3\">";
						echo "截止時間:&nbsp <input type='text' style='width:150px' class=\"form-control\" name='due'  value='$due'  />";
						echo "</div>";
						

					echo "</td></tr>";
    			echo "</table>";
			?>
			
			</div></div>
		<?php
			
			for($p=0; $p<$nrows_part; $p++)
			{
				$part = $resultspart['PART'][$p];
				
				
				$sql2="select * from question where ID='$id' and PART='$part' order by NO ASC";
					 $stmt2=ociparse($con,$sql2); 
					 ociexecute($stmt2,OCI_DEFAULT); 
					 $nrows2 = OCIFetchStatement($stmt2,$results2); 
				
				$sqlparttitle="select * from description where ID='$id' order by PART ASC";    //取出全部部分
					 $stmtparttitle=ociparse($con,$sqlparttitle);     
					 ociexecute($stmtparttitle,OCI_DEFAULT); 
					 $nrowsparttitle = OCIFetchStatement($stmtparttitle,$resultsparttitle); 

				//部分-----------------------------------------------
                echo "<div id=\"a2\">";
					$part_title = $resultsparttitle['PART_TITLE'][$p];
					$part_title =iconv("BIG5","UTF-8", $part_title);
					echo " <div class=\"input-group\">";
					echo "<div class=\"input-group-addon\" style='width:30px'>部分".$part.". 標題</div>";
					echo "<input type='text' class=\"form-control\" name='part_title[$p]'  value='$part_title' style='width:300px'/> <br>";
					echo "</div>";
				echo "</div><br>";
				//指導語-------------------------------------------------
				echo"<div id=\"a2\">";
					$part_description = $resultsdes['DESCRIPTION'][$p] ;
					$part_description =iconv("BIG5","UTF-8", $part_description);
					echo " <div class=\"input-group\">";
					
					echo "<div class=\"input-group-addon\" style='width:30px'>部分".$part.". 指導語</div>";
	 				echo "<input type='text' class=\"form-control\" name='part_description[$p]'  value='$part_description' style='width:300px'/> <br>";
					echo "</div></div>";
			
				
				//題目區-----------------------------------------------
				echo "<div class=\"a4\">";
					echo "<p>";
							$i3=0;
							for($i=0;$i<$nrows2;$i++)                           
							{
								$no = $results2['NO'][$i] ;
								$non = $results2['NON'][$i] ;
								$ques = $results2['QUESTION'][$i] ;
								$ques =iconv("BIG5","UTF-8", $ques);
								$type = $results2['TYPE'][$i];
								$least = $results2['LEAST'][$i];
								$most = $results2['MOST'][$i];
								$other = $results2['OTHER'][$i];
								$br = $results2['BR'][$i];

							  	$sql="SELECT COUNT(NON) FROM question where NO='$no' and ID='$id' and PART='$part' order by NON ASC";
									$stmt=ociparse($con,$sql);
									ociexecute($stmt,OCI_DEFAULT);
									$nrows=OCIFetchStatement($stmt,$results); 
								
							  	$sql3="select * from opt where NO='$no' and ID='$id' and PART='$part' order by NUM ASC";
									$stmt3=ociparse($con,$sql3);
									ociexecute($stmt3,OCI_DEFAULT);
									$nrows3 = OCIFetchStatement($stmt3,$results3); 
							
						
								//矩陣題選項 非常滿意etc.-------------------------------------------
								
									if ($results['COUNT(NON)'][0]>1 && $non==1)      
									{
										echo "<div class=\"col-lg-7 col-lg-offset-5\">";
										
										if($nrows3==2){
											for ($j=0;$j<$nrows3;$j++)
											{  
												$num = $results3['NUM'][$j] ;
												$opti = $results3['OPTION_VALUE'][$j] ;   
												$opti =iconv("BIG5","UTF-8", $opti);
												echo "<div class=\"col-md-6\">";
												
												echo " <center>";
												echo "<input type='text' class=\"form-control\" name='opti[$part][$no][$num]'  value='$opti' style='width:100px'/> <br>";
												echo "</center>";


												//	echo "<center>$num.$opti </center>";
												echo "</div>";
											}
										}//if(2)

										if($nrows3==3){
											for ($j=0;$j<$nrows3;$j++)
											{  
												$num = $results3['NUM'][$j] ;
												$opti = $results3['OPTION_VALUE'][$j] ;  
												$opti =iconv("BIG5","UTF-8", $opti);
												echo "<div class=\"col-md-4\">";

												echo " <center>";
												echo "<input type='text' class=\"form-control\" name='opti[$part][$no][$num]'  value='$opti' style='width:100px'/> <br>";
												echo "</center>";

												echo "</div>";
											}
										}//if(3)

										if($nrows3==4){
											for ($j=0;$j<$nrows3;$j++)
											{  
												$num = $results3['NUM'][$j] ;
												$opti = $results3['OPTION_VALUE'][$j] ; 
												$opti =iconv("BIG5","UTF-8", $opti);
												echo "<div class=\"col-md-3\">";

												echo " <center>";
												echo "<input type='text' class=\"form-control\" name='opti[$part][$no][$num]'  value='$opti' style='width:100px'/> <br>";
												echo "</center>";

												echo "</div>";
											}
										}//if(4)

										if($nrows3==5){
											for ($j=0;$j<$nrows3;$j++)
											{  
												$num = $results3['NUM'][$j] ;
												$opti = $results3['OPTION_VALUE'][$j] ;  
												$opti =iconv("BIG5","UTF-8", $opti);
												
												if($j==0||$j==4)
													echo "<div class=\"col-md-3\">";
												if($j==1||$j==2||$j==3)
													echo "<div class=\"col-md-2\">";

													echo " <center>";
													echo "<input type='text' class=\"form-control\" name='opti[$part][$no][$num]'  value='$opti' style='width:100px'/> <br>";
													echo "</center>";
												
													echo "</div>";
											}
										}//if(5)
										echo "</div>"; 
									  }//if && 
								  
								
								//題目---------------------------------------------------------------------------------------------------------------ok
								echo "<div class=\"form-group\">";
								
									if ($results['COUNT(NON)'][0]==1 && $non==1){ 
								  	
										echo "<div class=\"col-lg-12 col-lg-offset-0\">
												<label class=\"control-label\">";
											if($least==1){
												
													echo " <div class=\"input-group\">";
													echo "<div class=\"input-group-addon\" style='width:30px'><font color=\"red\">* </font>$no.</div>";
													echo "<input type='text' class=\"form-control\" name='ques[$part][$no][$non]'  value='$ques' style='width:500px'/> <br>";
													echo "</div>";
											}
								  			else 
										    {
													echo " <div class=\"input-group\">";
													echo "<div class=\"input-group-addon\" style='width:30px'>$no.</div>";
													echo "<input type='text' class=\"form-control\" name='ques[$part][$no][$non]'  value='$ques' style='width:500px'/> <br>";
													echo "</div>";
										     }
										echo "</label>
											</div>";
								   	}
								 	else{  //矩陣題
									$sqlm="SELECT * FROM question where NO='$no[$t]' and ID='$id' and PART='$part' order by NON ASC"; //sort the matrix question
										 $stmtm=ociparse($con,$sqlm);
										 ociexecute($stmtm,OCI_DEFAULT);
										 $nrowsm=OCIFetchStatement($stmtm,$resultsm); 
										
										$non = $resultsm['NON'][$i3];
										$ques = $resultsm['QUESTION'][$i3];
										$ques  =iconv("BIG5","UTF-8", $ques );
										$least = $resultsm['LEAST'][$i3];
										$i3++;
										echo "<div class=\"form-group\">";
										echo "<div class=\"col-md-5\">
												<label class=\"control-label\">";
											
											if($least==1){
												
												echo " <div class=\"input-group\">";
												echo "<div class=\"input-group-addon\" style='width:30px'><font color=\"red\">* </font>$no-$non.</div>";
												echo "<input type='text' class=\"form-control\" name='ques[$part][$no][$non]'  value='$ques' /> <br>";
												echo "</div>";
											
											}
											else {
												echo " <div class=\"input-group\">";
												echo "<div class=\"input-group-addon\" style='width:30px'>$no-$non.</div>";
												echo "<input type='text' class=\"form-control\" name='ques[$part][$no][$non]'  value='$ques' /> <br>";
												echo "</div>";				
											}
										
										echo "</label>
											 </div> ";

									//matrix--------------------------------------------------------------------------------------

										echo "<div class=\"col-md-7\">";
										if($least>=1){
											if($nrows3==2){
												for($ma=1;$ma<=$nrows3;$ma++){
													echo "<div class=\"col-md-6\">";
													  echo "<center>
															<input 
																name=\"matrix[$part][$no][$non]\" 
																type=\"radio\" 
																value=\"$ma\"
																class=\"matrix\"/>
															</center>
													 </div>";
												}//for(2)
											}//if(2)

											if($nrows3==3){
												for($ma=1;$ma<=$nrows3;$ma++){
													echo "<div class=\"col-md-4\">";
													  echo "<center>
															<input 
																name=\"matrix[$part][$no][$non]\" 
																type=\"radio\" 
																value=\"$ma\"
																class=\"matrix\"/>
															</center>
													 </div>";
												}//for(3)
											}//if(3)

											if($nrows3==4){
												for($ma=1;$ma<=$nrows3;$ma++){
													echo "<div class=\"col-md-3\">";
													  echo "<center>
															<input 
																name=\"matrix[$part][$no][$non]\" 
																type=\"radio\" 
																value=\"$ma\"
																class=\"matrix\"/>
															</center>
													 </div>";
												}//for(4)
											}//if(4)

											if($nrows3==5){
												for($ma=1;$ma<=$nrows3;$ma++){
													if($ma==1||$ma==5)
														echo "<div class=\"col-md-3\">";
													if($ma==2||$ma==3||$ma==4)
														echo "<div class=\"col-md-2\">";
													  
													  echo "<center>
															<input 
																name=\"matrix[$part][$no][$non]\" 
																type=\"radio\" 
																value=\"$ma\"
																class=\"matrix\"/>
															</center>
													 </div>";
												}//for(5)
											}//if(5)

										}//if(least)
										else{
											if($nrows3==2){
												for($ma=1;$ma<=$nrows3;$ma++){
													echo "<div class=\"col-md-6\">";
													  echo "<center>
															<input 
																name=\"matrix[$part][$no][$non]\" 
																type=\"radio\" 
																value=\"$ma\"/>
															</center>
													 </div>";
												}//for(2)
											}//if(2)

											if($nrows3==3){
												for($ma=1;$ma<=$nrows3;$ma++){
													echo "<div class=\"col-md-4\">";
													  echo "<center>
															<input 
																name=\"matrix[$part][$no][$non]\" 
																type=\"radio\" 
																value=\"$ma\"/>
															</center>
													 </div>";
												}//for(3)
											}//if(3)

											if($nrows3==4){
												for($ma=1;$ma<=$nrows3;$ma++){
													echo "<div class=\"col-md-3\">";
													  echo "<center>
															<input 
																name=\"matrix[$part][$no][$non]\" 
																type=\"radio\" 
																value=\"$ma\"/>
															</center>
													 </div>";
												}//for(4)
											}//if(4)

											if($nrows3==5){
												for($ma=1;$ma<=$nrows3;$ma++){
													if($ma==1||$ma==5)
														echo "<div class=\"col-md-3\">";
													if($ma==2||$ma==3||$ma==4)
														echo "<div class=\"col-md-2\">";
													  
													  echo "<center>
															<input 
																name=\"matrix[$part][$no][$non]\" 
																type=\"radio\" 
																value=\"$ma\"/>
															</center>
													 </div>";
												}//for(5)
											}//if(5)

										}//else
										
									echo "</div> </div>";  //col-md-7
									}//else
								 
								 //選擇題的選項--------------------------------------------------------------------------------------------------------------
								 	if ($results['COUNT(NON)'][0]==1)             
									{
										if($br==0)
											echo "<br>";

										for ($j=0;$j<$nrows3;$j++)
										{ 
											$num = $results3['NUM'][$j] ;
											$opti = $results3['OPTION_VALUE'][$j] ;
											$opti  =iconv("BIG5","UTF-8", $opti );

											//單選題-----------------------------------------------------------------------------------------------------------
											if($most==1){                
												if($type==5){           //單選按鈕-------------------------ok
													if($br==1){
														echo "<div class=\"radio\">";}
														  echo "<label class=\"radio-inline\" >";
															echo "<input name=\"radio[$part][$no]\" type=\"radio\" value='$num' /> ";
															echo   "$num.<input type='text' name='opti[$part][$no][$num]'  value='$opti' />";
															echo   "</label>";
													if($br==1)
														echo "</div>";

													if($other==1&&$j==$nrows3-1){
														$num=$num+1;
														if($br==1){
															echo "<div class=\"radio\">";}
																 echo "<label class=\"radio-inline\">
																			<input name=\"radio[$part][$no]\" type=\"radio\" value='$num' />   $num.其他:
																			<input type=\"text\" 
																					name=\"textfield[$part][$no]\" 
																					id=\"textfield[$part][$no]\"/>
																		</label>";
														if($br==1)
															echo "</div>";
													}//if(other)
												}
												else if($type==3){      //單選下拉選單--------------------------------------------------------------ok
													if($j==0)
														echo "<strong>下拉式:</strong>&nbsp&nbsp";
													if($num!=$nrows3)
														echo "<input type='text' name='opti[$part][$no][$num]'  value='$opti' />&nbsp&nbsp";
													else
														echo "<input type='text' name='opti[$part][$no][$num]'  value='$opti' />";
												}//else if
											  else 
												 echo "default";
											}//if($most)
											//多選-------------------------------------------------------------------------------------------------------------------------------
											else{                  
												if($type==4){        //多選核取方塊--------------------ok
													if($br==1){
														echo "<div class=\"checkbox\">";}
															echo "<label class=\"checkbox-inline\">
																	<input name=\"checkbox[$part][$no][$num]\" type=\"checkbox\" value='$num'/>  $num.<input type='text' name='opti[$part][$no][$num]'  value='$opti' />
																  </label>";
													  if($br==1){
														  echo "</div>";}
											
													 if($other==1&&$j==$nrows3-1){
															$num=$num+1;
															if($br==1){
																echo "<div class=\"checkbox\">";}
																	echo "<label class=\"checkbox-inline\">
																			<input name=\"checkbox[$part][$no][$num]\" type=\"checkbox\" value='$num' />   $num.其他: 
																			<input type=\"text\" 
																				   name=\"textfield[$part][$no]\" 
																				   id=\"textfield[$part][$no]\" /> 
																		  </label>";
															if($br==1){
																echo "</div>";}
													  }//if(other)
												}//if(type=4)

											  }//else
									  } //for(j) 
										 
										
								  }//if(count)

								//textfield-----------------------------------------------------------------------------------------ok
								  if($type==1){
									echo"<input type=\"text\" 
												name=\"textfield[$part][$no]\" 
												id=\"textfield[$part][$no]\" 
												class=\"form-control\" 
												/>";
									
									echo "<font color=\"red\"><h6>(字數限 $most 字以內)</h6></font>";
								  }

								//textarea---------------------------------------------------------------------------------------------ok
								  if($type==2){
									echo "<textarea name=\"textarea[$part][$no]\" 
													id=\"textarea[$part][$no]\"	
													class=\"form-control\" 
													>
										   </textarea>";
									
									echo "<font color=\"red\"><h6>(字數限 $most 字以內)</h6></font>";
								  }
								
							echo "</div>";	
							}//for(i)
							echo "</p>";
						  }//for(part)
						   echo "<p class=\"help-block\"></p>";

					
 
							echo
							" 
							刪除題目: 第<input type=\"text\"  name=\"part_ques\" value=\"\" size=\"3\" /><span class=\"help-inline\"> 部分 
								  第<input type=\"text\" name=\"no_ques\" value=\"\" size=\"3\"/> 題
								  第<input type=\"text\" name=\"non_ques\" value=\"\" size=\"3\" placeholder=\"矩陣題\"/> 小題
							<br><br> 
							刪除選項: 第<input type=\"text\"  name=\"part_opt\" value=\"\" size=\"3\"/> 部分 
								  第<input type=\"text\"  name=\"no_opt\" value=\"\" size=\"3\"/> 題
								  第<input type=\"text\"  name=\"num_opt\" value=\"\" size=\"3\"/> 小題
							<br><br>
							<div class=\"text-right \" >
								<button type=\"submit\" class=\"btn btn-success\" name=\"delete\" onclick=\"check()\">確定刪除</button>
	
								<button type=\"submit\" class=\"btn btn-primary\" name=\"modify\" > 完成</button>
							</div>
						    ";
						
                         
				?>
						
						
</div>
</div>
</div>
</div>
                
              </form>
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

<script language="javascript"> 
function check() {
    alert("刪除成功!"); 
}
</script>


</body>
</html>