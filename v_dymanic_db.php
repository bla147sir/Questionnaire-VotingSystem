<?

session_start(); 
$value=$_GET['value'];
$id=$_GET['ID'];
$no=$_GET['no'];
$row=$_GET['row'];
$detail=$_GET['detail'];

//echo $value;
//echo $id;

include("connect.php");

//******************************************************select type, done from vote 
$select="select * from vote where ID='$id' ";
$stmt=ociparse($con,$select);     
ociexecute($stmt,OCI_DEFAULT); 
$t= OCIFetchStatement($stmt,$result);
$done=$result['DONE'][0];

$str=explode(',',$value);

$num=$str[0]; // Á`¦@´Xµ§
$j=1;

for($i=$no;$i<=$num;$i++)
{

$auth=$str[$j];
$depart=$str[$j+1];
$name=$str[$j+2];
$file=$str[$j+3];
$des=$str[$j+4];

$name=iconv("UTF-8","BIG5", $name);
$des=iconv("UTF-8","BIG5", $des);
$auth=iconv("UTF-8","BIG5", $auth);
$depart=iconv("UTF-8","BIG5", $depart);
$file=iconv("UTF-8","BIG5", $file);

$sql2="insert into MASTERPIECE (ID, NO, NAME, DESCRIPTION, AUTHOR, DEPARTMENT, FILE_NAME) values('$id','$i','$name','$des','$auth','$depart','$file' )";
//echo $sql2."<br>";

$j=$j+5;



	    $stmt2 = OCIPARSE($con,$sql2);

		if(!OCIEXECUTE($stmt2,OCI_DEFAULT)) 
		{
		ocirollback($con);            
		exit();
		}
		else ocicommit($con);
		
		if($done==1) {
			$update_done = "update vote set DONE=2, ROW_NUM='$row', DETAIL='$detail' where id='$id' ";
		
			$stmt = OCIPARSE($con,$update_done );

			if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
			{
				ocirollback($con);            
				exit();
			}
			else ocicommit($con);

		} // done=1
	$url2 = "v_design3.php?ID=$id"; 
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url2'";
	echo "</script>";


}
?>