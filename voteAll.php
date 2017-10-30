<? 
session_start(); 
$_SESSION['QV']=1;
include("check.php");
include("connect.php");

$done=$_GET['done'];
$yet=$_GET['yet'];
$open=$_GET['open'];



if ($open==0&&$yet==0&&$done==0)
{$open=1;
	$yet=0;
 $done=0;

}
else if ($open!=0)
{
  $yet=0;
 $done=0;
}
else if ($done!=0)
{$open=0;
 $yet=0;
}
else if ($yet!=0)
{$open=0;
 $done=0;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>國立彰化師範大學線上問卷及投票系統</title>

     <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <!-- Custom CSS -->    
	<link href="css/ncue.css" rel="stylesheet">
	<link href="css/index_label.css" rel="stylesheet">
	<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="./js.JScript"></script>
	<script type="text/javascript" src="js/index_label.js"></script>
   <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery.min.js"></script>
 <script>

function open(obj){
	location.href="questionnaireAll.php?open="+obj;
}
</script>
<script>
function done(obj){
	location.href="questionnaireAll.php?done="+obj;
}
</script>
<script>
function yet(obj){
	location.href="questionnaireAll.php?yet="+obj;
}
</script>

</head>
<?php  


$k=$_SESSION['Username'];
$sql8="select * from member where IDENTITY='$k'";       
 
$stmt8=ociparse($con,$sql8); 
ociexecute($stmt8,OCI_DEFAULT); 
	  
$nrows8 = OCIFetchStatement($stmt8,$results8);

if($nrows8!=1)
{	
	header("Location:index.php");
	exit;
}
if ($results8['STATUS'][0]==1)
	$disabled="";
else
	$disabled="disabled";

?>
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

			
            <div class="col-md-12" >

							<?
							
							date_default_timezone_set('Asia/Taipei');
							$now=date("YmdHis");
						
							?>
				<div id="vote-block" style=" vertical-align:top ;" align='center' >
                   
                       <div class="ttt">
						<ul class="tabs">
						  <li class='vote <?if ($open!=0) echo "active"; ?>' ><span>開放中</span></li>
					      <li class='vote <?if ($done!=0) echo "active"; ?>' ><span>已截止</span></li>
						  <li class='vote <?if ($yet!=0) echo "active"; ?>'  ><span>尚未開始</span></li>
						</ul>
                      </div>
					
					<div class="tab_container">
					 <ul class="tab_content" >


		<!--________________________________________投票_______________________________________________ -->
						<li style="left: 0px;" >

	
                    
				<?php
 $QV=1;
				//預設每頁筆數(依需求修改)
				$pageRow_records = 10;
				$k=$_SESSION['Username'];
				
				
				$aa="select * from member where IDENTITY='$k'"; 
				 $stmtaa=ociparse($con,$aa); 
				 ociexecute($stmtaa,OCI_DEFAULT); 
				$nrowsaa = OCIFetchStatement($stmtaa,$resultsaa); 

				if ($nrowsaa==1&&$resultsaa['STATUS'][0]==1)
				$sql1="select * from vote where datetime<$now AND DUE>$now  AND done=2 AND not ID=1030001 order by datetime desc ";
				else
				$sql1="select * from vote where ACCOUNT='$k' AND  datetime< $now AND DUE>$now  AND done=2 AND not ID=1030001 order by ID";



				
					 $stmt1=ociparse($con,$sql1); 
				 ociexecute($stmt1,OCI_DEFAULT); 
				$nrows1 = OCIFetchStatement($stmt1,$results1);
				if ($nrows1==0)
					echo "<p class=\"bg-info\" ><br>　　　　目前未有開放中之活動</br></br></p>";
				else
					{
						echo "<table width=\"900\"  >";
		
		$total_records=$nrows1;
$ii=0;
 //預設頁數
 $num_pages = 1;
 //若已經有翻頁，將頁數更新
  if ($open!=0)
$num_pages=$open;

 //本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
 $startRow_records = ($num_pages -1) * $pageRow_records;

 //計算總頁數=(總筆數/每頁筆數)後無條件進位。
 $total_pages = ceil($total_records/$pageRow_records);

				?>

					 <tr align="center" bgcolor="#CEE0EC" >
							
							<td align="center" height="30"  width='300' >活動名稱</td>
							<td  width='100'>開始日期</td>
							<td width='100' >結束日期</td>
							<td width='100' >主辦單位</td>
							<td width='80' >承辦人</td>
							<td  width='70' >看結果</td>
							<td  width='50' >填寫人</td>
							<td  width='50' >修改</td>
							<td width='50' >刪除</td>
						</tr>
				<?


$i=$startRow_records;
				for($i=$startRow_records; $i< $startRow_records+$pageRow_records&&$i<$nrows1;$i++) 
				{

					
					include("statisticInclude.php");
					

					echo "<td align=\"center\"> $date </td>";
					echo "<td  align=\"center\"> $due </td>";
					echo "<td > $department </td>";
					echo "<td > $undertaker </td>";
					echo "<td w>";
					echo "<a href=\"v_statistic.php?ID=$id\">看結果</a>";
				    echo "</td><td ><a href=\"people.php?ID=$id&QV=V\">看名單</a></td>" ;
					echo"<td ><a href=\"$modify?ID=$id\"><button type=\"button\" class=\"btn btn-success btn-xs\"  $disabled >修改</button></td>";
					echo "<td ><form method='POST' name=\"delete\" id=\"delete\"  action=\"delete.php?ID=$id\" onSubmit=\"return confirm('確定刪除 ?')\" >
					<button type=\"submit\" class=\"btn btn-primary btn-xs\" name=\"send\" id=\"send\"  >刪除</button> </form>   </td></tr>";
					//
					
}
?>

</table>

						
					   
<? 
	$page_num=ceil(($nrows1)/$pageRow_records);
if ($page_num>1)
{
	echo "<br><div class=\"btn-group\" role=\"group\" aria-label=\"First group\">";
	for ($i=1;$i<=$page_num;$i++)
	{	
	echo "<button type=\"button\" class=\"btn btn-default";
	 if ($open==$i)
		 echo "active";
	 echo "\" value=\"$i\" onclick=\"open(this.value)\">$i</button>";
	}     
	echo "</div>";
}
}?>

							
</li >
<!--________________________________________投票中_______________________________________________ -->

<!--________________________________________完成投票_______________________________________________ -->
<li  style="left: -1000px;">
		<table width="900"  >
                     <tr align="center" bgcolor="#CEE0EC" >
				
							<td align="center" height="30"  width='400' >活動名稱</td>
							<td  width='100'>開始日期</td>
							<td width='100' >結束日期</td>
							<td width='80' >主辦單位</td>
							<td width='70' >承辦人</td>
							<td  width='50' >看結果</td>
							<td  width='50' >填寫人</td>
							<td width='50' >刪除</td>
						</tr>
				<?php

				$k=$_SESSION['Username'];

				$aa="select * from member where IDENTITY='$k'"; 
				 $stmtaa=ociparse($con,$aa); 
				 ociexecute($stmtaa,OCI_DEFAULT); 
				$nrowsaa = OCIFetchStatement($stmtaa,$resultsaa); 
				if ($nrowsaa==1&&$resultsaa['STATUS'][0]==1)
				{$sql1="select * from vote where DUE<$now  AND done=2  AND NOT ID=1030001 order by ID"; }
				else
				{$sql1="select * from vote where ACCOUNT='$k'  AND DUE<$now  AND done=2   AND NOT ID=1030001 order by ID"; }

					 $stmt1=ociparse($con,$sql1); 
				 ociexecute($stmt1,OCI_DEFAULT); 
				$nrows1 = OCIFetchStatement($stmt1,$results1);

$total_records=$nrows1;
$ii=0;


 //預設頁數
 $num_pages = 1;
 //若已經有翻頁，將頁數更新
if ($done!=0)
 $num_pages=$done;

 //本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
 $startRow_records = ($num_pages -1) * $pageRow_records;

 //計算總頁數=(總筆數/每頁筆數)後無條件進位。
 $total_pages = ceil($total_records/$pageRow_records);
$i=$startRow_records;
				for($i=$startRow_records; $i< $startRow_records+$pageRow_records&&$i<$nrows1;$i++) 
				{
	include("statisticInclude.php");
			

					echo "<td  align=\"center\"> $date </td>";
					echo "<td  align=\"center\"> $due </td>";
					echo "<td > $department </td>";
					echo "<td > $undertaker </td>";
					echo "<td ";
					echo "<a href=\"v_statistic.php?ID=$id\">看結果</a>";
				    echo "</td><td ><a href=\"people.php?ID=$id&QV=V\">看名單</a></td>" ;
					echo "<td ><form method='POST' name=\"delete\" id=\"delete\"  action=\"delete.php?ID=$id\" onSubmit=\"return confirm('確定刪除?')\" >
					<button type=\"submit\" class=\"btn btn-primary btn-xs\" name=\"send\" id=\"send\" >刪除</button> </form>   </td></tr>";
					//}
}
?>

						
					</table>
      
<? 
	$page_num=ceil(($nrows1)/$pageRow_records);
if ($page_num>1)
{
	echo "<br><div class=\"btn-group\" role=\"group\" aria-label=\"First group\">";
	for ($i=1;$i<=$page_num;$i++)
	{	
	echo "<button type=\"button\" class=\"btn btn-default";
	 if ($done==$i)
		 echo "active";
	 echo "\" value=\"$i\" onclick=\"done(this.value)\">$i</button>";
	}     
	echo "</div>";
}

?>	
						

</li >
 <!--________________________________________完成投票_______________________________________________ -->


 <!--________________________________________未完成投票_______________________________________________ -->

<li  style="left: 1000px;">
		<table width="900"  >
                     <tr align="center" bgcolor="#CEE0EC" >
							<td align="center"  height="30" width='350'>活動名稱</td>
							<td width='100' >開始日期</td>
							<td width='100' >結束日期</td>
							<td width='100' >主辦單位</td>
							<td width='80' >承辦人</td>
							<td width='50' >修改</td>
							<td width='50' >刪除</td>
							<td width='70' >狀態</td>
						</tr>
				<?php

				$k=$_SESSION['Username'];


				$aa="select * from member where IDENTITY='$k'"; 
				 $stmtaa=ociparse($con,$aa); 
				 ociexecute($stmtaa,OCI_DEFAULT); 
				$nrowsaa = OCIFetchStatement($stmtaa,$resultsaa); 
				if ($nrowsaa==1&&$resultsaa['STATUS'][0]==1)
				{$sql1="select * from vote where ( DATETIME>$now  OR done<2 )  AND NOT ID=1030001 order by ID"; }
				else
				{$sql1="select * from vote where ACCOUNT='$k'  AND ( DATETIME>$now  OR done<2 )  AND NOT ID=1030001 order by ID"; }


					 $stmt1=ociparse($con,$sql1); 
				 ociexecute($stmt1,OCI_DEFAULT); 
				$nrows1 = OCIFetchStatement($stmt1,$results1);

$total_records=$nrows1;
$ii=0;/*

*/
 //預設頁數
 $num_pages = 1;
 //若已經有翻頁，將頁數更新
if ($yet!=0)
 $num_pages=$yet;

 //本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
 $startRow_records = ($num_pages -1) * $pageRow_records;

 //計算總頁數=(總筆數/每頁筆數)後無條件進位。
 $total_pages = ceil($total_records/$pageRow_records);

$i=$startRow_records;
				for($i=$startRow_records; $i< $startRow_records+$pageRow_records&&$i<$nrows1;$i++) 
				{
					include("statisticInclude.php");
					$done = $results1['DONE'][$i] ;

					echo "<td  align=\"center\"> $date </td>";
					echo "<td  align=\"center\"> $due </td>";
					echo "<td > $department </td>";
					echo "<td > $undertaker </td>";
					$next=$done+1;
					echo "<td > ";
					if($done<4)
						echo"<a href=\"design".$next.".php?ID=$id\"><button type=\"button\" class=\"btn btn-success btn-xs\" >";
					else
						echo"<a href=\"$modify?ID=$id\"><button type=\"button\" class=\"btn btn-success btn-xs\" $disabled >";
					echo "修改</button></a> </td>";
					echo "<td ><form method='POST' name=\"delete\" id=\"delete\"  action=\"delete.php?ID=$id\" onSubmit=\"return confirm('確定刪除?')\" >
					<button type=\"submit\" class=\"btn btn-primary  btn-xs\" name=\"send\" id=\"send\">刪除</button> </form>   </td>";
					if($done<2)
						echo "<td >未完成</td>";
					else
						echo "<td  >未開放</td>";
					echo "</tr>";
					//}
}
?>

						
					</table>
      
<? 
	$page_num=ceil(($nrows1)/$pageRow_records);
if ($page_num>1)
{
	echo "<br><div class=\"btn-group\" role=\"group\" aria-label=\"First group\">";
	for ($i=1;$i<=$page_num;$i++)
	{	
	echo "<button type=\"button\" class=\"btn btn-default";
	 if ($yet==$i)
		 echo "active";
	 echo "\" value=\"$i\" onclick=\"yet(this.value)\">$i</button>";
	}     
	echo "</div>";
}
 ?>


						

</li >

 <!--________________________________________未完成投票_______________________________________________ -->

 </ul  > <!--"tab_content" -->
	</div > <!--"tab_container" -->
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

  
<script>

$(#send).on('click',function{

var formURL = $("#form").attr("action");

$.ajax(
		{
			beforeSend: function (request) {
				if(!confirm('是否刪除？')) return false;
			},
			url : formURL,
			type: "POST",
			data : postData

})

</script>
<script>
$(#first).on('click',function{
<?
	$_SESSION['page']=2;?>
		})
	</script>
<script>
$(#TOpage).on('click',function{
<?
	$_SESSION['page']=$x;?>
		})



</script>

</body>



</html>