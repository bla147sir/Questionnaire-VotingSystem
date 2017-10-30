<?php 
session_start(); 

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
<?
$user = trim($_POST['p_usr']) ; 
$pwd = trim($_POST['p_pwd']) ;

if(strlen($user)>1)
{
	
	include("auth_example.php");
	$result = validPassword($user,$pwd);
	$result_array=explode(",",$result);
	if(strpos($result,'success'))//認證成功
	{
		 if ( ereg("^[smdaSMDA]{1}[0-9]{7,9}",$user) )//學生
		 {
			  if ( (substr($user,1,2)=="99" || substr($user,1,2)<="20" ) && strlen($user)==8) 
				  $user=strtoupper($user);
			  else   
				  $user=substr($user,1);
		 }

		$_SESSION['auth']=1;  
		$_SESSION['Username']=$user;
		$_SESSION['NAME']=$result_array[2];
		$_SESSION['department']=$result_array[3];
		$_SESSION['identity']=$result_array[4];
		
		$_SESSION['NAME']=iconv("big5","UTF-8",$_SESSION['NAME']); 
		$_SESSION['department']=iconv("big5","UTF-8",$_SESSION['department']);
		$_SESSION['identity']=iconv("big5","UTF-8",$_SESSION['identity']); 
		$_SESSION['user_type']=0;
		if ($_SESSION['identity']=="教師") $_SESSION['user_type']=1;
		if ($_SESSION['identity']=="職員") $_SESSION['user_type']=2;
		if ($_SESSION['identity']=="學生") $_SESSION['user_type']=3;		

		echo "<script type='text/javascript'>";
		echo "window.location.href='index.php'";
		echo "</script>";
		exit;
	}
	else
	{
		$_SESSION['Username']="";
		$_SESSION['Password']="";		
		echo "<script language = JavaScript>";
		echo "alert(\"帳號密碼錯誤，請重新輸入！\");"; 
		echo "top.location.href='login.php';";
		echo "</script>";
		exit;
	}
}
include("connect.php");
?>


    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <!-- Custom CSS -->    
	<link href="css/ncue.css" rel="stylesheet">

	 <!-- jQuery -->
   
	 <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrapValidator.js"></script>
<script type="text/javascript" src="js/minwt.auto_full_height.mini.js"></script>
<link href="./css/menu.css" rel="stylesheet" >
    <script src="js/jquery.min.js" type="text/javascript"></script>


    <style type="text/css">
#a1{
	color: #990000;
	font-weight: bolder;
	text-align: center;
	font-family: Microsoft JhengHei;
	font-size: 16px;
}
</style>

</head>

<body onLoad="document.forms.login.p_usr.focus()">

  <div class="container container_ncue"  valign="bottom"  > 
		<div class="row">
            <div class="col-md-12" valign=\" bottom \">				
				<div id="banner">
				
			
				</div>	<?	include("test_menu.php");?>
			</div>
		</div>
<br>

        <div class="row" style=" height:500px; " align="center" >
            <div class="col-md-1" >
			<?	///include("vote_menu.php");?>
            </div> 
			
            <div class="col-md-11" align='center' >
        			<?
						$now=date("YmdHis");
						
					?>
                <div class="container">
                    <div style="margin-top:30px;" class="mainbox col-md-6 col-md-offset-1 col-sm-6 col-sm-offset-1">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <div class="panel-title">使用者登入</div>
                                <div style="float:right; font-size: 100%; position: relative; top:-10px"><a href="https://aps.ncue.edu.tw/sso/passwd_forget.php" target="_blank">忘記密碼?</a></div>
                            </div>
                            <div style="padding:30px 80px" class="panel-body">
                                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                                <form id="login" name="login" class="form-horizontal" role="form" method="POST" action="login.php" >
									<div class="form-group"> 
                                    <div class="input-group" style="line-height:0px;margin-bottom:5px">
                                        　<span class="input-group-addon">帳號：</span>　
                                        <input id="login-username" type="text" class="form-control" name="p_usr" value="" placeholder="">　
                                    </div>
									</div>
									<div class="form-group"> 
                                    <div  class="input-group" style="line-height:0px;margin-bottom:10px">
                                        　<span class="input-group-addon" id="basic-addon1">密碼：</span>
　
                                        <input id="login-password" type="password" class="form-control" name="p_pwd" placeholder="">　
                                    </div>
									</div>
                                    <div style="margin-top:10px" class="form-group">
                                        <!-- Button -->
                                        <div class="col-sm-12 controls"  style="padding:0px 120px" >
                                            <!--<a id="btn-login" href="#" class="btn btn-primary">  登 入  </a>-->
											<button class="btn btn-md btn-primary btn-block" type="submit">  登 入  </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><br/><br/><br/><br/>
<? //include("footer.html");?>
    </div>
    



 <script type="text/javascript">

$(document).ready(function() {	
    $('#login').bootstrapValidator({		
        message: '帳號密碼輸入錯誤!!',       
         feedbackIcons: {
            valid: 'glyphicon glyphicon',       
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },  
		fields: {
            p_usr: 
			{
                validators: {
                    notEmpty: {
                        message: '請輸入帳號!!'
                    }, 
                    stringLength: {                          
                        max: 16, 
                        message: '帳號輸入錯誤!!' 
                    }
                }
            },
			p_pwd: 
			{
                validators: {
                    notEmpty: {
                        message: '請輸入密碼!!'
                    }, 
                    stringLength: {                          
                        max: 20, 
                        message: '密碼輸入錯誤!!' 
                    }
                }
            }			
        }
    });
}); 
</script> 
   

</body>

</html>
