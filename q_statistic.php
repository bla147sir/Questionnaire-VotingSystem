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
 $dd=$_GET['ID'];

$_SESSION['ID']=$dd;
$sql="select * from questionnaire where ID='$dd'";
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
	
    <link href="css/bootstrap.css"  rel="stylesheet" type="text/css"/>
	<link href="css/ncue.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="js/vendor/jquery/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/jquery.hoverpulse.js"></script>
	
 <?php 
    	echo"<link rel=\"stylesheet\"  href=\"css/bg$style.css\" />";
    ?>

    

    <!-- Bootstrap Core JavaScript -->
   



</style>

</head>

<body onLoad="document.forms.form.user_id.focus()">

  <div class="container container_ncue"  >
		<div class="row">
            <div class="col-md-12">				
				<div id="banner">
				</div>	
			<?	include("test_menu.php");?>
			</div>
		</div>
<br>
        <div class="row" style=" height:350px; " align="center" >
            <div class="col-md-1" >
			<?	///include("vote_menu.php");?>
            </div> 
            <div class="col-md-11" align='center' >
<div class="contain">
		
      
	<? $id=$_GET['ID'];?>

	<?php
		$sql1="select TITLE from questionnaire where ID='$id' "; 
	

	 
	 $stmt1=ociparse($con,$sql1); 
	 ociexecute($stmt1,OCI_DEFAULT); 
	   
	 $nrows1 = OCIFetchStatement($stmt1,$results1); 

		 $title = $results1['TITLE'][0];
		 $title= iconv("BIG5","UTF-8",$title);

	   $sql1="select distinct IDENTITY from IDENTITY where ID='$id' "; 
					 $stmt1=ociparse($con,$sql1); 
	 ociexecute($stmt1,OCI_DEFAULT); 
	   
	 $nrows1 = OCIFetchStatement($stmt1,$results1); 
 echo "<div></div>";
 $other_counter=0;
		
	   echo "<div id=\"dd\"><div id=\"a1\"><h3><br>$title<br><br></h3><h5 align=\"right\" ><br>本分問卷總填寫人數 : $nrows1<br></h5></div></div>";

	

   


	$ssql="select DISTINCT PART from question where ID='$id' ";   
	
    $sstmt=ociparse($con,$ssql); 

	 ociexecute($sstmt,OCI_DEFAULT);

	 $nrowss = OCIFetchStatement($sstmt,$resultss);
	 
 $ar = array("零","一", "二", "三", "五", "六", "七", "八", "九", "十") ;
	
	 for($kk=0; $kk<$nrowss ;$kk++)
	{$k=$kk+1;
	   if ($nrowss >1)
		echo "<h3>第".$ar[$k]."部分</h3>";

	$sql2="select * from question where ID='$id' AND PART='$k' ORDER BY NO ASC ,NON ASC";       
 
	 $stmt2=ociparse($con,$sql2); 
	 ociexecute($stmt2,OCI_DEFAULT);

	 $nrows2 = OCIFetchStatement($stmt2,$results2); 
	 for($i=0; $i<$nrows2 ;$i++) 
	 {  
	 $Q = $results2['QUESTION'][$i] ;
	 $Q= iconv("BIG5","UTF-8",$Q);
	 $no= $results2['NO'][$i] ;
	 $non= $results2['NON'][$i] ;
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($results2['NON'][$i]==0)
	echo "<h3><br>$no . $Q </h3>";
	else if ($results2['TYPE'][$i]==6||$results2['TYPE'][$i]==3||$results2['TYPE'][$i]==4||$results2['TYPE'][$i]==5)
	 {echo "<div class=\"contain\" align='left'>";
	
	 if ($results2['NO'][$i] ==$results2['NO'][$i-1] ||$results2['NO'][$i] ==$results2['NO'][$i+1])
	{echo "<h4><br>$no . $non . $Q </h4>";}
	 else
	{
		echo "<h4><br> $no . $Q </h4>";
	 }
	      
			///////////////////////////選項
		$sql3="select * from OPT where ID='$id' AND PART='$k' AND NO='$no'  ORDER BY NUM ASC"; 
		
		 $stmt3=ociparse($con,$sql3); 
		 ociexecute($stmt3,OCI_DEFAULT);
		 $nrows3 = OCIFetchStatement($stmt3,$results3);
		$max[$k][$i]=0;
		$maxSlen=0;
			///////tabl
		 for($j=0; $j<$nrows3 ;$j++) 
		 { $opt[$k][$i][$j]=$results3['OPTION_VALUE'][$j] ;
		 $opt[$k][$i][$j]= iconv("BIG5","UTF-8",$opt[$k][$i][$j]);
		   $num= $results3['NUM'][$j] ;
		//////////////////////////計數器
		$sql4="select COUNT(*) from ANS_OPT where ID='$id' AND PART='$k' AND NO='$no' AND NON='$non' AND ANSWER=$num" ;
    
		 $stmt4=ociparse($con,$sql4); 
		 ociexecute($stmt4,OCI_DEFAULT);
		 $nrows4 = OCIFetchStatement($stmt4,$results4);
         $cou[$k][$i][$j]=$results4['COUNT(*)'][0] ;
		 
		 if (strlen($opt[$k][$i][$j])>$maxSlen)
			 $maxSlen=strlen($opt[$k][$i][$j]);

		 if ($max[$k][$i]<$cou[$k][$i][$j])
			 $max[$k][$i]=$cou[$k][$i][$j];
		$m=m.$k.$i;
		$n=n.$k.$i;
		$maxi=maxi.$k.$i;
			 
		 } $wid=$nrows3*30+50;

		  if  ($results2['OTHER'][$i]==1)
		 {	$other_counter=$other_counter+1;
			  $sql6="select * from ANS_OPT where ID='$id' AND PART='$k' AND NO=$no AND NON=$non AND ANSWER=$nrows3+1";       
		$stmt6=ociparse($con,$sql6); 
		 ociexecute($stmt6,OCI_DEFAULT);
		 $nrows6 = OCIFetchStatement($stmt6,$results6); 
			$nrows3=$nrows3+1;
		        $cou[$k][$i][$j]=$nrows6;
				$opt[$k][$i][$j]="其他";
				//echo $nrows6."其他";
				 if ($max[$k][$i]<$cou[$k][$i][$j])
			 $max[$k][$i]=$cou[$k][$i][$j];
				 $m=m.$k.$i;
		$n=n.$k.$i;
		$maxi=maxi.$k.$i;
	
		$wid=$wid+30;
		 }


		  $in=$k.$i;
			 $_SESSION["$m"]=$opt[$k][$i];
			 $_SESSION["$n"]=$cou[$k][$i];
			 $_SESSION["$maxi"]=$max[$k][$i];
		
			
		
echo "<table   ><tr>" ;	

          echo "<td>　　　　<div class=\"counter\"><table   class=\"table table-bordered\" width=\"auto\" rules=\"all\" cellpadding='5'><tr><td colspan=\"3\"    class=\"warning\" align=\"center\">人數統計</td><tr>";
		for($j=0; $j<$nrows3 ;$j++) 
		{ $jj=$j+1;
			echo  "<tr><td>$jj</td><td width=\"auto\">".$opt[$k][$i][$j]."</td><td>".sprintf("%d",$cou[$k][$i][$j])."</td></tr>";
		  $sum[$k][$i]=$sum[$k][$i]+$cou[$k][$i][$j];
		  }
		
		echo "<tr class=\"warning\" ><td   colspan=\"2\"   align=\"center\">總票數</td><td>";
		echo sprintf("%d",$sum[$k][$i])."</td></tr>";
		
		$sql6="select distinct IDENTITY  from ANS_OPT where ID='$id' ";  //人數統計     
		$stmt6=ociparse($con,$sql6); 
		 ociexecute($stmt6,OCI_DEFAULT);
		 $nrows6 = OCIFetchStatement($stmt6,$results6); 
		echo "<tr class=\"warning\" ><td   colspan=\"2\"   align=\"center\">總人數</td><td>";
		echo $nrows6 ;
			echo "</td></tr></table></div></td>";
		if ($max[$k][$i]!=0)
		 {
		echo "<td width=\"300+$maxSlen*3\"  align=\"center\"><div  class=\"bar\" style=\"height:".$wid."\">";
		if ($chart==2)
		echo "<img src= \" bar.php?I=$in&W=$wid &len=$maxSlen \" /> ";
		if ($chart==1)
		echo "<img src= \" pie.php?I=$in&W=$wid &len=$maxSlen\" /> ";
		 }
		echo "</div></td></tr>";
    
		if ($results2['OTHER'][$i]==1)
		 {	
			echo "<tr><td>";
			echo "<p>其他 : </p>  ";
			
		   echo  "<div class=\"text$other_counter\">";

		 $sql6="select * from TEXT where ID='$id' AND PART='$k' AND NO=$no AND NON=$non ";       
		 $stmt6=ociparse($con,$sql6); 
		 ociexecute($stmt6,OCI_DEFAULT);
		 $nrows6 = OCIFetchStatement($stmt6,$results6); 

		 for($p=0; $p<$nrows6 ;$p++) 
		 {   $z=$p+1;
			$answer=$results6['ANSWER'][$p];
		    $answer= iconv("BIG5","UTF-8",$answer);
		 	echo "　　".$z. ".　".$answer ."<br>" ;
		 }

		 echo "</div>";
		 echo "<div class=\"button$other_counter\" > <button class=\"btn btn-success  btn-sm\" width=\"100\" >更多</button> &nbsp;  &nbsp;  &nbsp; </div>";
		 echo "</td></tr>";
	  }
		  echo "</table>";
echo "</div>";

	 }////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	else
	 {	echo "<h4 align='left' > $no . $Q </h4>";
	$other_counter=$other_counter+1;
		 echo  "<div class=\"text$other_counter\" align='left' >";
		 $sql5="select * from TEXT where ID='$id' AND PART='$k' AND NO=$no AND NON=$non"; 
		
		$stmt5=ociparse($con,$sql5); 
		ociexecute($stmt5,OCI_DEFAULT);
		
		$nrows5 = OCIFetchStatement($stmt5,$results5); 
 
		for($p=0; $p<$nrows5 ;$p++) 
		{   $P= $p+1;
 
			$answer=$results5['ANSWER'][$p];
		    $answer= iconv("BIG5","UTF-8",$answer);

			echo "　　".$P. ".　". $answer."<br>" ;
		}
		echo "</div>";
		echo "<div class=\"button$other_counter\" align='left' > <button class=\"btn btn-sm btn-success \" width=\"0\" >更多</button>  &nbsp;  &nbsp;  &nbsp; </div>";
		}
	 }


		} 
		
	

	?>


 		<?
		if ($_SESSION['Username']==$undertaker||$admin==1)
		{  ?>
            <div class="row">
                <div class="col-lg-12">
                    <form align="right"  id="GoNextPage" name="GoNextPage" method="get" action="q_CSVdownload.php?ID=<?$id ?>">
<input name="OK" type="submit" class="btn btn-sm btn-info active " value="下載" /><p></p>
<? $_SESSION['MOMO']=$id;?>
</form>
                </div>
            </div>
			<?}?>

            </div>

      

    </div>    
    <!-- /.container -->

    <div class="container">

        <hr>

        <!-- Footer -->
        <footer>
		

        </footer>

    </div>
    <!-- /.container -->

  

</body>



<script type="text/javascript">

			$(function() {
<? for($boot=1;$boot<=$other_counter;$boot++) {?>

			$('.button<? echo $boot; ?>').click(function() {
			
				var $div<? echo $boot; ?> = $('div.text<? echo $boot; ?>');
				// 先取得是否有記錄在 .data('contentHeight') 中
				var contentHeight<? echo $boot; ?> = $div<? echo $boot; ?>.data('contentHeight<? echo $boot; ?>');
			
				// 若沒有記錄
				if(!!!contentHeight<? echo $boot; ?>){
					// 取得完整的高
					contentHeight<? echo $boot; ?> = determineActualHeight($div<? echo $boot; ?>);
					// 並記錄在 .data('contentHeight') 中
					$div<? echo $boot; ?>.data('contentHeight<? echo $boot; ?>', contentHeight<? echo $boot; ?>);
					}
		
				// 進行折疊
				$div<? echo $boot; ?>.stop().animate({ 
					height: (contentHeight<? echo $boot; ?> == $div<? echo $boot; ?>.height() ?25: contentHeight<? echo $boot; ?>)
				}, 1000);
			});

			function determineActualHeight($div<? echo $boot; ?>) {
				var $clone<? echo $boot; ?> = $div<? echo $boot; ?>.clone().hide().css('height', 'auto').appendTo($div<? echo $boot; ?>.parent()),
				height = $clone<? echo $boot; ?>.height();
				$clone<? echo $boot; ?>.remove();
				return height;
				}
			<?}?>})
				
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