<?php session_start(); 

include("connect.php");

				$account=$_SESSION['Username'];
				$undertaker=$_SESSION['NAME'];
				$department=$_SESSION['department'];

				$sql8="select * from member where IDENTITY='$account' and status='1'";       
 
				 $stmt8=ociparse($con,$sql8); 
				 ociexecute($stmt8,OCI_DEFAULT); 
	  
				$nrows8 = OCIFetchStatement($stmt8,$results8);

				if($nrows8!=1)
				{	
				  header("Location:index.php");
				}

$id=$_GET['ID'];

//***************************************現在的時間
	$Ymd=date("Ymd"); 
	$His= date("His");
	$now=$Ymd.$His;
	//echo $now;
//***************************************id自動新增
	$sql="select MAX(id) as ID from questionnaire";
	$stmt=ociparse($con,$sql);     
	ociexecute($stmt,OCI_DEFAULT); 
	$t= OCIFetchStatement($stmt,$results);
	$max_id=$results['ID'][0];
	//echo "max_id:".$max_id;
	$check_id=(int)($max_id/10000);  //取資料庫最大ID的年份
	//******************************
    $Y=date("Y");
	$now_year=$Y-1911;     			//取現在的年份
	if($check_id==$now_year)  {     //問卷ID年份跟著現在的年份跳
		$id=$max_id+1;
		//echo $id;
	}
	else{                          
		//$now_year=104;
		$id=$now_year.'0001';
		//echo $id;
	}
	
if(isset($_POST["B1"]))
{
	//***************************************轉換時間的格式
	$date_value=$_POST['date'];
	$date1=str_replace('/','',$date_value);
	$date2=str_replace(':','',$date1);
	$date3=str_replace('-','',$date2);
	//echo $date3."<br>";
	
	$due_value=$_POST['due'];
	$due1=str_replace('/','',$due_value);
	$due2=str_replace(':','',$due1);
	$due3=str_replace('-','',$due2);
	//echo $due3."<br>";
	
	//***************************************讓題目 介紹詞 指導語換行
	$title_value= $_POST['title']; 
	$t=nl2br($title_value);
	$title=str_replace('\n','<br>',$t);
	
	$introduce_value=$_POST['introduce'];
	$i=nl2br($introduce_value);
	$introduce=str_replace('\n','<br>',$i);
	
	$description_value=$_POST['description'];
	$d=nl2br($description_value);
	$description=str_replace('\n','<br>',$d);
	
	//***************************************填寫人身分
	for($i=1;$i<=5;$i++)
	{
		if($_POST['participant'][$i]){
			$p_value[$i]=$_POST['participant'][$i];
			$p_value[$i]=1;
		}
		else $p_value[$i]=0;
	}
	//$participant   [開放]/教師/職員/學生/[限定], 如：01100 代表限教職員填答 
	for($i=1;$i<=5;$i++)$participant .= $p_value[$i];

	//***************************************存值到questionnaire table
		$sql= "insert into questionnaire values('$id','$title',$date3,$due3,'$_POST[department]','$_POST[undertaker]',$_POST[tel],'$introduce',$_POST[style],'$participant','$description','$_POST[account]',$_POST[analyze],$_POST[qhome],1,$_POST[encrypt],$_POST[chart])";
		$sql =iconv("UTF-8","BIG5//IGNORE", $sql);
		//echo $sql;
		$stmt = OCIPARSE($con,$sql);

		if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);

	$url2 = "q_design2.php?ID=$id"; 
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

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
    <link href="css/index_label.css" rel="stylesheet">
	<link href="css/ncue.css" rel="stylesheet">
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>

</head>
<body>
  <div class="container container_ncue"  valign="bottom"  > 
		<div class="row">
            <div class="col-md-12" valign="bottom">				
				<div id="banner">
				
			
				</div>	<?	include("test_menu.php");?>
			</div>
		</div>
<br>

        <div class="row" style=" height:500px; "  >
            <div class="col-md-12"  >

			<div class="progress"  style="background-color:#cccccc;">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
    完成度 : 20%
  </div>
</div>
	
				<div id="panel-calendar" class="panel panel-primary">
			
			<p class="bg-primary" style=" font-size: large;">&nbsp第一步-填寫基本資料(問卷)</p>
				<div class="panel-body">
					<div class="container">
							<div class="col-md-9">
			
						<table class="table table-bordered">
						<tr class="success"><td>
						<h5><font color="red">打 * 表示為必填</font></h5>	
						</tr>
						</td>
						</table>
					
			<form name="form1" method='POST' id="form1"  class="form-horizontal">			
			<!--問卷題目 -->
				<div class="form-group"><strong>問卷題目<font color="red">*</font></strong>
					<textarea name='title'  class="form-control" placeholder="最多200字"   value='<? echo $_POST['title'];?>' maxlength="200"/></textarea></div>
	       
			<!--介紹詞 -->
	        <div class="form-group"><strong>介紹詞</strong>
				<textarea name='introduce' class="form-control" placeholder="最多500字"  value='<? echo $_POST['description'];?>' maxlength="500"></textarea></div>
	        
			<!--指導語 -->
			<div class="form-group"><strong>指導語</strong>
				<textarea name='description' class="form-control" placeholder="最多500字"  value='<? echo $_POST['introduce'];?>' maxlength="500"></textarea></div>
	        
			<!--承辦單位 -->
		    <div class="form-group"><strong>承辦單位<font color="red">*</font></strong>
				<input type='text'  class="form-control" name='department' placeholder="最多15字"  value='<? echo $department;?>' maxlength="15"/></div>
			
			<!--承辦人 -->
			<div class="form-group"><strong>承辦人<font color="red">*</font></strong>
				<input type='text' name='undertaker'  class="form-control"  value="<? echo $undertaker; ?>" maxlength="10"/></div>
	        
			<!--承辦帳號 -->
			<div class="form-group"><strong>承辦帳號<font color="red">*</font></strong>
				<input type='text' name='account'  class="form-control"  value="<? echo $account; ?>"  maxlength="20"/> </div>
			
			<!--分機 -->
			 <div class="form-group"><strong>分機<font color="red">*</font></strong>
             <input type='text' name='tel'  class="form-control"  value='<? echo $_POST['tel'];?>' maxlength="10"/></div>
			
			<!--填寫時間* -->
			<? $Ymd=date("Ymd"); ?>
            <div class="form-group"><strong>填寫時間<font color="red">*&nbsp&nbsp&nbsp</font><font color="gray">時間格式: (西元)yyyy年/mm月/dd日- hh時:mm分:ss秒</font></strong>
				<div class="input-group date form_datetime col-md-5" data-date="<? $Ymd; ?>" data-date-format="yyyy/mm/dd-hh:ii:ss" data-link-field="dtp_input1">
					<input class="form-control"  type="text" value="<? echo $_POST['date']?>" name="date" maxlength="20" placeholder="請由右邊圖案選擇" >
					<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
				</div>~
				<div class="input-group date form_datetime col-md-5" data-date="<? $Ymd; ?>" data-date-format="yyyy/mm/dd-hh:ii:ss" data-link-field="dtp_input1">
					<input class="form-control"  type="text" value="<? echo $_POST['due'];?>" name="due" maxlength="20" placeholder="請由右邊圖案選擇" >
					<span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
				</div>	
			</div>
			
			 <!--填寫人身分 --> 
			  <div class="form-group"><strong>填寫人身分<font color="red">*</font></strong>
			  <?
				$p=array("","開放","教師","職員","學生","限定");
				echo "<label class=\"checkbox-inline\">
					<input type=\"checkbox\" name=\"participant[1]\" value=\"1\"  class=\"participant\" checked>$p[1]
					</label>";
	
				for($i=2;$i<=5;$i++){
				echo "<label class=\"checkbox-inline\">
					<input type=\"checkbox\" name=\"participant[$i]\" value=\"$i\"  class=\"participant\">$p[$i]
					</label>";
				}
			  ?>
			 </div>
			 
              <!--是否要有首頁 --> 
			 <div class="form-group"><strong>是否要有首頁<font color="red">*</font></strong>
				<label class="radio-inline">
					<input type="radio" name="qhome"  value="1" checked>是 	
				</label>
				<label class="radio-inline">
					<input type="radio" name="qhome"  value="2">否
				</label>
			 </div>
			 
			  <!--填答者身分是否加密 -->
			<div class="form-group"><strong>填答者身分是否加密<font color="red">*</font></strong>
				<label class="radio-inline">
					<input type="radio" name="encrypt"  value="1" checked>是 
				</label>
				<label class="radio-inline">
					<input type="radio" name="encrypt"  value="2">否
				</label>
		    </div>
			 
			<!--問卷結果是否公開 -->
			<div class="form-group"><strong>問卷結果是否公開<font color="red">*</font></strong>
				<label class="radio-inline">
					<input type="radio" name="analyze" id="analyze" value="1" checked>是 
				</label>
				<label class="radio-inline">
					<input type="radio" name="analyze" id="analyze" value="2">否
				</label>
			 </div>

			 <!--結果呈現方式 --> 
			  <div class="form-group"><strong>結果呈現方式<font color="red">*</font></strong>
			  <label class="radio-inline">
					<input type="radio" name="chart" value="1" checked>圓餅圖
				</label>
				<label class="radio-inline">
					<input type="radio" name="chart" value="2">長條圖
				</label>
			 </div>
			 
			
			<!--問卷主題 -->
			<div class="form-group"><strong>問卷主題<font color="red">*</font></strong>
			
				<table id="tab"  class="table table-bordered">
                <!--第一行-->
			

				<div class="form-group">
					<div>
						<tr class="success">
							<td align="center" ><img src="image/white.png" width="100" height="100" alt=""  class="img-circle" name="style"/></td>		
							<td align="center" ><img src="image/white1.png" width="100" height="100" alt=""  class="img-circle" name="style"/></td>
							<td align="center" ><img src="image/white2.png" width="100" height="100" alt=""  class="img-circle" name="style"/></td>
						</tr>
						<tr>
							<td align="center" ><input type="radio" name="style" value="1" checked>藍條紋</td>    
							<td align="center" ><input type="radio" name="style"  value="2"/>綠白漸層</td>
							<td align="center" ><input type="radio" name="style"  value="3"/>圈圈圓圓</td>
						</tr>
					</div>
				</div>
				</div>
				<!--第二行-->
				
					<div>
						<tr class="success">
							<td align="center" ><img src="image/cloud.png" width="100" height="100" alt=""  class="img-circle" name="style"/></td>		
							<td align="center" ><img src="image/white4.png" width="100" height="100" alt=""  class="img-circle" name="style"/></td>
							<td align="center" ><img src="image/white5.png" width="100" height="100" alt=""  class="img-circle" name="style"/></td>
						</tr>
						<tr>
							<td align="center" ><input type="radio" name="style" value="4" >白雲</td>                  
							<td align="center" ><input type="radio" name="style"  value="5"/>落葉</td>
							<td align="center" ><input type="radio" name="style"  value="6"/>藍白漸層</td>
						</tr>
					</div>
				
				</table>
				
				<div class="text-right " >
				<button type="submit" name="B1" class="btn btn-default" >下一步</button>
				</div>
				</form>  <!--form1-->
				
				
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
<script type="text/javascript" src="js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrapValidator.js"></script>

 <script type="text/javascript">

$(document).ready(function() {
  
	
    $('#form1').bootstrapValidator({
		
        message: 'This value is not valid',
       
        fields: {
            title: 
			{
                validators: {
                    notEmpty: {
                        message: '必填!!'
                    }
                }
            },
			department: 
			{
                validators: {
                    notEmpty: {
                        message: '必填!!'
                    }
                }
            },      
			undertaker: 
			{
                validators: {
                    notEmpty: {
                        message: '必填!!'
                    }
                }
            },   
			account: 
			{
                validators: {
                    notEmpty: {
                        message: '必填!!'
                    }
                }
            }, 
			tel: 
			{
                validators: {
                    notEmpty: {
                        message: '必填!!'
                    }
                }
            }, 
			date: 
			{
                validators: {
                    notEmpty: {
                        message: '必填!!'
                    }
                }
            }, 
			due: 
			{
                validators: {
                    notEmpty: {
                        message: '必填!!'
                    }
                }
            },
			participant: {
				selector:'.participant',
                validators: {
					choice: {
                        min: 1,
						max: 5,
                        message: '必填!!'
                    }
				}
            }
			
        }
    });
}); 
</script> 

<script type="text/javascript">
    $('.form_datetime').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
        showMeridian: 1
    });
	$('.form_date').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
	$('.form_time').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 1,
		minView: 0,
		maxView: 1,
		forceParse: 0
    });
</script>

</body>
</html>
