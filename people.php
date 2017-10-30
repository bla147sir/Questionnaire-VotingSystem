<?php session_start();
include("connect.php");

date_default_timezone_set('Asia/Taipei');			
$now=date("YmdHis");




$id=$_GET['ID'];
$QV=$_GET['QV'];


				$k=$_SESSION['Username'];

$sql4="select * from MEMBER where IDENTITY='$k'";       
 
$stmt4=ociparse($con,$sql4); 
ociexecute($stmt4,OCI_DEFAULT); 
$nrows4 = OCIFetchStatement($stmt4,$results4);

$identity = $results4['IDENTITY'][0] ;    //帳號
$status = $results4['STATUS'][0] ;   //身分

if($nrows4!=1&&$status!=1)
{	if ($QV=='Q')
		header("Location:questionnaireAll.php");
	else
		header("Location:voteAll.php");
}
else if($_SESSION['Username']==null)
{
header("Location:index.php");
}









?>

<!DOCTYPE html>
<html lang="en">
<head>
  
    <title>國立彰化師範大學線上問卷及投票系統</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


     <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <!-- Custom CSS -->    
	<link href="css/ncue.css" rel="stylesheet">
	<link href="css/index_label.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
   
    <script type="text/javascript" src="./js.JScript"></script>
	<script type="text/javascript" src="js/index_label.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery.min.js"></script>

</head>

<body onLoad="document.forms.form.user_id.focus()" >

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

<?
if($QV=='Q') {
	$sql1="select * from identity where ID='$id' ORDER BY TIME ASC";
	$sql="select * from QUESTIONNAIRE where ID='$id' ";
}
else if($QV=='V') {
	$sql1="select * from ELECTION_INFO where ID='$id' ORDER BY TIME ASC";
	$sql="select * from VOTE where ID='$id' ";
}
else
{
	echo "<script language = JavaScript>";
	echo "alert(\"無此活動！\");"; 
	echo "window.location.href='index.php'";
	echo "</script>"; 
	exit;
}

	$stmt1=ociparse($con,$sql1); 
	ociexecute($stmt1,OCI_DEFAULT); 
	$nrows1 = OCIFetchStatement($stmt1,$results1); 

	$stmt=ociparse($con,$sql); 
	ociexecute($stmt,OCI_DEFAULT); 
	$nrows = OCIFetchStatement($stmt,$results); 
	$title = $results['TITLE'][0];
	$title= iconv("BIG5","UTF-8",$title);
	

echo "<div id=\"a1\"><h4 style=\"font-weight: bolder;\" class=\"inline\">$title - 參與活動者名單</h4>
		</div>  ";
if ($nrows1 ==0)
{
	 echo "<br>　　<span class='glyphicon glyphicon-exclamation-sign'></span>  目前尚未有人投票 ";
}
else
{
	 echo "<div> 目前共有 <font color='red'>$nrows1 </font>人已參與 </div>";
?>
<br>	<table width="600"  >
                     <tr align="center" bgcolor="#CEE0EC" >
							<td align="center">帳號</th>
							<td align="center">姓名</td>
							<td align="center">單位/系級</td>
						</tr>
                     
<?     

		for($j=0;$j<$nrows1;$j++) 
		{                                 
          $user_id_tmp=$results1[IDENTITY][$j];
		  /* 活動結束後開放 add by boblee!(for production environment)
		  if (ereg("[smdaSMDA9][0-9]{7,9}",$user_id_tmp ) ) 
		  {
			   include("/home/bob/common/connect_dean.php");  //學生	   'XZ'為交換學生  XXX94XXX
			   $sql="select stu_name,unt_lname   from dean.s30_student a,dean.s90_unit b  where a.stu_id='$user_id_tmp' and substr(a.last_cls,2,3)=b.unt_id ";  
			   //echo $sql ;
			   $stmt=ociparse($con,$sql); 
			   ociexecute($stmt,OCI_DEFAULT); 				  
			   $nrows2 = OCIFetchStatement($stmt,$results2); 
			   $user_name = $results2['STU_NAME'][0] ;
			   $dept_name = $results2['UNT_LNAME'][0] ;
		  }
		  else
		  {
			  $con=OCIPLOGON("od","boblee0911","sun450") ; //教職員
			   $sql="select a.empl_chn_name ,c.dept_full_name,dept_no 
				   from per.psfempl a ,per.psfcrjb b ,per.stfdept c
				   where substr(a.email,1,instr(a.email,'@',1,1)-1)='$user_id_tmp'  and a.empl_no=b.crjb_empl_no 
				   and b.crjb_quit_date is null and b.crjb_depart=c.dept_no and crjb_seq='1' ";       
			   $stmt=ociparse($con,$sql); 
			   ociexecute($stmt,OCI_DEFAULT); 				  
			   $nrows3 = OCIFetchStatement($stmt,$results3); 
			   $user_name = $results3['EMPL_CHN_NAME'][0] ;
			   $dept_name = $results3['DEPT_FULL_NAME'][0] ;
		  }
		  */

			$user_name= iconv("BIG5","UTF-8",$user_name);
			$dept_name= iconv("BIG5","UTF-8",$dept_name);
		  echo "<tr><td align=\"center\">$user_id_tmp </td><td align=\"center\">$user_name </td><td align=\"center\">$dept_name </td></tr>";

		}


?>
<? 
/*
				for($i=0; $i<$nrows1 ;$i++) 
				{											
					$identity = $results1['IDENTITY'][$i] ;
					$array[$i]=$identity;

					$time = $results1['TIME'][$i] ;
					$t[$i]=$time;

					$y=(int)($t[$i]/10000000000);
					$m=(int)(($t[$i]-($y*10000000000))/100000000);
					$d=(int)(($t[$i]-($y*10000000000)-($m*100000000))/1000000);
					$h=(int)(($t[$i]-($y*10000000000)-($m*100000000)-($d*1000000))/10000);
					$min=(int)(($t[$i]-($y*10000000000)-($m*100000000)-($d*1000000)-($h*10000))/100);
					$s=(int)(($t[$i]-($y*10000000000)-($m*100000000)-($d*1000000)-($h*10000)-($min*100))/1);
					
					$ii=$ii+1;

					$k=$array[$i];

				if($s<10)
					$s='0'+"$s";

					if($ii%2==0)
					echo "<tr align=\"center\" bgcolor=\"#EEEEDE\" >";
					else
					echo "<tr align=\"center\" bgcolor=\"#FFFFFF\" >";
					echo "<th width=\"30\" scope=\"row\">$ii</th>";
					echo "<td align=\"center\" >$array[$i]</td>";
					echo "<td>"."$y"."/"."$m"."/"."$d"." $h".":"."$min".":"."$s"."</td>";
					echo "</tr>";

				}

*/
?>

                       </table>
					   
					   <br><br><br>
					   <?$people="people_CSVdownload.php"; ?>
					    <form align="right"  id="GoNextPage" name="GoNextPage" method="get" action=<?echo "$people"?> >
						<input name="OK" type="submit" class="btn btn-sm btn-info active " value="下載" /><p></p>
						<? $_SESSION['NUMBER']=$id; $_SESSION['QV']=$QV; ?>
						</form>

					   <?}?>
			</div>  <!--  <div id="panel-calendar" class="panel panel-primary dd">	 -->
			</div><!-- <div class="container-fluid"  align='left'> -->
            </div>  <!--  <div class="col-md-12"  > -->
			</div><!-- <div class="row" style=" height:500px; "  > -->
			</div><!--   <div class="container container_ncue"  valign="bottom"  >  -->


   <? include("footer.html");?>
    <!-- /.container -->

</body>

</html>
