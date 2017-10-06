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
date_default_timezone_set('Asia/Taipei');

$id=$_GET['ID'];  // 取得網址後面的ID #  
$_SESSION['qid']=$id;

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
	echo "<script language = JavaScript>";
	echo "alert(\"此問卷已截止！\");";
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;
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
	echo "<script language = JavaScript>";
	echo "alert(\"您已填過此問卷，謝謝您的參與！\");"; 
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;
}
//檢查結束
//***************************************************************************

//用於後面取出各部分問題的sql
$sql1="select * from questionnaire where ID='$id' ";      
 $stmt1=ociparse($con,$sql1);     //取出資料固定格式
 ociexecute($stmt1,OCI_DEFAULT); 
 $nrows1 = OCIFetchStatement($stmt1,$results1); //算取出來的有幾筆

$sqlt="select * from question where ID='$id' ";    //取出全部問題
 $stmtt=ociparse($con,$sqlt);     
 ociexecute($stmtt,OCI_DEFAULT); 
 $nrowst = OCIFetchStatement($stmtt,$resultst); 

$sqlpart="select distinct part from question where ID='$id' ";    //取出全部部分
 $stmtpart=ociparse($con,$sqlpart);     
 ociexecute($stmtpart,OCI_DEFAULT); 
 $nrowspart = OCIFetchStatement($stmtpart,$resultspart); 

//include("fillin_insert_DB.php");
    
$id = $results1['ID'][$nrows1-1];
$style = $results1['STYLE'][$nrows1-1];
$sqldes="select * from description where ID='$id' order by PART ASC";    
 $stmtdes=ociparse($con,$sqldes); 
 ociexecute($stmtdes,OCI_DEFAULT); 
 $nrowsdes = OCIFetchStatement($stmtdes,$resultsdes); 

?>
<!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />   
    <!-- Custom CSS -->   	
	<link rel="stylesheet" type="text/css" href="css/Style<?=$style?>.css" />
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

		   <div id="panel-calendar" class="panel panel-primary dd">			
				<!--<div class="panel-body">-->
					<div class="container-fluid">
							<div class="col-md-12">
				<div id="a1"><h3 style="font-weight: bolder;" class="inline">
   		<?php
				//標題-------------------------------------------------------------------------------------
				for($i=0; $i<$nrows1 ;$i++) 
    			{  
	 				$title = $results1['TITLE'][$i] ;
					$title = iconv("BIG5","UTF-8",$title);					
					echo "$title";
    			}
			?>
			</h3></div></div>  

		 <div class="span12">
				<div id="a3">
   		<?php
		//描述語------------------------------------------------------------------
				$description = $results1['DESCRIPTION'][0];
				$description = iconv("BIG5","UTF-8",$description);
				if($description!=NULL)
					echo "<table class=\"table table-bordered\">
							<tr class=\"success\">
								<td><h5>$description</h5></td>
							</tr>
						  </table>";

				echo "<h6><font color=\"red\">打 * 表示為必填</font></h6>";
			?>
			</div></div>	
		<?php
		echo"<form method='POST' name=\"form\" id=\"form\" class=\"form-horizontal\" action=\"q_fillin_insert_DB.php\">";

		$t=0;
			
			for($p=0; $p<$nrowspart; $p++)
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
                if ($resultsparttitle['PART_TITLE'][$p]!=NULL)
				{
					echo "<div class=\"a2 col-lg-12 col-md-12 col-xs-12 col-sm-12\"><h3>";
						$part_title = $resultsparttitle['PART_TITLE'][$p];
						$part_title = iconv("BIG5","UTF-8",$part_title);	
						echo "$part_title";
					echo "</h3></div>";
				}
				//指導語-------------------------------------------------
				if ($resultsdes['DESCRIPTION'][$p]!=NULL)
				{
					echo"<div class=\"a3 col-lg-12 col-md-12 col-xs-12 col-sm-12\">";
					$des = $resultsdes['DESCRIPTION'][$p] ;
					$des = iconv("BIG5","UTF-8",$des);			
					echo "$des <br>";
					echo "</div>";
				}
				
				//題目區-----------------------------------------------
					echo "<p>";
					$i4=0;
					$topic=0;
					for($i=0;$i<$nrows2;$i++)                           
					{
						$no[$t] = $results2['NO'][$i] ;
						$non[$t] = $results2['NON'][$i] ;
						$ques = $results2['QUESTION'][$i] ;
						$ques = iconv("BIG5","UTF-8",$ques);
						$type[$t] = $results2['TYPE'][$i];
						$least[$t] = $results2['LEAST'][$i];
						$most[$t] = $results2['MOST'][$i];
						$other[$t] = $results2['OTHER'][$i];
						$olimit[$t] = $results2['OTHER_NUM'][$i];
						$orequire[$t] = $results2['OTHER_REQUIRE'][$i];
						$br = $results2['BR'][$i];

						if ($i%2==1)$bg="bg";
						else $bg="";
						

						$sql3="select * from opt where NO='$no[$t]' and ID='$id' and PART='$part' order by NUM ASC";   //選項
							$stmt3=ociparse($con,$sql3);    
							ociexecute($stmt3,OCI_DEFAULT);
							$nrows3 = OCIFetchStatement($stmt3,$results3); 
							
						if($type[$t]==6){
							$sql="SELECT * FROM question where NO='$no[$t]' and ID='$id' and PART='$part' order by NON ASC";  //sort matrix questions
								$stmt=ociparse($con,$sql);
								ociexecute($stmt,OCI_DEFAULT);
								$nrows=OCIFetchStatement($stmt,$results);
							
							$non[$t]=$results['NON'][$i4];
							$ques = $results['QUESTION'][$i4] ;
							$ques = iconv("BIG5","UTF-8",$ques);
							$least[$t] = $results['LEAST'][$i4];
							$i4++;
						}//if(type=6)
					
				
						//矩陣題question + 選項(非常滿意etc.)--------------------------------------------
							if($type[$t]==6 && $non[$t]==0)
							{								
								echo "<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">
										<div class=\"form-group\">
											<div class=\"col-xs-5 col-sm-5 col-md-5 col-lg-5\">
												<label class=\"control-label\">
												<p class=\"text-left story3\">$no[$t]. $ques</p>
												</label>
											</div>";

								echo "<div class=\"col-xs-7 col-sm-7 col-md-7 col-lg-7\">";	
								//依矩陣題選項數目決定欄位寬度
								switch($nrows3)
								{
									case 2 : $col_num="6";break;
									case 3 : $col_num="4";break;
									case 4 : $col_num="3";break;									
									default :$col_num="1-5";
								}								
								for ($j=0;$j<$nrows3;$j++)
								{  
									$num = $results3['NUM'][$j] ;
									$opti = $results3['OPTION_VALUE'][$j] ; 
									$opti = iconv("BIG5","UTF-8",$opti);
									echo "<div class=\"col-xs-$col_num col-md-$col_num col-lg-$col_num col-sm-$col_num\">											
												<center><label class=\"control-label\">
                                                <p class=\"text-left story4\">$opti </p></label></center>
											</div>";
								}
								echo "</div>";
							    $topic=1;
							}//if(type=6 non=0)					
						  
						
						//題目-----------------------------------------------------------------------------------
							if ($type[$t]!=6 && $non[$t]==1)//非矩鎮題 (type<>6且 non一定==1)
							{ 
								echo "<div class=\"form-group col-xs-12 col-sm-12 col-md-12 col-lg-12\">
										<label class=\"control-label\">";
									if($least[$t]>=1)//必填
										echo "<p class=\"text-left story\"><font color=\"red\">* </font>$no[$t]. $ques</p>";
									else 
										echo "<p class=\"text-left story\">$no[$t]. $ques</p>"; 
								echo "</label>";
							}
							else
							{
								if($type[$t]==6 && $non[$t]!=0)//矩陣題的小題
								{ 
										echo "<div class=\"form-group $bg\">
											<div class=\"col-xs-5 col-sm-5 col-md-5 col-lg-5\">
											<label class=\"control-label\">";
										if($least[$t]>=1)//必填
											echo "<p class=\"text-left story\"><font color=\"red\">* </font>$no[$t]-$non[$t]. $ques</p>";
										else 
											echo "<p class=\"text-left story\">$no[$t]-$non[$t]. $ques</p>";             
									echo "</label>
										 </div>";

									//matrix--------------------------------------------------------------------
									echo "<div class=\"col-xs-7 col-sm-7 col-md-7 col-lg-7\">";
									if($least[$t]>=1) $matrix_class="class=\"matrix$t\"";
									else  $matrix_class="";
										
									for($ma=1;$ma<=$nrows3;$ma++)
									{
										echo "<div class=\"col-xs-$col_num col-md-$col_num col-lg-$col_num col-sm-$col_num\">";
										echo "  <center> <label class=\"control-label\">
                                                    <p class=\"text-left story\">
												    <input 
													name=\"matrix[$part][$no[$t]][$non[$t]]\" 
													type=\"radio\" 
													value=\"$ma\"
													$matrix_class />
													</p>
													</label>
												</center>
											</div>";
									}
									
								echo "</div>";  //col-md-7 

								if($topic==1){
									if($non[$t]==$nrows-1)    
										echo "</div>";  //改變div的排版
								}else{
									if($non[$t]==$nrows)    
										echo "</div>";  //改變div的排版
								}								

							}//if(type==6 non!=0)
						 }//else
							

						 //選擇題的選項------------------------------------------------------------------------
							if($type[$t]==3 || $type[$t]==4 || $type[$t]==5){
								if($br==0)
									echo "<br>";

								for ($j=0;$j<$nrows3;$j++)
								{ 
									$num = $results3['NUM'][$j] ;
									$opti = $results3['OPTION_VALUE'][$j] ;
									$opti = iconv("BIG5","UTF-8",$opti);
									
									//單選題-------------------------------------------------------------------
									if($most[$t]==1){                
										if($type[$t]==5){           //單選按鈕------------------------
											if($br==1)
												echo "<br>";
											
											if($least[$t]==1){	  
												echo "<label class=\"radio-inline\" >
														<input 
															name=\"radio[$part][$no[$t]]\" 
															type=\"radio\" 
															value='$num' 
															class=\"radio$t\"
															id=\"radio$part$no[$t]$num\"/>  $opti
													  </label>";
											}//if(least=1)
											else{	  
												echo "<label class=\"radio-inline\" >
														<input 
															name=\"radio[$part][$no[$t]]\" 
															type=\"radio\" 
															value='$num'
															id=\"radio$part$no[$t]$num\"/>  $opti
													  </label>";
											}//else

											if($other[$t]==1&&$j==$nrows3-1){
												$num=$num+1;
												if($br==1)
													echo "<br>";
												 if($least[$t]==1){
													 echo "<label class=\"radio-inline\">
																<input name=\"radio[$part][$no[$t]]\" 
																		type=\"radio\" 
																		value='$num' 
																		class=\"radio$t\"
																		id=\"checkother$t\"/>   其他:
																</label>
																<input type=\"text\" 
																		name=\"textfield[$part][$no[$t]]\" 
																		id=\"other$t\"
																		disabled=\"disabled\"/>
																<font color=\"red\"><span style=\"font-size:small;\">(字數限 $olimit[$t] 字以內)</span></font>";
												 }//if(least)
												 else{	  
													echo "<label class=\"radio-inline\" >
															<input 
																name=\"radio[$part][$no[$t]]\" 
																type=\"radio\" 
																value='$num'
																id=\"checkother$t\"/>  其他:
														  </label>
															<input type=\"text\" 
																name=\"textfield[$part][$no[$t]]\" 
																id=\"other$t\"
																disabled=\"disabled\"/>
															<font color=\"red\"><span style=\"font-size:small;\">(字數限 $olimit[$t] 字以內)</span></font>";
												}//else

											}//if(other)
										}
										//單選下拉選單-----------------------------------------------------------
										else if($type[$t]==3 && $least[$t]==1){      
											if($j==0)
												echo "<select name=\"select[$part][$no[$t]]\" class=\"form-control request\"><option value=\"\">請選擇</option>";
											if($num!=$nrows3)
												echo "<option value=\"$num\">$opti</option>";
											else
												echo "<option value=\"$num\">$opti</option></select>";
										}
										else if($type[$t]==3 && $least[$t]==0){      
											if($j==0)
												echo "<select name=\"select[$part][$no[$t]]\" class=\"form-control\"><option value=\"\">請選擇</option>";
											if($num!=$nrows3)
												echo "<option value=\"$num\">$opti</option>";
											else
												echo "<option value=\"$num\">$opti</option></select>";
										}//else if
									  else 
										 echo "default";
									}//if($most)
									//多選-----------------------------------------------------------------------
									else{                  
										if($type[$t]==4){        //多選核取方塊--------------------
											if($br==1)
												echo "<br>";
										
											echo "<label class=\"checkbox-inline\">
															<input name=\"checkbox[$part][$no[$t]][$num]\" 
															type=\"checkbox\" 
															value='$num'
															class=\"checkbox$t\"/>  $opti
													   </label>";
											 if($other[$t]==1&&$j==$nrows3-1){
													$num=$num+1;
													if($br==1)
														echo "<br>";
														echo "<label class=\"checkbox-inline\">
																<input name=\"checkbox[$part][$no[$t]][$num]\" 
																	   type=\"checkbox\" 
																	   value='$num' 
																	   class=\"checkbox$t\"
																	   id=\"checkother$t\"
																	   />  其他:</label>
																<input type=\"text\" 
																	   name=\"textfield[$part][$no[$t]]\"
																	   id=\"other$t\"
																	   disabled=\"disabled\"/> 
																<font color=\"red\"><span style=\"font-size:small;\">(字數限 $olimit[$t] 字以內)</span></font>";
												  }//if(other)
										}//if(type=4)

									  }//else
							  } //for(j) 
						}//if(type=3.4.5)

						//textfield-------------------------------------------------------------------------------
						  if($type[$t]==1){
							if($least[$t]>=1){
								echo"<input type=\"text\" 
										name=\"textfield[$part][$no[$t]]\" 
										id=\"request$t\" 
										class=\"form-control\"
										/>";
							}
							else
								echo"<input type=\"text\" 
										name=\"textfield[$part][$no[$t]]\" 
										id=\"textfield_word$t\" 
										class=\"form-control \" 
										/>";
							
							if($most[$t]>1)
								echo "<font color=\"red\"><h6>(字數限 $most[$t] 字以內)</h6></font>";
						  }

						//textarea-------------------------------------------------------------------------------
						  if($type[$t]==2){
							  if($least[$t]>=1){
								  echo "<textarea name=\"textarea[$part][$no[$t]]\" 
												  id=\"request$t\"	
												  class=\"form-control \"></textarea>";
							   }
							  else
									echo "<textarea name=\"textarea[$part][$no[$t]]\" 
													id=\"textarea_word$t\"	
													class=\"form-control\"></textarea>";

							if($most[$t]>1)
								echo "<font color=\"red\"><h6>(字數限 $most[$t] 字以內)</h6></font>";
						  }
						$t=$t+1;
						
						if($type[$t]!=6)
						{  echo "</div>";   } //form-group col-lg-12
					}//for(i)
					echo "</p>";
				  }//for(part)
				   echo "<p class=\"help-block\"></p>";

						  //button-------------------------------------------------------------------------------
						   echo 
						   "<div class=\"form-group\">
                            <div class=\"col-lg-7 col-lg-offset-5 col-md-7 col-md-offset-5 col-sm-7 col-sm-offset-5 col-xs-7 col-xs-offset-5\">
                                <input type=\"hidden\" name=\"qid\" value=\"$id\">
								<button type=\"submit\" class=\"btn btn-primary\" name=\"send\" id=\"send\">送出問卷</button>
                            </div>
						  </div>";

						echo"</form>";
						
                       
				?>
						
		
</div>
</div>
</div>
</div>
        </div>         

            </div>

        </div>
</div>
    </div>
   <? include("footer.html");?>


	<!-- The modal -->
   <div class="modal fade bs-example-modal-sm" id="erroralert" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-vertical-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="modal-title" id="myModalLabel" style="color:#800000">《提醒》</h5>
                </div>
                <div class="modal-body">
					<div class="text-center welcome"></div>
                </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-xs btn-primary" data-dismiss="modal">關閉</button>				
				</div>
            </div>
        </div>
    </div>
    <!-- /.The modal -->

<script type="text/javascript">

$(document).ready(function() {
	
    $('#form')
	  .bootstrapValidator({
		
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon',       
            //invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },   
        fields: {
			<? for($boot=0;$boot<$t;$boot++)
			{ 
				if($least[$boot]>=1)
				{
					if($type[$boot]==1||$type[$boot]==2)
					{ ?>
						request<? echo $boot; ?>: 
						{
							selector:'#request<?echo $boot; ?>',
							validators: {
								notEmpty: {
									message: '此題為必填問題!!'
								},
								stringLength: {
									min: <? echo "$least[$boot]"; ?>,
									max: <? echo "$most[$boot]"; ?>,
									message: '答案最多<? echo "$most[$boot]"; ?>字，最少<? echo "$least[$boot]"; ?>字'
									
								},  
							}
						},
					<? }//if(type=1.2)
				}//if(least>=1)
				if($type[$boot]==1 && $least[$boot]==0)
				{?>
					textfield_word<? echo $boot; ?>: 
					{
						selector:'#textfield_word<?echo $boot; ?>',
						validators: {
							stringLength: {
								min: <? echo "$least[$boot]"; ?>,
								max: <? echo "$most[$boot]"; ?>,
								message: '答案最多<? echo "$most[$boot]"; ?>字'
							},  
						}
					},
				<?}//if(type=1 least=0)
				if($type[$boot]==2 && $least[$boot]==0)
				{?>
					textarea_word<? echo $boot; ?>: 
					{
						selector:'#textarea_word<?echo $boot; ?>',
						validators: {
							stringLength: {
								min: <? echo "$least[$boot]"; ?>,
								max: <? echo "$most[$boot]"; ?>,
								message: '答案最多<? echo "$most[$boot]"; ?>字'
							},  
						}
					},
				<?}//if(type=2 least=0)
				if($type[$boot]==3 && $least[$boot]>=1)
				{?>
					request: 
					{
						selector:'.request',
						validators: {
							notEmpty: {
								message: '此題為必填問題!!'
							}, 
						}
					},
				<? }//if(type=3)
				if($type[$boot]==6 && $least[$boot]>=1)
				{ ?>
					matrix<?echo $boot; ?>: 
					{
						selector:'.matrix<?echo $boot; ?>',
						validators: {
							notEmpty: {
								message: '此題為必填問題!!'
							}
						}
					},
			<? }//if(type=6)
				if($type[$boot]==5 && $least[$boot]>=1)
				{?>
					radio<?echo $boot; ?>: 
					{
						selector:'.radio<?echo $boot; ?>',
						validators: {
							notEmpty: {
								message: '此題為必填問題!!'
							}
						}
					},
			<?}//if(type=5)
				if($type[$boot]==4)
				{?>
					checkbox<? echo $boot; ?>: 
					{
						selector:'.checkbox<? echo $boot; ?>',
						validators: {
							choice: {
								min: <? echo "$least[$boot]"; ?>,
								max: <? echo "$most[$boot]"; ?>,
								message: '請選擇 <? echo "$least[$boot]"; ?>個 至 <? echo "$most[$boot]"; ?>個 選項'
							}
						}
					},
			 <?}//if(type=4)
				 if($other[$boot]==1)
				 {?>
					other<? echo $boot; ?>: 
					{
						selector:'#other<?echo $boot; ?>',
						enabled: false,
						validators: {
							notEmpty:{
								message: '其他格為必填!!'
							},
							stringLength: {
								min: 1,
								max: <? echo "$olimit[$boot]"; ?>,
								message: '答案最多 <? echo "$olimit[$boot]"; ?> 字'
							},  
						}
					},  
				 <?}//if(other=1)
			}//for(i) 
	?>
        }
		})
		//remove the sucess css and always enable the submit button
        .on('status.field.bv', function(e, data) {
			data.element.parents('.form-group').removeClass('has-success');
			data.bv.disableSubmitButtons(false);
		})
		
		.on('added.field.bv', function(e, data) {
			console.log('Added element --> ', data.field, data.element, data.options);
		})
		
		//alert for error happening
		.on('error.form.bv', function(e, data) {
			//alert('部分題目填答不完整\r\r請依提示填答後再送出'); //先改以alert提醒
			$('#erroralert')
                .find('.welcome').html("<span style='color:blacke;font-size:13px'>部分題目填答不完整，請依提示填答再送出！</span>").end()
                .modal('show');

		})

			
		//Submit using Ajax -- add by boblee 104/03/31
		.on('success.form.bv', function(e) {
            var postData = $(this).serializeArray();
			var formURL = $(this).attr("action");
			$.ajax(
			{
				beforeSend: function (request) {
						if(!confirm('是否確定送出問卷？')) return false;
				},
				url : formURL,
				type: "POST",
				data : postData,
				success:function(data, textStatus, jqXHR) 
				{
					var msg = $.trim(data);
					//alert(msg);
					if (msg.substr(0,1)=="0" )	
					{
						alert(msg.substr(2)) ;
						top.location.href="index.php";
						//MyForm1.check.disabled=true;
						//MyForm1.reset.disabled=true;
					}
					else  alert(msg.substr(2)) ;
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					alert(jqXHR);      
				}
			});
			e.preventDefault(); //STOP default action
			e.unbind(); //unbind. to stop multiple form submit.
        })

		// Enable other validators if user clicks it (checkbox) 
		<? for($boot=0; $boot<=$t; $boot++)
		   {
				if($other[$boot]==1)
				{?>
					.on('click', 'input#checkother<? echo $boot; ?>',function(){
						$otherfield = $('#form').find('input#other<? echo $boot; ?>').eq(0);
						if(form.checkother<? echo $boot; ?>.checked==true){
							$("#other<? echo $boot; ?>").removeAttr('disabled');
							$("#other<? echo $boot; ?>").val(""); //清空
							$('#form').bootstrapValidator('enableFieldValidators', 'other<? echo $boot; ?>',true);
						}//if(disabled)
						else{
							$("#other<? echo $boot; ?>").val(""); //清空
							$("#other<? echo $boot; ?>").attr('disabled', 'disabled');
							$('#form').bootstrapValidator('enableFieldValidators', 'other<? echo $boot; ?>',false);
							//hide the error class cause it's meaningless
							$("#other<? echo $boot; ?>").closest('.form-group').removeClass('has-error has-feedback');
							$("#other<? echo $boot; ?>").closest('.form-group').find('small.help-block').hide();
							$("#other<? echo $boot; ?>").closest('.form-group').find('i.form-control-feedback').hide();
						}//else
					}) 
				<?}//if(other=1)
		   }//for(boot)
		//Enable other validators if user clicks it (radio) 
		   $boot2=0;
		   for($js_p=0; $js_p<$nrowspart; $js_p++)
		   {
				$js_part = $resultspart['PART'][$js_p];
				$sql_js="select * from question where ID='$id' and PART='$js_part' order by NO ASC";
				 $stmt_js=ociparse($con,$sql_js); 
				 ociexecute($stmt_js,OCI_DEFAULT); 
				 $nrows_js = OCIFetchStatement($stmt_js,$results_js); 

				for($i=0;$i<$nrows_js;$i++)                           
				{	
					$js_no = $results_js['NO'][$i];
					$js_type = $results_js['TYPE'][$i];
					
					$sql3_js="select * from opt where NO='$js_no' and ID='$id' and PART='$js_part' order by NUM ASC"; 
					 $stmt3_js=ociparse($con,$sql3_js);    
					 ociexecute($stmt3_js,OCI_DEFAULT);
					 $nrows3_js = OCIFetchStatement($stmt3_js,$results3_js); 
					
					for ($j=0;$j<$nrows3_js;$j++)
					{ 
						$js_num = $results3_js['NUM'][$j] ;
					
						if($js_type==5)
						{ ?>
							.on('click', 'input#<?echo radio.$js_part.$js_no.$js_num; ?>',function(){
								if(form.<?echo radio.$js_part.$js_no.$js_num; ?>.checked==true){
									$("#other<? echo $boot2; ?>").val(""); //清空
									$("#other<? echo $boot2; ?>").attr('disabled', 'disabled');
									$('#form').bootstrapValidator('enableFieldValidators', 'other<? echo $boot2; ?>',false);
									//hide the error class cause it's meaningless
									$("#other<? echo $boot2; ?>").closest('.form-group').removeClass('has-error has-feedback');
									$("#other<? echo $boot2; ?>").closest('.form-group').find('small.help-block').hide();
									$("#other<? echo $boot2; ?>").closest('.form-group').find('i.form-control-feedback').hide();
								}//if(disabled)
							}) 
					  <?}//if(type==5)
				  }//for(num)
				  $boot2++;	
				}//for(no)
		   }//for(part)
		   ?>


});
</script>


</body>
</html>