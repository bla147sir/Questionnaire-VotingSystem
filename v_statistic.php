<? session_start(); 

include("connect.php");
include("check.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<script type="text/javascript" src="js/jquery.min.js"></script>

    
<?
 $id=$_GET['ID'];

$_SESSION['ID']=$id;
$sql="select * from vote where ID='$id'";
$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
$nrows=OCIFetchStatement($stmt,$results); 
$undertaker=$results['ACCOUNT'][0];
$ana=$results['ANALYZE'][0];//是否開放結果查詢
$date=date("YmdHis");
//echo $results['DATETIME'][0] ;
if($date<$results['DUE'][0])
	$open_flag=0; //問卷尚未截止
else
	$open_flag=1; //問卷已截止

$style = $results['STYLE'][$nrows-1];
$chart=$results['CHART'][0];
$user=$_SESSION['Username'];

$sql="select * from member where identity='$user'";

$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
$nrows=OCIFetchStatement($stmt,$results); 



if ($nrows==1&&$results['STATUS'][0]==1)
  $admin=1;
else
	$admin=0;
//echo $open_flag ;			
//管理者
//開放結果查詢的問卷或承辦人可於問卷截止後查看
if( $admin==1 || ($open_flag==1 && ($_SESSION['Username']==$undertaker||$ana==1)))
{

?>

    <title>國立彰化師範大學線上問卷及投票系統</title>


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

<!-- Bootstrap Core JavaScript -->


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
				</div>	
				<?	include("test_menu.php");?>
			</div>
		</div>
<br>

        <div class="row" style=" height:500px; "  >
            <div class="col-md-12"  >

 <div id="panel-calendar" class="panel panel-primary dd">	
<div class="container-fluid"  align='left'>
				
   		<?php

		echo"<div id=\"all_work\" >";
		//印出作品--------------------------------------------------		

		$sql="select * from vote where ID='$id' "; 
		$stmt=ociparse($con,$sql); 
		ociexecute($stmt,OCI_DEFAULT); 
		$nrows= OCIFetchStatement($stmt,$results); 

		 $title = $results['TITLE'][0];
		 $title= iconv("BIG5","UTF-8",$title);
		 $detail= $results['DETAIL'][0];
		 $description = $results['DESCRIPTION'][0];
		 $description= iconv("BIG5","UTF-8",$description);

		 $sql1="select distinct IDENTITY from ELECTION_INFO where ID='$id' "; 
		 $stmt1=ociparse($con,$sql1); 
		 ociexecute($stmt1,OCI_DEFAULT); 
		 $nrows1 = OCIFetchStatement($stmt1,$results1); 

 $other_counter=0;
		

//標題-------------------------------------------------------------------------------------
echo "<div id=\"a1\"><h3 style=\"font-weight: bolder;\" class=\"inline\">$title	</h3>
			<h5 align=\"right\" ><br>投票總人數 : $nrows1<br></h5>
		</div>   ";         //<div id=\"a1\">
		echo "</div>  ";//<div id=\"all_work\" >

		echo "<h4 align=\"center\">$description</h4></br>";
		$type=$results['TYPE'][0];
		$row=$results['ROW_NUM'][0];
		
switch($row)
			{
				case 1 : $height="600px";$w_height="580px";break;
				case 2 : $height="500px";$w_height="480px";break;
				case 3 : $height="400px";$w_height="380px";break;									
			}
//////////////////////////////////////////////計算票數
			if ($type==1)
				$sql1="select * from CANDIDATE where ID='$id' order by NO ASC"; 
			else 
				$sql1="select * from MASTERPIECE where ID='$id' order by NO ASC"; 

			$stmt1=ociparse($con,$sql1); 
			ociexecute($stmt1,OCI_DEFAULT); 
			$nrows1 = OCIFetchStatement($stmt1,$results1); 
			$ij=0;
			$max1=0;
			$maxNAME=0;
			for($i=0;$i<$nrows1;$i++)
			{
				$Name[$i]=$results1['NAME'][$i];
				$Name[$i]=iconv("BIG5","UTF-8",$Name[$i]);
				
				$no[$i]=$results1['NO'][$i];
				$author[$i]=iconv("BIG5","UTF-8", $results1['AUTHOR'][$i]);

				$department[$i]=iconv("BIG5","UTF-8",$results1['DEPARTMENT'][$i]);
				$description[$i]=iconv("BIG5","UTF-8",$results1['DESCRIPTION'][$i]);
				$net_addr[$i] = $results1['HYPERLINK'][$i];


				if($Name[$i]!="")
					$des_name[$i]="作品名稱: $Name[$i]";
				else 
					$des_name[$i]="";

				if($author[$i]!="")
					$des_author[$i]="作者 :$author[$i]($department[$i])";
				else
					$des_author[$i]="";


			
				$sql2="select COUNT(*) from VOTE_CHOSE where ID='$id' AND CHOOSE=$no[$i]"; 
				$stmt2=ociparse($con,$sql2); 
				ociexecute($stmt2,OCI_DEFAULT); 
				$nrows2 = OCIFetchStatement($stmt2,$results2); 
				$voteN[$i]=$results2['COUNT(*)'][0];
				if($voteN[$i]==0)
					$N[$i]="";
				else 
					$N[$i]=$Name[$i];
				//***********************************************************************************************
				
				if ($type==2)
				{  $slogan[$i]=$results1['FILE_NAME'][$i];  $slogan[$i]= iconv("BIG5","UTF-8",$slogan[$i]); }
				else if ($type==3||$type==4||$type==5)
					$file_name[$i]=$results1['FILE_NAME'][$i];
			
				//***********************************************************************************************
				
				if (strlen($Name[$i])>$maxNAME)
					$maxNAME=strlen($Name[$i])+10;
				if ($voteN[$i]>$max1)
					{$max1=$voteN[$i];  $winner=$i;}  //存票數最高者
			}
			

		
			
			 $_SESSION["m1"]=$N;
			 $_SESSION["n1"]=$voteN;
			 $_SESSION["maxi1"]=$max1;
			 $wid=$nrows1*30+50;
				/////////////計算票數_END
				//////////顯示長條圖或圓餅圖
		if ($max1!=0)
			 {
				echo" <div  align=\"center\" >";
				 if ($chart==2)
					echo "<img src= \" bar.php?I=1&W=$wid &len=$maxNAME \" /> ";
				 else if ($chart==1)
					echo "<img  src= \" pie.php?I=1&W=$wid &len=$maxNAME\" /> ";
				echo "</div>";
			 }

		
//////////*****************************************************************************下載
		if ($_SESSION['Username']==$undertaker||$admin==1)
		{  $_SESSION['DD']=$id; 
			echo"  <form align=\"right\"  id=\"GoNextPage\" name=\"GoNextPage\" method=\"get\" action=\"v_CSVdownload.php?ID=<?$id?>\">
		<input name=\"OK\" type=\"submit\" class=\"btn btn-sm btn-info active \" value=\"下載\" /><p></p>
		</form>";
		}	
//////////*****************************************************************************下載

$row_col=floor(12/$row);

if ($type==1)
echo "<table class=\"table table-bordered\">";
echo"<div align=\"center\" id=\"all_work\">";
for($i=0;$i<$nrows1;$i++)
{

					if($type!=2){
					switch($detail)
					{
						case 00 : $context="<h5>作品編號: $no[$i]<br>$des_name[$i]</h5>";break;
						case 01 : $context="<h5>作品編號: $no[$i]<br>$des_name[$i]<br>創作理念 : $description[$i]</h5>";break;
						case 10 : $context="<h5>作品編號: $no[$i]<br>$des_name[$i]<br>$des_author[$i]</h5>";break;
						case 11 : $context="<h5>作品編號: $no[$i]<br>$des_name[$i]<br>$des_author[$i]<br>創作理念 : $description[$i]</h5>";break;
					}
				}//if(type!=2)
				else{
					switch($detail)
					{
						case 00 : $context="";break;
						case 01 : $context="<h5>創作理念 : $description[$i]</h5>";break;
						case 10 : $context="<h5>$des_author[$i]<br></h5>";break;
						case 11 : $context="<h5>$des_author[$i]<br>創作理念 : $description[$i]</h5>";break;
					}
				}//else
////////////***************************************************************************顯示資料
			
	if ($type==1)  //////////CANDIDATE*****************************************
	{ 
		if ($i%$row==0)
			echo "<tr>";

		echo "<td><table width='100%'><tr>";
		echo "<td width='80%' ><label><font color=\"blue\"   >$Name[$i]</font><p style=\"font-size:12px;\" >($department[$i])</p></label></td>";
		
		echo "<td align='right' ><font style=\"font-size:12px;\" 　color=\"gray\"　>$voteN[$i] 票</font></td>";
				
		echo "</tr></table></td>";
		if ($i%$row==($row-1))
			echo "</tr>";
				
	}   //($type==1)  //CANDIDATE_END

	else
	{
		if($type!=2)
					echo "<div class=\"col-lg-$row_col col-md-$row_col col-xs-$row_col col-sm-$row_col\" style=\"height:$height;\"><div class=\"work\" style=\"height:$w_height;\" >";
				else
					echo "<div class=\"col-lg-$row_col col-md-$row_col col-xs-$row_col col-sm-$row_col\" style=\"height:250px\">";

		if ($type==4||$type==5||$type==6)  	
		{		
			echo "<div class=\"resize\" >
				<a href=\"./images/$file_name[$i]\" title=\"$context\" class=\"html5lightbox\" data-group=\"mygroup\" >";
			if ( $type==4 )
				echo "<img src='./images/$file_name[$i]' height='$w_height'>";  
			else if  ($type==5)
				echo "<video  width ='400/$row' height= '300/$row'  controls>
						<source src=\"./images/$file_name[$i]\" type=\"video/mp4\">
						Your browser does not support the video tag.
					  </video>";
				echo"</a></div>";/////resize
		}

		else if ($type==3)
		{
			$file_len=strlen($file_name[$i]);
			$file_type=$file_name[$i][$file_len-3];

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


		}
		else ////type 2
		{	
			echo "<div class='panel panel-primary'> ";
			echo "<div class='panel-heading'>$Name[$i]</div>";
			echo "<div class='panel-body' style=\"height:75px;\">$slogan[$i]</div>"; //panel-body_END
			echo"<div class='panel-footer ' align=\"center\" ><span align='left'>$context </span> 　$voteN[$i] 票		</div>
				</div>";
		echo " </div>";//<div class=\"col-lg-$row_co
		}//else  type 2

				if($type!=2){
					
					echo "<font color=\"blue\">作品編號 : $no[$i]</font>";
					// ANONYMOUS--------------------------------------------------
					if($detail=="00" || $detail=="01")
						echo "<div><a href=\"./images/$file_name[$i]\" title=\"$context[$i]\" class=\"html5lightbox\" data-group=\"mygroup_more\" >$des_name[$i]<br><span>詳細資訊</span></a></div>"; //div
					else
						echo "<h5>$des_name[$i]<br>$des_author[$i]</h5><div><a href=\"./images/$file_name[$i]\" title=\"$context[$i]\" class=\"html5lightbox\" data-group=\"mygroup_more\" ><span>詳細資訊</span></a></div>"; //div
					// hyperlink----------------------------------------------------
					if($net_addr[$i]!=NULL)
						echo "<div><a href=\"$net_addr[$i]\" target=\"blank\"><span>原始檔案連結</span></a></div>";
				}

				echo "<br><font   size=\"4\" valign=\"bottom\">$voteN[$i] 票</font>";
				if($type!="2")
					echo "</div>"; //div=work
					
				echo "</div>"; //div style=height
	}  //else   type !=1

		
		
} ///for 

if ($style==1)
  echo "</table>";

//echo "</div>";
?>
	 

	
			</div>  <!-- <div class="container-fluid"  align='left'>-->

		</div> <!--<div class="panel-calendar"  >-->
 </div> <!--<div class="col-md-12"  >-->
 </div>  <!--<div class="row"  >-->
            </div><!--<div class="container"  >-->
    
    <!-- /.container >

    <div class="container">

        <hr>

        <!-- Footer >
        <footer>
		

        </footer>

    </div>
    <!-- /.container -->

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

<style type="text/css">
<? for($boot=1;$boot<=$other_counter;$boot++) {?>	
	.text<?echo $boot;?>{
		height: 25px;
		overflow: hidden;
	}
	<?}?>
</style>
</html>
<?  }
else {
	echo "<script language = JavaScript>";
	echo "alert(\"此問卷目前未開放查看結果！\");"; 
	echo "window.location.href='index.php'";
	echo "</script>"; 
	exit;
}
?>
</body>