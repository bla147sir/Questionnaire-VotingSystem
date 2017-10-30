<? session_start(); 
include("connect.php");
$dd=$_SESSION['MOMO'];

$sql="select * from questionnaire where ID='$dd'";
$stmt=ociparse($con,$sql); 
ociexecute($stmt,OCI_DEFAULT);
 OCIFetchStatement($stmt,$results); 

  $undertaker=$results['ACCOUNT'][0];
  $tit=$results['TITLE'][0] ;
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
{

$user_login=admin;

//***********************************

$file="./download/questionnaire/". $dd. ".csv";
$fp=fopen("$file","w");
fwrite($fp,chr(0xEF).chr(0xBB).chr(0xBF));



	 			$stuednt_array=explode("<br />",$tit);

			foreach($stuednt_array as $index => $value)
				{$tt=$tt.$value;
				//$tt= iconv("BIG5","UTF-8",$tt);
				}
				
//fwrite($fp,$sql.",");
fwrite($fp,$tt.",");
fwrite($fp,",\r\n\r\n");

/////////////////////////部分
$ssql="select DISTINCT PART from question where ID='$dd' ";   
    $sstmt=ociparse($con,$ssql); 

	 ociexecute($sstmt,OCI_DEFAULT);

	 $nrowss = OCIFetchStatement($sstmt,$resultss);
	  $ar = array("零","一", "二", "三", "五", "六", "七", "八", "九", "十") ;
	
	 for($kk=0; $kk<$nrowss ;$kk++)
	{$k=$kk+1;
	   if ($nrowss >1)
		fwrite($fp,"\r\n第".$ar[$k]."部分"."\r\n");

	$sql2="select * from question where ID='$dd' AND PART='$k' ORDER BY NO ASC ,NON ASC";        
 
	 $stmt2=ociparse($con,$sql2); 
	 ociexecute($stmt2,OCI_DEFAULT);

	 $nrows2 = OCIFetchStatement($stmt2,$results2); 
	 for($i=0; $i<$nrows2 ;$i++) 
	 {  
	 $Q = $results2['QUESTION'][$i] ;
	 $Q= iconv("BIG5","UTF-8",$Q);

	 $no= $results2['NO'][$i] ;
	 $non= $results2['NON'][$i] ;
	//********************************
	if ($results2['NON'][$i]==0)
	fwrite($fp,sprintf("%d",$no).".".$Q."\r\n");

	else if ($results2['TYPE'][$i]==6||$results2['TYPE'][$i]==3||$results2['TYPE'][$i]==4||$results2['TYPE'][$i]==5)
	 {
	if ($results2['NO'][$i] ==$results2['NO'][$i-1] ||$results2['NO'][$i] ==$results2['NO'][$i+1])
	{
	 fwrite($fp,sprintf("%d",$no).".".sprintf("%d",$non).",".$Q."\r\n");
	}
	 else
	{
		fwrite($fp,sprintf("%d",$no).",".$Q."\r\n");
	 }
	      
			///////////////////////////選項
		$sql3="select * from OPT where ID='$dd' AND PART='$k' AND NO='$no'  ORDER BY NUM ASC"; 
		 $stmt3=ociparse($con,$sql3); 
		 ociexecute($stmt3,OCI_DEFAULT);
		 $nrows3 = OCIFetchStatement($stmt3,$results3);
		$max[$i]=0;
			///////table
		 for($j=0; $j<$nrows3 ;$j++) 
		 { $opt[$i][$j]=$results3['OPTION_VALUE'][$j] ;
		 	 $opt[$i][$j]= iconv("BIG5","UTF-8",$opt[$i][$j]);

		   $num= $results3['NUM'][$j] ;

		//////////////////////////計數器
		$sql4="select COUNT(*) from ANS_OPT where ID='$dd' AND PART='$k' AND NO='$no' AND NON='$non' AND ANSWER=$num" ;
		 $stmt4=ociparse($con,$sql4); 
		 ociexecute($stmt4,OCI_DEFAULT);
		 $nrows4 = OCIFetchStatement($stmt4,$results4);
         $cou[$i][$j]=$results4['COUNT(*)'][0] ;
		 
		 if ($max[$i]<$cou[$i][$j])
			 $max[$i]=$cou[$i][$j];
		$m=m.$i;
		$n=n.$i;
		$maxi=maxi.$i;
		 } 
		  
			 $_SESSION["$m"]=$opt[$i];
			 $_SESSION["$n"]=$cou[$i];
			 $_SESSION["$maxi"]=$max[$i];
			
			$wid=$nrows3*30+50;
		
 $sql6="select distinct IDENTITY  from ANS_OPT where ID='$dd' ";  //人數統計     
		$stmt6=ociparse($con,$sql6); 
		 ociexecute($stmt6,OCI_DEFAULT);
		 $nrows6 = OCIFetchStatement($stmt6,$results6); 
		  fwrite($fp," ,"." ,"."\r\n");

		for($j=0; $j<$nrows3 ;$j++) 
		{ fwrite($fp," ,".$opt[$i][$j].",".sprintf("%d",$cou[$i][$j])."\r\n");
		  $sum[$i]=$sum[$i]+$cou[$i][$j];}
		
      fwrite($fp,"\r\n , 總票數 ,".sprintf("%d",$sum[$i]).",\r\n");

		  fwrite($fp," , 總人數 ,". sprintf("%d",$nrows6).",\r\n\r\n");
	 }

	 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	else
	 {	
	   fwrite($fp,$no.",".$Q."\r\n");
		
		 $sql5="select * from TEXT where ID='$dd' AND PART='$k' AND NO=$no AND NON=$non";       
		$stmt5=ociparse($con,$sql5); 
		ociexecute($stmt5,OCI_DEFAULT);
		$nrows5 = OCIFetchStatement($stmt5,$results5); 
		for($p=0; $p<$nrows5 ;$p++) 
		{   $P= $p+1;
			$answer=$results5['ANSWER'][$p];
			$answer= iconv("BIG5","UTF-8",$answer);

			 fwrite($fp,",".$P.",".$answer."\r\n");
		}
		}


		}
	}


fclose($fp);
$attch_tmp="data.csv";
$file_path = "./" . $file ;//檔案來源：wbe server的絕對路徑
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

}
else
{header("Location:index.php");}
?>