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
$_SESSION["get_id"]=$id;
//******************************************************select type, done from vote 
$select="select * from vote where ID='$id' ";
$stmt=ociparse($con,$select);     
ociexecute($stmt,OCI_DEFAULT); 
$t= OCIFetchStatement($stmt,$result);
$type=$result['TYPE'][0];
$done=$result['DONE'][0];
//******************************************************select no if done=2
if($done==2){
	if($type==1)
		$select_no="select MAX(no) as NO from candidate where ID='$id'";
	else if($type!=1)
		$select_no="select MAX(no) as NO from masterpiece where ID='$id'";
	$stmt=ociparse($con,$select_no);     
	ociexecute($stmt,OCI_DEFAULT); 
	$t= OCIFetchStatement($stmt,$result);
	$no=$result['NO'][0];
}
//*********************** type3~5 手動輸入
if(isset($_POST["B1"])){

	include("v_design_insertDB.php");

	$url2 = "v_design3.php?ID=$id"; 
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url2'";
	echo "</script>";

}
//*********************** type=1 手動輸入
if(isset($_POST["save"]))
{

	if($done!=2)
	{
		$row=$_POST["row"];
		$order_by=$_POST["order_by"];
							
		$update_vote = "update vote set  ROW_NUM='$row', DONE='2', ORDER_BY=$order_by where id='$id'  ";
		$stmt_update = OCIPARSE($con,$update_vote );
									
		if(!OCIEXECUTE($stmt_update,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);

	}

    $url3 = "v_design3.php?ID=$id"; 
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url3'";
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
    <link href="css/index_label.css" rel="stylesheet">
	<link href="css/ncue.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-1.2.6.pack.js"></script>  
	<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="js/jquery.js"></script> <!-- checkbox 傳值需要 -->

</head>
<body>
  <div class="container container_ncue" valign="bottom"  > 
		<div class="row">
            <div class="col-md-12" >				
				<div id="banner">
				
			
				</div>	<?	include("test_menu.php");?>
			</div>
		</div>
<br>

        <div class="row" style=" height:500px; " >

			
            <div class="col-md-12" >

<div class="progress"  style="background-color:#cccccc;">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 80%;">
    完成度 : 80%
  </div>
</div>

	<form name="form" method='POST'  enctype="multipart/form-data">
		<div id="panel-calendar" class="panel panel-primary">
			
			<p class="bg-primary" style=" font-size: large;">&nbsp第二步-
			<? 
				if($type==1) echo "人員票選";

				if($type==2) echo "標語票選";

				if($type==3) echo "文字檔票選(限定.doc/.txt/.pdf )";

				if($type==4) echo "圖片檔票選(限定.jpg )";

				if($type==5) echo "影片檔票選(限定.mp4 )";

				if($type==6) echo "文字/圖片/影片檔的資料匯入";
		
			echo "</p>";

			?><div class="panel-body">
			
			<? //***********************************if type =1~2 
				if($type==1 || $type==2){ ?>
				<div class="text-right " >
					<label class="radio-inline">
						<input type="radio" name="decide"  value="1" <?if ($_POST['ensure']!=1) echo "checked" ?>>直接匯入 
					</label>
					<label class="radio-inline">
						<input type="radio" name="decide"  value="2"  <? if (isset($_POST['ensure']) && $_POST['decide']=="2") echo "checked"; ?>>手動輸入
					</label>
				
				<button type="submit" name="ensure" class="btn btn-default" >確定</button></div>
			
				<?
			

				//*************************************************直接匯入
				
				include("v_d_import.php"); //步驟2 (if isset import then write into DB)

				if(isset($_POST['ensure']) && $_POST['decide']==1){ //步驟1 (選擇檔案)

				

					echo "<div class=\"col-xs-3\"><input name=\"file\" type=\"file\" ></div>";

							if($done==1){  ?>
									<div class="col-xs-2">
									<strong>每行顯示筆數</strong>
									<select name="row"  class="form-control" >              
									<option value="1" selected>1</option>
									<option value="2" >2</option>
									<option value="3" >3</option>
									<option value="4" >4</option>
									<option value="5" >5</option>
									</select> 
									</div>
									
									<? if($type==1){ ?>
									<div class="col-xs-2">
									<strong>人員顯示順序</strong>
									<select name="order_by"  class="form-control" >              
									<option value="1" selected>姓名筆劃</option>
									<option value="2" >單位筆劃</option>
									
									</select> 
									</div>
									<?} // type=1
									 if($type==2){ ?>
									<div class="col-xs-3">
									<strong>是否顯示作者單位/創作理念</strong><br>
									 <label class="checkbox-inline">
										<input type="checkbox" name="detail[1]" value="1" id="t2" checked>作者單位
										</label>
										<label class="checkbox-inline">
										<input type="checkbox" name="detail[2]" value="2" id="t2" >創作理念
									</label></div>
									<? } //type=2
							} //done=1 ?>									
					<br>
					<div class="col-xs-2">
						<div class="text-right">
						<button type="submit" name="import" class="btn btn-default" >確定匯入</button>
						</div>
					</div>
				<? } // if ensure & decide=1 
					
			
				//*************************************************手動輸入
				 if(isset($_POST['ensure']) && $_POST['decide']==2){ 
							//*************************************type=1 
							if($type==1)
							{
							echo "<script type='text/javascript'>";
							echo "window.open('v_d_candidate.php ', '被投票人選', config='height=600,width=900');";
							echo "</script>";

								if($done==1){?>
								
									<div class="col-xs-2 "><strong>每行顯示筆數</strong>
									<select name="row"  class="form-control" >              
									<option value="1" selected>1</option>
									<option value="2" >2</option>
									<option value="3" >3</option>
									<option value="4" >4</option>
									<option value="5" >5</option>
									</select> 
									</div>

									<div class="col-xs-2">
									<strong>人員顯示順序</strong>
									<select name="order_by"  class="form-control" >              
									<option value="1" selected>姓名筆劃</option>
									<option value="2" >單位筆劃</option>
									</select>
									</div><br>
								<? }// if done=1 
							
								echo  "<div class=\"text-right\">
									<button type='sumbit' class='btn btn-default' name='save'>下一步</button>
									</div>";
							
						} //type=1
							//*************************************type=2
							if($type==2){
								
								echo "
								<table class=\"table table-bordered\">
								<tr class=\"success\"><td>
								<h5><font color=\"red\">【作者】【單位系級】【作品名字】【創作理念】皆可不填</font></h5>	
								</tr>
								</td>
								</table>";
							
								 if($done==1){ ?>
									<div class="col-xs-4 "><strong>每行顯示筆數</strong>
									<input type="text" name="t1" id="t1" class="form-control"/></div>
									
									<strong>是否顯示作者單位/創作理念</strong>
									 <label class="checkbox-inline">
										<input type="checkbox" name="detail" value="1" id="t2" checked>作者單位
										</label>
										<label class="checkbox-inline">
										<input type="checkbox" name="detail" value="2" id="t2" >創作理念
									</label>
										
									
								<?  } // done=1
	
									 include("v_d_slogan.php");
	
								 ?>
								 <div class="text-left">
								<button type="button" class="btn btn-success" id='addButton'>新增筆數</button>
								<button type="button" class="btn btn-primary" id='removeButton'>刪除</button>
								</div><br>
								
							   
								<table class="table table-striped"><tr><td style="width:100px">筆數</td><td style="width:150px">作者</td><td style="width:150px">單位/系級</td><td style="width:150px">作品名字</td><td>標語</td></tr>
								</table>

								<div id='TextBoxesGroup'>
									<div id="TextBoxDiv1">
									</div>
								</div>

								<div class="text-right " >
								<button type="button" class="btn btn-default" id='getButtonValue' >下一步</button>
								</div>
								<?

								} //type=2
					} // if ensure & decide=2 
				} // if type =1~2
				else if ($type==3 || $type==4 || $type==5 )	include("v_d_masterpiece.php");
				
					

				else if ($type==6) {
					
				include("v_d_import.php"); //步驟2 (if isset import then write into DB)

					echo "<div class=\"col-xs-3\"><input name=\"file\" type=\"file\" ></div>";

							if($done==1){  ?>
									<div class="col-xs-2">
									<strong>每行顯示筆數</strong>
									<select name="row"  class="form-control" >              
									<option value="1" selected>1</option>
									<option value="2" >2</option>
									<option value="3" >3</option>
									</select> 
									</div>

									<div class="col-xs-2">
									<strong>選擇檔案類型</strong>
									<select name="f_extension"  class="form-control" >              
									<option value="jpeg" selected>jpg</option>
									<option value="doc" >doc</option>
									<option value="pdf" >pdf</option>
									<option value="txt" >txt</option>
									<option value="mp4" >mp4</option>
									</select> 
									</div>

									<div class="col-xs-3">
									<strong>是否顯示作者單位/創作理念</strong><br>
									 <label class="checkbox-inline">
										<input type="checkbox" name="detail[1]" value="1"  checked>作者單位
										</label>
										<label class="checkbox-inline">
										<input type="checkbox" name="detail[2]" value="2" >創作理念
									</label></div>
							<? } //done=1 ?>									
					<br>
					<div class="col-xs-2">
						<div class="text-right">
					
						<button type="submit" name="import" class="btn btn-default" >確定匯入</button>
						</div>
					</div>
				<?	} //type=6	?>
				
					</div> <!-- panel-body-->

					
				
				</form>
					
				</div> <!--panel-calendar-->
				
			</div> <!--col-md-12-->
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


<!--<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script-->
<?
if($type!=1){?>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrapValidator.js"></script>>
<? } ?>

</body>
</html>