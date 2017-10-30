<?php session_start();
include("connect.php");

date_default_timezone_set('Asia/Taipei');			
$now=date("YmdHis");
 $php_self=$_SERVER['PHP_SELF'];//get current url

$indexAll=$_GET['all'];
$indexQ=$_GET['Questionnaire'];
$indexV=$_GET['Vote'];
if ($indexAll==0&&$indexQ==0&&$indexV==0)
{$indexAll=1;
	$indexQ=0;
 $indexV=0;
}
else if ($indexAll!=0)
{
  $indexQ=0;
 $indexV=0;
}
else if ($indexV!=0)
{$indexAll=0;
 $indexQ=0;
}
else if ($indexQ!=0)
{$indexAll=0;
 $indexV=0;
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
<script>

function ALL(obj){
	location.href="index.php?all="+obj;
}
</script>
<script>
function Questionnaire(obj){
	location.href="index.php?Questionnaire="+obj;
}
</script>
<script>
function Vote(obj){
	location.href="index.php?Vote="+obj;
}
</script>


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

<!-- ------------------分隔線---------------------- !!-->
<div id="vote-block" style=" vertical-align:top ;" align='center'>
                   
                       <div class="ttt">
						<ul class="tabs">
						
						  <li class='vote <?if ($indexAll!=0) echo "active"; ?> ' ><span  >開放中</span></li>
					      <li class='vote <?if ($indexV!=0) echo "active"; ?> '><span >　票選　</span></li>
						  <li class='vote <?if ($indexQ!=0) echo "active"; ?> '><span >　問卷　</span></li>
						  　
						 
						</ul>
                      </div>
					
					<div class="tab_container" >
					 <ul class="tab_content" >
<!--_________________________________________________所有活動_________________________________________________________-->
						<li style="left: <? if ($indexAll!=0) echo '0' ; else echo "1000px";?> ; " >

				<?php  

			//預設每頁筆數(依需求修改)
				$pageRow_records = 10;
				   $i=0;

				$sql1="select * from vote where datetime<$now AND DUE>$now AND done=2 AND NOT ID=1030001 order by datetime desc ,id asc ";
	
				 $stmt1=ociparse($con,$sql1); 
				ociexecute($stmt1,OCI_DEFAULT); 
				$nrows1 = OCIFetchStatement($stmt1,$results1); 

				$sql2="select * from questionnaire where datetime<$now AND DUE>$now  AND done=4 AND NOT ID=1030001 order by datetime desc,id asc ";
	
				 $stmt2=ociparse($con,$sql2); 
				ociexecute($stmt2,OCI_DEFAULT); 
				$nrows2 = OCIFetchStatement($stmt2,$results2);
				
				$V=0;
				$Q=0;
			
				
if ($nrows1+$nrows2==0)
					echo "<p class=\"bg-info\" ><br>　　　　目前未有開放中之活動</br></br></p>";
				else{
				if ($indexAll==0||$indexAll==1)
							$num_pages = 1;
				else
							$num_pages = $indexAll;

	include("indexInclude.php"); 
					}
		
$page_num=ceil(($nrows1+$nrows2)/$pageRow_records);
if ($page_num>1)
{
	echo "<br><div class=\"btn-group\" role=\"group\" aria-label=\"First group\">";
	for ($i=1;$i<=$page_num;$i++)
	{	
        echo "<button type=\"button\" class=\"btn btn-default";
		if ($indexAll==$i)
			echo "active";
			echo " \" value='$i' onclick=\"ALL(this.value)\">$i</button>";
	}     
     echo "</div>";
}
	  ?>
	  </li>


<!--_________________________________________________所有投票_________________________________________________________-->
						<li style="left:<? if ($indexV!=0) echo '0' ; else if($indexQ!=0) echo '-1000px' ; else echo '1000px';?> ;" >


				<?php 

				   $i=0;


				//$sql1="select * from vote where datetime<$now AND DUE>$now AND done=2 AND NOT ID=1030001 order by datetime ,id asc ";
				$sql1="select * from vote where datetime<$now AND done=2  AND NOT ID=1030001 order by datetime desc,id asc ";
	
				$stmt1=ociparse($con,$sql1); 
				ociexecute($stmt1,OCI_DEFAULT); 
				$nrows1 = OCIFetchStatement($stmt1,$results1); 

				$nrows2=0;
			




if ($nrows1==0)
					echo "<p class=\"bg-info\" ><br>　　　　目前未有開放中之投票</br></br></p>";
else{
	if ($indexV==0||$indexV==1)
							$num_pages = 1;
				else
							$num_pages = $indexV;
	include("indexInclude.php"); 
					}
$page_num=ceil($nrows1/$pageRow_records);
if ($page_num>1)
{
	echo "<br><div class=\"btn-group\" role=\"group\" aria-label=\"First group\">";
	for ($i=1;$i<=$page_num;$i++)
	{	
       echo "<button type=\"button\" class=\"btn btn-default";
		if ($indexV==$i)
			echo "active";
		echo "\" value='$i' onclick=\"Vote(this.value)\">$i</button>";
	}       
     echo "</div>";	
}


?>
					
					</li>
				
<!--_________________________________________________所有問卷_________________________________________________________-->
		<li style="left: <? if ($indexQ!=0) echo '0' ; else echo '-1000px'; ?> ;" >

				<?php 
				   $i=0;

				//$sql2="select * from questionnaire where datetime<$now AND DUE>$now  AND done=4 AND NOT ID=1030001 order by datetime , id asc ";
				$sql2=  "select * from questionnaire where datetime<$now AND done=4  AND NOT ID=1030001 order by datetime desc, id asc ";
	
				 $stmt2=ociparse($con,$sql2); 
				ociexecute($stmt2,OCI_DEFAULT); 
				$nrows2= OCIFetchStatement($stmt2,$results2); 
				$nrows1=0;
			

if ($nrows2==0)
					echo "<p class=\"bg-info\" ><br>　　　　目前未有開放中之問卷</br></br></p>";
				else{
				
	if ($indexQ==0||$indexQ==1)
							$num_pages = 1;
				else
							$num_pages = $indexQ;

	include("indexInclude.php"); 
					}
$page_num=ceil($nrows2/$pageRow_records);
if ($page_num>1)
{
	echo "<br><div class=\"btn-group\" role=\"group\" aria-label=\"First group\">";
	for ($i=1;$i<=$page_num;$i++)
	{	
        echo "<button type=\"button\" class=\"btn btn-default";
		if ($indexQ==$i)
			echo "active";
		echo "\" value='$i' onclick=\"Questionnaire(this.value)\">$i</button>";
	}       
     echo "</div>";	
}
?>
</li>


<!-------------------------------------------- END OF THE DIV=QUESTIONNAIRE-BLOCK ------------------------------->


			 	 


 
            </div>

        </div>

    </div>   </div>   </div>
    <!-- /.container -->

   <? include("footer.html");?>
    <!-- /.container -->

</body>

</html>
