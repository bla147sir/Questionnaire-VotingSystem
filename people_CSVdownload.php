<? session_start(); 
include("connect.php");
$ID=$_SESSION['NUMBER'];
$QV=$_SESSION['QV'];

if ($QV=='V')
$sql="select * from VOTE where ID='$ID' ";
else 
$sql="select * from QUESTIONNAIRE where ID='$ID' ";

$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
OCIFetchStatement($stmt,$results); 
	
$undertaker=$results['ACCOUNT'][0];
$tit=$results['TITLE'][0];
$type=$results['TYPE'][0];
$tit= iconv("BIG5","UTF-8",$tit);
$ID= iconv("UTF-8","BIG5",$ID);

$user=$_SESSION['Username'];


$adm="select * from member where identity='$user'";
$admstmt=ociparse($con,$adm); 
ociexecute($admstmt,OCI_DEFAULT);
$admR= OCIFetchStatement($admstmt,$admresults); 

if ($admR==1&&$admresults['STATUS'][0]==1)
  $admin=1;
else
	$admin=0;

if($user==$undertaker||$admin==1)
{; 
	
}
else
{echo "<script language = JavaScript>";
	echo "alert(\"You can't download it \");"; 
	echo "window.location.href='index.php'";
	echo "</script>"; 
	exit(0);
}


$user_login=admin;

//***********************************
if ($QV=='V')
	$file="./download/vote/people/".$ID.".csv";
else 
	$file="./download/questionnaire/people/".$ID.".csv";

$fp=fopen("$file","w");
fwrite($fp,chr(0xEF).chr(0xBB).chr(0xBF));

	$stuednt_array=explode("<br />",$tit);
	foreach($stuednt_array as $index => $value)
		{$title=$title.$value;}

fwrite($fp,$title.",\r\n\r\n");

fwrite($fp,",填寫人帳號,填寫人時間,\r\n");
if ($QV=='V')
	$sql="select * from ELECTION_INFO where ID='$ID'";
else 
	$sql="select * from IDENTITY where ID='$ID'";

$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
$nrows=OCIFetchStatement($stmt,$results);

for ($i=0;$i<$nrows;$i++)
{$j=$i+1;

$time = $results['TIME'][$i] ;
$t[$i]=$time;

$y=(int)($t[$i]/10000000000);
$m=(int)(($t[$i]-($y*10000000000))/100000000);
$d=(int)(($t[$i]-($y*10000000000)-($m*100000000))/1000000);
$h=(int)(($t[$i]-($y*10000000000)-($m*100000000)-($d*1000000))/10000);
$min=(int)(($t[$i]-($y*10000000000)-($m*100000000)-($d*1000000)-($h*10000))/100);
$s=(int)(($t[$i]-($y*10000000000)-($m*100000000)-($d*1000000)-($h*10000)-($min*100))/1);
					


	fwrite($fp,"$j,".$results['IDENTITY'][$i].","."$y"."/"."$m"."/"."$d"." $h".":"."$min".":"."$s".",\r\n");

}




fclose($fp);
$attch_tmp="participant.csv";
$file_path =$file ;//檔案來源：wbe server的絕對路徑
$file_size = filesize($file_path);

header('Pragma: public');
header('Expires: 0');
header('Last-Modified: ' . gmdate('D,d M Y H:i ') . ' GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header("Content-type: application/download");   
header('Content-Length: ' . $file_size);
header("Content-type:application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="' . $attch_tmp . '";'); //要output的檔名(可自訂)
header('Content-Transfer-Encoding: binary');
readfile($file_path);

?>