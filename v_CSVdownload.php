<? session_start(); 
include("connect.php");
$dd=$_SESSION['DD'];

$sql="select * from VOTE where ID='$dd' ";
$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
 OCIFetchStatement($stmt,$results); 
	
$undertaker=$results['ACCOUNT'][0];
$tit=$results['TITLE'][0];
$type=$results['TYPE'][0];
$tit= iconv("BIG5","UTF-8",$tit);
$dd= iconv("UTF-8","BIG5",$dd);

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

$file="./download/vote/". $dd. ".csv";
$fp=fopen("$file","w");
fwrite($fp,chr(0xEF).chr(0xBB).chr(0xBF));


	$stuednt_array=explode("<br />",$tit);
	foreach($stuednt_array as $index => $value)
		{$tt=$tt.$value;
		//$tt= iconv("BIG5","UTF-8",$tt);
		}
		
fwrite($fp,$tt.",\r\n\r\n");


/////////////////////////部分
$sql="select * from ELECTION_INFO where ID='$dd'";
$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
$nrows=OCIFetchStatement($stmt,$results);

$sql1="select * from vote_chose where ID='$dd'";
$stmt1=ociparse($con,$sql1); 
ociexecute($stmt1,OCI_DEFAULT);
$nrows1=OCIFetchStatement($stmt1,$results1);

 fwrite($fp,"總投票人數 : ,".$nrows ."人, ,總票數 : , $nrows1 人 \r\n\r\n" );


if ($type==1)
	$sql="select * from CANDIDATE where ID='$dd'  ORDER BY NO ASC";
else
	$sql="select * from MASTERPIECE  where ID='$dd'  ORDER BY NO ASC";
$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
$nrows=OCIFetchStatement($stmt,$results);

if ($type==1)
for ($i=0;$i<$nrows;$i++)
{
	$no=$results['NO'][$i];
	$name=iconv("BIG5","UTF-8",$results['NAME'][$i]);
	$department=iconv("BIG5","UTF-8",$results['DEPARTMENT'][$i]);
	
	$sql1="select COUNT(*) from VOTE_CHOSE where ID='$dd' and CHOOSE=$no ";

	$stmt1=ociparse($con,$sql1); 
	ociexecute($stmt1,OCI_DEFAULT);
	OCIFetchStatement($stmt1,$results1);
	$count=$results1['COUNT(*)'][0];
	fwrite($fp,"$no . , $name , ($department ) , ,$count 票 \r\n" );
}

else
for ($i=0;$i<$nrows;$i++)
	{
	$no=$results['NO'][$i];
	
	$name=iconv("BIG5","UTF-8",$results['NAME'][$i]);
	$department=iconv("BIG5","UTF-8",$results['DEPARTMENT'][$i]);
	$author=$results['AUTHOR'][$i];
	$department=$results['DEPARTMENT'][$i];
	$slogan=$results['FILE_NAME'][$i];

	$sql1="select COUNT(*) from VOTE_CHOSE where ID='$dd' and CHOOSE=$no ";

	$stmt1=ociparse($con,$sql1); 
	ociexecute($stmt1,OCI_DEFAULT);
	OCIFetchStatement($stmt1,$results1);

	$count=$results1['COUNT(*)'][0];

	if ($type==2)
	fwrite($fp,"$no . , $name ,$slogan\r\n,, $author($department ) ,$count 票 \r\n" );
	else
	fwrite($fp,"$no . , $name  ,$author($department ) ,$count 票 \r\n" );
}

fclose($fp);
$attch_tmp="data.csv";
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