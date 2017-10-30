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
 $id=$_GET['ID'];  // 取得網址後面的ID #  

$_SESSION['vid']=$id;


$sql_vote="select * from vote where ID='$id' ";  //取出該筆投票    
 $stmt_vote=ociparse($con,$sql_vote);     //取出資料固定格式
 ociexecute($stmt_vote,OCI_DEFAULT); 
 $nrows_vote = OCIFetchStatement($stmt_vote,$results_vote); //算取出來的有幾筆

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

//*****************************************
//檢查是否為開放填答期間
//*****************************************
$date=date("YmdHis");
if($date<$results_vote['DATETIME'][0])
{
	echo "<script language = JavaScript>";
	echo "alert(\"此投票活動尚未開放！\");"; 
	echo "window.location.href='index.php'";
	echo "</script>"; 
	exit;
}
if($date>$results_vote['DUE'][0])
{	
	echo "<script language = JavaScript>";
	echo "alert(\"此投票活動已截止！\");";
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;
}
//*******************************************
//權限檢查
//*******************************************
$arr_identity= array("[開放]","教師","職員","學生","[限定]");
$p = $results_vote['PARTICIPANT'][0] ; //01110    [開放]/教師/職員/學生/[限定]
for($i=0;$i<5;$i++)
	if (substr($p,$i,1)=="1") $partcipant .= " " . $arr_identity[$i];

$user_type=$_SESSION['user_type'] ;
if (!substr($p,$user_type,1)=="1" && !$status==1 && !substr($p,0,1)=="1") 
{
	echo "<script language = JavaScript>";
	echo "alert(\"此投票活動僅開放【 $partcipant 】填寫\");"; 
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;
}

//是否為可填寫者
$sql_elector_check="select * from ELECTOR where ID=$id";       //檢查是否有勾選   (elector 有無值)
 $stmt_elector_check=ociparse($con,$sql_elector_check); 
 ociexecute($stmt_elector_check,OCI_DEFAULT);   
 $nrows_elector_check = OCIFetchStatement($stmt_elector_check,$results_elector_check);

$sql_elector="select * from ELECTOR where IDENTITY='".$_SESSION['Username']."' and ID=$id";       
 $stmt_elector=ociparse($con,$sql_elector); 
 ociexecute($stmt_elector,OCI_DEFAULT);   
 $nrows_elector = OCIFetchStatement($stmt_elector,$results_elector);
if ($nrows_elector_check!=0 && $nrows_elector==0 && !$status==1)
{
	echo "<script language = JavaScript>";
	echo "alert(\"此投票活動僅開放【 特定人士 】填寫\");"; 
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;
}

//********************************************
//檢查是否已完成填答
//********************************************
$username=$_SESSION['Username'];
$sql_id="select * from election_info where ID='$id' and IDENTITY='$username' " ;     //已投過票的紀錄
 $stmt_id=ociparse($con,$sql_id);     
 ociexecute($stmt_id,OCI_DEFAULT); 
 $nrows_id = OCIFetchStatement($stmt_id,$results_id);
if ($nrows_id>0  && !$status==1) 
{
	echo "<script language = JavaScript>";
	echo "alert(\"您已投過票，謝謝您的參與！\");"; 
	echo "window.location.href='index.php';";
	echo "</script>"; 
	exit;
}
//檢查結束
//***************************************************************************


$style = $results_vote['STYLE'][0];

?>

<!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />   
	 <!-- Custom CSS -->   	
	<link rel="stylesheet" type="text/css" href="css/Style<?=$style?>.css" />
	<link href="css/ncue.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
	<link rel="stylesheet" type="text/css" href="css/bootstrapValidator.css" />   
 <?php 
    	echo"<link rel=\"stylesheet\"  href=\"css/bg$style.css\" />";
    ?>	
<!-- Bootstrap Core JavaScript -->
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/dist/js/bootstrapValidator.js"></script>
	<script type="text/javascript" src="js/jquery.hoverpulse.js"></script>	
	<script type="text/javascript" src="js/imageAutoSize.js"></script>
	<script type="text/javascript" src="js/html5lightbox.js"></script>

	<style type="text/css">
		.hover {
		   background: #D9EEA2; 
		}
		table{
		   background: #EEF7D2; 		   
		}
		fieldset {
	border:1px dashed #AAA;
	padding:0px;
	margin-top:0px;
	margin-bottom:0px;
	
}
		legend {
	font-family:Arial, Helvetica, sans-serif;
	font-size: 13px;
	letter-spacing: 1px;
	font-weight: bold;	
	color:#36558E;	
	border: 0px dashed #333;
	padding: 0px;
}
	 
	 #html5-lightbox-box{
		position:absolute;
		 top:-100px;		 
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


				<div id="a1"><h2 style="font-weight: bolder;" class="inline">
   		<?php
			//標題-------------------------------------------------------------------------------------
				$title = $results_vote['TITLE'][0];
				$title = iconv("BIG5","UTF-8",$title);
				echo "$title";	
		?>
			</h2></div></div>  
			
		<?php 
			//描述----------------------------------------
				$v_des = $results_vote['DESCRIPTION'][0];
				$v_des = iconv("BIG5","UTF-8",$v_des);
				if($v_des!="")
					echo "<table class=\"table table-bordered\">
							<tr class=\"success\"><td><h5>$v_des</h5></td></tr>
						  </table>";
	

			//紀錄可投幾票 & 還剩下幾票-------------------------
			$most = $results_vote['MOST'][0];
			$least = $results_vote['LEAST'][0];
			echo"<div align=\"right\">
					最少須投 $least 票，最多可以投 $most 票	
				 </div>";
		?>

		 <div class="span12">
				<div id="a3">
   		<?php
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
		echo "<h6><font color=\"red\">※依照 $order_by 順序排列(共 $nrows_can 人)</font></h6>"; 
		?>
			</div></div>   <!--   <div class="span12">          <div id="a3">    -->
		<?php
		echo"<form method='POST' name=\"form_member\" id=\"form_member\" class=\"form-horizontal\" action=\"v_insert_DB.php\">
			  <div class=\"form-group\"><table class=\"table table-bordered\">";
		//印出候選人--------------------------------------------------
			$row = $results_vote['ROW_NUM'][0];
				
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

				echo "<td style=\"border:solid 3px #FFFFFF\">
						<label>
							<input name=\"$input_name\" type=\"$input_type\" value=\"$no\">
								<font color=\"blue\"><span style=\"font-size:15px;\">$candidate</span></font>
								<span style=\"font-size:12px;\">($department)</span>
						</label>";

				if($des!=""){
					echo "<a href=\"./image/$i\" title=\"候選人:$candidate<br>$des \" class=\"html5lightbox\" data-group=\"mygroup\" data-width=\"500\" data-height=\"10\">詳細資料</a>";
				}//if(des!=null)
								
				echo "</td>";

				if($i%$row==($row-1))
					echo "</tr>";
			}//for(i)
			
			
			echo "</table></div>";// div=form-group
			echo "<div class=\"form-group\">
					<div align=\"center\" class=\"col-lg-12\">
					   <button type=\"submit\" class=\"btn btn-primary\" name=\"send\" id=\"send\">送出</button>
				    </div>
				  </div></form>"; //div=form-group; form=form_member
			
			if($username == $results_vote['UNDERTAKER'][0] || $status==1)
			echo "<form method='POST' name=\"form_mod\" id=\"form_mod\" action=\"v_design2.php?ID=$id\">
					<div align=\"right\" class=\"col-lg-12 col-md-12 col-xs-12 col-sm-12\">
						<button type=\"submit\" class=\"btn btn-default\" name=\"modify\" id=\"modify\">修改/新增</button>
					</div>
			      </form>";//form= modify
						
						
                       
		?>
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



<script>

$("td").hover(function() {
  $(this).addClass("hover");
}, function() { 
  $( this ) .removeClass("hover");
});
 
</script>

<script type="text/javascript">
$(document).ready(function() {
	$('#form_member').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon',
          //  invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
		fields: {
            'checkbox[]': {
                validators: {
                    choice: {
                        min: <? echo "$least"; ?>,
                        max: <? echo "$most"; ?>,
                        message: '最多投 <? echo $most; ?> 票 且 最少投 <? echo $least; ?> 票'
                    }
                }
            },
			'radio': {
                validators: {
					notEmpty: {
						message: '請選擇一個!!'
					}, 
				}
            }
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
		$('#erroralert')
			.find('.welcome').html("<span style='color:blacke;font-size:13px'>票數限制：最少須投<? echo $least; ?> 票，最多可投<? echo $most; ?> 票！</span>").end()
			.modal('show');
	})
	
	//submit by ajax----------------------------------
	.on( 'success.form.bv' , function(e) {
		var postData = $(this).serializeArray();
		var formURL = $("#form_member").attr("action");	
		$.ajax(
		{
			beforeSend: function (request) {
					if(!confirm('是否確定送出？')) return false;
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
	});//send.click

});
</script>


</body>
</html>