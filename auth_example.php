<?php 
	
	function validPassword($user_id, $user_pwd)
	{  
		$user_ip = ($_SERVER[HTTP_X_FORWARDED_FOR]?$_SERVER[HTTP_X_FORWARDED_FOR]:$_SERVER["REMOTE_ADDR"]);
		$flag = false;
        $timeout = 2; //測試連線時間
    	$EcpaHostIp = "aps.ncue.edu.tw";	//◎驗證的URL
		$request="user_id=".$user_id."&user_pwd=".$user_pwd."&user_ip=".$user_ip."";
		// Open connection
		$fp = fsockopen($EcpaHostIp, 80,$errno,$errstr,$timeout);

		// Send the request
		if($fp)
        {
			fputs($fp, "POST /sso/auth_dlearn.php HTTP/1.1\r\n");
			fputs($fp, "Host: ".$EcpaHostIp." \r\n");
			fputs($fp, "Content-Length: " . strlen($request) . "\r\n");
			fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n\r\n");
			fputs($fp, "$request\r\n");
			fputs($fp, "\r\n");
	
			$result  = fread($fp,1024);
			fclose($fp);			
		    $res1 = trim(substr($result,strpos($result,"\r\n\r\n")+4));		
			return $res1;		    
		}   
		
    	return 0;
	}
?>
