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
		//******************************************取title & part_title & part_description
		$title=$_POST['title'];
		$datetime=$_POST['datetime'];
		$due=$_POST['due'];
		
		//**************************************update vote table
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

}

?>

<!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />   
	 <!-- Custom CSS -->   	
	<link rel="stylesheet" type="text/css" href="css/Style<?=$style?>.css" />
	<link href="css/ncue.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
	<link rel="stylesheet" type="text/css" href="css/bootstrapValidator.css" />   
    <script src="js/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/jquery.hoverpulse.js"></script>
	
 <?php 
    	echo"<link rel=\"stylesheet\"  href=\"css/bg$style.css\" />";
    ?>	
<!-- Bootstrap Core JavaScript -->
	<script type="text/javascript" src="js/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/dist/js/bootstrapValidator.js"></script>
	<script type="text/javascript" src="js/imageAutoSize.js"></script>
	<script type="text/javascript" src="js/html5lightbox.js"></script>
	
<!-- Gotop sign CSS -->

<style type="text/css">
	#gotop {
		display: none;
		position: fixed;
		right: 50px;
		bottom: 20%;    
		padding: 10px 15px;    
		font-size: 12px;
		background: lightgrey;
		color: black;
		font-weight: bolder;
		cursor: pointer;
		border-radius:10px;
		opacity:0.5;/*others,透明度50%*/
		-moz-opacity:0.5;  /*Firefox,透明度50%*/
		filter:alpha(opacity=50);/*IE,透明度50%*/
	}
	
	.work {
		padding-top:4%;
		margin-top:3%;
		border:#FFAC55 solid 1px;
		border-radius:10px;
	}

	#html5-lightbox-box{
		overflow-x:hidden !important;
		overflow-y:hidden !important;
	}
	#html5-lightbox-box img{		
		padding-right:5px !important;
		
	}

	#html5-close{
		top: 6px !important; 
		right: 6px !important; 
		margin-top: -9px !important; 
		margin-right: -9px !important;	
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

        <div class="row" style=" height:500px; "  >
            <div class="col-md-12"  >

		   <div id="panel-calendar" class="panel panel-primary dd">			
				<!--<div class="panel-body">-->
					<div class="container-fluid">
				<form method='POST' name="form1" class="form-horizontal">
							<div class="col-md-12">
		
				<div id="a1"><h3 style="font-weight: bolder;" class="inline">
   		<?php
			//標題-------------------------------------------------------------------------------------
				$title = $results_vote['TITLE'][0];
				$title = iconv("BIG5","UTF-8",$title);
				echo "<input type=\"text\" class=\"form-control\" name=\"title\" value=\"$title\" size=\"50\"/>";	
		?>
			</h3></div></div>  

		<?php 
			//開放時間 截止時間----------------------------------------
					echo "<table class=\"table table-bordered\">
							<tr class=\"success\"><td>
							<div class=\"col-xs-3\">
							開放時間:&nbsp <input type='text' style='width:150px' class=\"form-control\" name='datetime'  value='$datetime' />
							</div>
							 <div class=\"col-xs-3\">
							截止時間:&nbsp <input type='text' style='width:150px' class=\"form-control\" name='due'  value='$due'  />
							</div></td></tr>

						  </table>";
	
			//紀錄可投幾票 & 還剩下幾票-------------------------
			$most = $results_vote['MOST'][0];
			$least = $results_vote["LEAST"][0];
			//echo"<div align=\"right\">
			//		最少須投 $least 票，最多可以投 $most 票
			//     </div>";
		?>
		
		 <div class="span12">
				<div id="a3">
					<h6><font color="red">※<span style='color:blacke;font-size:13px'>票數限制：最少須投<? echo $least; ?> 票，最多可投<? echo $most; ?> 票！</span>　(點擊圖片可以看大圖或詳細說明)</font></h6>
   		<?php
		//排列順序-------------------------------------
		$sql_work="select * from masterpiece where ID='$id' order by NO ASC";    //取出全部作品
			 $stmt_work=ociparse($con,$sql_work);     
			 ociexecute($stmt_work,OCI_DEFAULT); 
			 $nrows_work = OCIFetchStatement($stmt_work,$results_work); 
		//建議擴充插件----------------------------------
		$type = $results_vote['TYPE'][0];
		if($type==3)
			echo "<h6><font color=\"red\">※建議使用<a href=\"https://chrome.google.com/webstore/detail/office-editing-for-docs-s/gbkeegbaiigmenfmjfclcdgdpimamgkj\" target=\"_blank\">Chrome Office Viewer</a>開啟檔案</font></h6>"; 


		?>
			</div></div>
		<?php
		echo"<div align=\"center\" id=\"all_work\"><form method='POST' name=\"form_work\" id=\"form_work\" class=\"form-horizontal\" ><div class=\"form-group\">";
		//印出作品--------------------------------------------------
			$detail = $results_vote['DETAIL'][0];
			$row = $results_vote['ROW_NUM'][0];
			switch($row)
			{
				case 1 : $height="600px";$w_height="580px";break;
				case 2 : $height="500px";$w_height="480px";break;
				case 3 : $height="400px";$w_height="380px";break;									
			}

			for($i=0;$i<$nrows_work;$i++)
			{
				$no = $results_work['NO'][$i];
				$name = $results_work['NAME'][$i];
				$name = iconv("BIG5","UTF-8",$name);
				$author = $results_work['AUTHOR'][$i];
				$author = iconv("BIG5","UTF-8",$author);
				$department = $results_work['DEPARTMENT'][$i];
				$department = iconv("BIG5","UTF-8",$department);
				$description = $results_work['DESCRIPTION'][$i];
				$description = iconv("BIG5","UTF-8",$description);
				$file_name[$i] = $results_work['FILE_NAME'][$i];
				
				if($name!="")
					$des_name="作品名稱: $name";
				else 
					$des_name="";

				if($author!="")
					$des_author="作者 : $author($department)";
				else
					$des_author="";




				if($type!=2){
					switch($detail)
					{
						case 00 : $context="<h5>作品編號: $no<br>$des_name</h5>";break;
						case 01 : $context="<h5>作品編號: $no<br>$des_name<br>創作理念 : $description</h5>";break;
						case 10 : $context="<h5>作品編號: $no<br>$des_name<br>$des_author</h5>";break;
						case 11 : $context="<h5>作品編號: $no<br>$des_name<br>$des_author<br>創作理念 : $description</h5>";break;
					}
				}//if(type!=2)
				else{
					switch($detail)
					{
						case 00 : $context="";break;
						case 01 : $context="<h5>創作理念 : $description</h5>";break;
						case 10 : $context="<h5>$des_author<br></h5>";break;
						case 11 : $context="<h5>$des_author<br>創作理念 : $description</h5>";break;
					}
				}//else
				
				$row_col=12/$row;

				if($type!=2)
					echo "<div class=\"col-lg-$row_col col-md-$row_col col-xs-$row_col col-sm-$row_col\" style=\"height:$height;\"><div class=\"work\" style=\"height:$w_height;\" >";
				else
					echo "<div class=\"col-lg-$row_col col-md-$row_col col-xs-$row_col col-sm-$row_col\" style=\"height:250px\">";
				
				
				
				//type=2 ->pure word-------------------------------------------
				if($type==2){
					$file_name[$i]=iconv("BIG5","UTF-8",$file_name[$i]);
					echo "<div class=\" panel panel-primary\">
							  <div class=\"panel-heading\"><label class=\"$label_class\"><input name=\"$input_name\" type=\"$input_type\" value=\"$no\">
								<font color=\"white\">作品編號 : $no</font></label></div>
							  <div class=\"panel-body\" style=\"height:75px;\">$file_name[$i]</div>
							  <div class=\"panel-footer\">$context</div>
						  </div>";//div=panel panel
				}

				//type=3 ->file word-------------------------------------
				if($type==3){
					$file_len=strlen($file_name[$i]);
					$file_type=$file_name[$i][$file_len-1];
					if ($file_type=='t'){
						$file_class="html5lightbox";
						$target="";
					}//if(file=t)
					else{
						$file_class="";
						$target="_blank";
					}//else
					echo "<div>
						<a href=\"./images/$file_name[$i]\" title=\"$context\"  target=\"$target\" class=\"$file_class\" data-group=\"mygroup\" data-width=\"500\" data-height=\"500\">
						$file_name[$i]</a>
					  </div>"; //div
					  
				}//if(type=3)
				
				//type=4 ->pic-------------------------------------------
				if($type==4)
					echo "<div class=\"resize\">
						<a href=\"./images/$file_name[$i]\" title=\"$context\" class=\"html5lightbox\" data-group=\"mygroup\">
						<img src='./images/$file_name[$i]' height='300'></a>
					  </div>"; //div=resize
				//type=5 ->video--------------------------------------------
				if($type==5)
					echo "<div class=\"resize\">
						<a href=\"./images/$file_name[$i]\" title=\"$context\"  class=\"html5lightbox\" data-group=\"mygroup\" data-width=\"750\" data-height=\"500\" >
						<br><video  width ='400/$row' height= '300/$row'  controls>
							 <source src=\"./images/$file_name[$i]\" type=\"video/mp4\">
							 <source src=\"./images/$file_name[$i]\" type=\"video/ogg\">
							Your browser does not support the video tag.
							</video>
						</a>
					  </div>"; //div=resize

				
				if($most==1){
					$label_class="radio-inline";
					$input_type="radio";
					$input_name="radio";
				}else{
					$label_class="checkbox-inline";
					$input_type="checkbox";
					$input_name="checkbox[]";
				}//if(most==1)


				if($type!=2){
					echo "<label class=\"$label_class\">
							 <input name=\"$input_name\" type=\"$input_type\" value=\"$no\">
							     <font color=\"blue\">作品編號 : $no</font>
						  </label>";
					// ANONYMOUS--------------------------------------------------
					if($detail=="00" || $detail=="01")
						echo "<div><a href=\"./images/$file_name[$i]\" title=\"$context\" class=\"html5lightbox\" data-group=\"mygroup_more\" >$des_name<br><span>詳細資訊</span></a></div>"; //div
					else
						echo "<div><h5>$des_name<br>$des_author</h5><a href=\"./images/$file_name[$i]\" title=\"$context\" class=\"html5lightbox\" data-group=\"mygroup_more\" ><span>詳細資訊</span></a></div>"; //div
				}
				if($type!="2")
					echo "</div>"; //div=work
					
				echo "</div>"; //div style=height
				
				
			}//for(i)
			echo "</div>";//div=form-group 
							   echo "<p class=\"help-block\"></p>";          
		?>

			<div class="form-group text-center">
			<button type="submit" class="btn btn-primary" name="modify" >修改完成</button></div>
			<div class="form-group text-right">
			<button type="submit" class="btn btn-default" name="add" >新增筆數</button></div>
		
	</form>					
</div>

</div>
</div>
</div>
        </div>         

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