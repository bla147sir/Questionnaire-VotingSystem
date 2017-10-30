<?
 session_start();
 session_destroy();
 
 $HTTP_HOST=$_SERVER['HTTP_HOST'];  //  ex. aps.ncue.edu.tw
 if ($HTTP_HOST == "aps.ncue.edu.tw")
   echo "<script> top.location.href='index.php'; </script>";
 else 
   echo "<script> window.opener=null;window.open('','_top','');window.close(); </script>"; 

?>