<?php
ob_start();
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 
header("Pragma: no-cache"); 

session_start();// 啟動session,才可讀取session檔裏的變數
//尚未登入強迫回到登入主畫面 
if($_SESSION['Username']==NULL||$_SESSION['auth']!=1)
{                                          
  echo "<script language = JavaScript>";         
  echo "    top.location.href=\"login.php\";";     
  echo "</script>"; 
  exit;
}

?>

