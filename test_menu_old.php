
<?php session_start(); 

include("connect.php");
$url = "login.php";

//********************************************************
//左邊選單
//管理者(status=1)可新增問眷及人員名單 , 一般承辦人可查看問卷
//程式清單(未來可修改為直接存取DB)
  $php_self=$_SERVER['PHP_SELF'];//get current url
	$url=substr($php_self,strrpos($php_self,"/")+1);
	
	//程式清單(未來可改為存取DB)
	$arr_program=array(0=>"index.php",1=>"login.php",2=>"q_home.php",3=>"q_fillin.php",4=>"q_design.php",5=>"q_design2.php",6=>"q_design3.php",7=>"q_design4.php",8=>"q_design5.php",9=>"questionnaireAll.php",10=>"create.php",11=>"voteAll.php",12=>"v_home.php",13=>"v_work.php",14=>"v_member.php",15=>"v_design.php",16=>"v_design2.php",17=>"_design3.php");
	
	$arr_active = array_fill(1,30,"");
	$p_id = array_search($url,$arr_program);
	
	$arr_active[$p_id]="active";

/******************MENU*******************style=\"  background-color: #000000; \"**/
echo "<nav class=\"navbar navbar-inverse\">";
echo "<div class='container-fluid'>";
echo "<ul class=\"nav navbar-nav navbar-left\">";
//echo "<button class='btn btn-black navbar-btn navbar-left $arr_active[0] ' 　 onclick=\" window.location.href='index.php'  \"　>　</button>";
				
echo "<li class='$arr_active[0] '><a href='index.php'><span  class='glyphicon glyphicon-home'></span > 首頁</a></li>";
	
	$account=$_SESSION['Username'];
	$sql9="select * from member where IDENTITY='$account'";
	$stmt9=ociparse($con,$sql9); 
	ociexecute($stmt9,OCI_DEFAULT); 	  
	$nrows9 = OCIFetchStatement($stmt9,$results9);
	if($nrows9>=1)
	{	
		$identity = $results9['IDENTITY'][0] ;    //帳號
		$status = $results9['STATUS'][0] ;   //身分		

           if($status==1||$status==2)//管理者
		{	
   echo "<li class='dropdown  $arr_active[11]'>";
			echo "<a class=\"dropdown-toggle\" data-toggle=\"dropdown\" role='button' href='voteAll.php' > <span class='glyphicon glyphicon-pencil'></span> 我的票選 ";
		echo"<span class=\"caret\"></span></a>";
			echo " <ul class='dropdown-menu' role='menu'>
				   <li class='black'><a href=\"voteAll.php\" >我的票選</a></li>
				   <li class='black'><a href=\"v_design.php\" >新增票選</a></li>
				 </ul>  
			  </li>
				";
//</div>
        /********************************************************************************/
   echo "<li class='dropdown  $arr_active[9]'>";
			echo "<a class=\"dropdown-toggle\" data-toggle=\"dropdown\" role='button' href='questionnaireAll.php' > <span class='glyphicon glyphicon-file'></span> 我的問卷 ";
		echo"<span class=\"caret\"></span></a>";
			echo " <ul class='dropdown-menu' role='menu'>";
			echo " <li class='black' > <a href=\"questionnaireAll.php\" >我的問卷</a> </li> ";
			echo " <li class='black' > <a href=\"q_design.php\" > 新增問卷 </a> </li>";
			echo "  </ul> </li>
			     ";




		}        

 if($status==1)//管理者
		{//echo  "<button class='btn btn-black navbar-btn navbar-left'  onclick=\"window.location.href='create.php'\"  > 
		echo "<li  class=' $arr_active[10]'><a href='create.php'><span class='glyphicon glyphicon-user'></span> 建立者名單</a></li>";}
	}
	if($p_id==4||$p_id==5||$p_id==6||$p_id==7||$p_id==8)
		echo "<li class='$arr_active[4] $arr_active[5] $arr_active[6] $arr_active[7] $arr_active[8] ' style=\"color:white;  \"><a>　新增問卷　</a></li>";

	if($p_id==15||$p_id==16||$p_id==17)
		echo "<li class='$arr_active[15] $arr_active[16] $arr_active[17]' style=\"color:white;  \"><a>　新增票選　</a></li>";

	if($p_id==2||$p_id==12)
		echo "<li class='$arr_active[2] $arr_active[12]'> <a>活動說明 </a></li>";
         // echo  "<button  >　</button>";

	if($p_id==3)
	echo "<li class='$arr_active[3]'><a> 填問卷中 </a></li>";
  //       echo  "<button  >　</button>";


	if($p_id==13||$p_id==14)
	echo "<li class='$arr_active[13] $arr_active[14]' ><a> 投票中 </a></li>";


	echo "</ul> "; ///////////////class=\"nav navbar-nav\"

echo "<ul class=\"nav navbar-nav navbar-right\">";

	if($_SESSION['Username']==null)
	echo " <li class='$arr_active[1]'><a href='login.php'><span  class='glyphicon glyphicon-log-in'></span >　登入</a></li>";

	if($_SESSION['Username'] != null&&$_SESSION['auth']==1)
	{
		echo "<p class=\"navbar-text \"  style=\"color:white;  \"  > ". $_SESSION['NAME'] . "，您好!</p>";
		echo "<li><a href='logout.php'>　<span class='glyphicon glyphicon-log-out'></span> 登出　</a></li>";
	
	} 
echo "</ul>"; ///////////////class=\"nav navbar-nav\"
echo "</div>";
	echo "</nav>";
?>	
  



       





