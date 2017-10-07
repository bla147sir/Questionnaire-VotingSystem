<?php 

include("connect.php");

	//*******************choose 直接匯入 (only type1~2 and 6 operate here)
	if($_POST['decide']==1 || $type==6 || $_SESSION["done"]==0){
			
			//*****************row
			$row=$_POST['row'];
			//*****************order_by
			$order_by=$_POST['order_by'];
			//*****************detail
			for($i=1;$i<=2;$i++)
			{
				if($_POST['detail'][$i])	$d_value[$i]=1;
				else $d_value[$i]=0;
			}
			//$detail   作者/單位, 如：01 不顯示作者 顯示單位 
			for($i=1;$i<=2;$i++)$detail .= "$d_value[$i]";

			for($i=1;$i<$_SESSION['count'];$i++){
				//********************
				if($done==2) $no=$no+1;
				//*******************elector
					if($_SESSION["done"]==0){
						
						$identity[$i]=$_SESSION['data'][$i][1];
						$e_name[$i]=$_SESSION['data'][$i][2];    // allow null
						$dept[$i]=$_SESSION['data'][$i][3];		 // allow null
						$p_no[$i]=$_SESSION['data'][$i][4];      // allow null
						
						$import_elector= "insert into elector values('$id','$e_name[$i]','$dept[$i]','$p_no[$i]','$identity[$i]')";
				
						//echo "a:".$import_elector."<br>";
						$stmtach = OCIPARSE($con,$import_elector);
						
					}

				//*******************type=1 (candidate)
					if($type==1 ){
				
						$c_name[$i]=$_SESSION['data'][$i][1];
						$dept[$i]=$_SESSION['data'][$i][2];
						$p_no[$i]=$_SESSION['data'][$i][3];
						$email[$i]=$_SESSION['data'][$i][4];       // allow null
						$description[$i]=$_SESSION['data'][$i][5]; // allow null
						
						if($done==1)
							$import_candidate= "insert into candidate values('$id',$i,'$c_name[$i]','$dept[$i]','$p_no[$i]','$email[$i]','$description[$i]')";
						else if($done==2)
							$import_candidate= "insert into candidate values('$id',$no,'$c_name[$i]','$dept[$i]','$p_no[$i]','$email[$i]','$description[$i]')";
						//echo $import_candidate."<br>";
						$stmtach = OCIPARSE($con,$import_candidate);
					}
				//*******************type=2 or 6 (masterpiece)
					if($type==2 || $type==6){
						
						if($type==2){
							$file_name[$i]=$_SESSION['data'][$i][1];
							$author[$i]=$_SESSION['data'][$i][2];      // allow null
							$dept[$i]=$_SESSION['data'][$i][3];        // allow null
							$data_name[$i]=$_SESSION['data'][$i][4];   // allow null
							$description[$i]=$_SESSION['data'][$i][5]; // allow null
						}
						else {
							$f_extension=$_POST['f_extension'];
							$f_id=$id*1000;

							if($done==1) $file_name[$i]=$f_id+$i.".".$f_extension;
							if($done==2) $file_name[$i]=$f_id+$no.".".$f_extension;
							 
							// echo  $file_name[$i]."<br>";

							$author[$i]=$_SESSION['data'][$i][1];      // allow null
							$dept[$i]=$_SESSION['data'][$i][2];        // allow null
							$data_name[$i]=$_SESSION['data'][$i][3];   // allow null
							$description[$i]=$_SESSION['data'][$i][4]; // allow null

						} 
						//**************************************************
						if($done==1)
							$import_slogan= "insert into masterpiece (ID, NO, NAME, DESCRIPTION, AUTHOR, DEPARTMENT, FILE_NAME)values('$id',$i,'$data_name[$i]','$description[$i]','$author[$i]','$dept[$i]','$file_name[$i]')";
						else if($done==2)
							$import_slogan= "insert into masterpiece (ID, NO, NAME, DESCRIPTION, AUTHOR, DEPARTMENT, FILE_NAME)values('$id',$no,'$data_name[$i]','$description[$i]','$author[$i]','$dept[$i]','$file_name[$i]')";
						$stmtach = OCIPARSE($con,$import_slogan);
						//echo $import_slogan."<br>";
					} 
				
					if(!OCIEXECUTE($stmtach,OCI_DEFAULT)) 
					{
						ocirollback($con);            
						exit();
					}
					else ocicommit($con);
				
			} // for

		if($type==6){
			if($f_extension=='doc' || $f_extension=='pdf' || $f_extension=='txt' )
				$update_type = "update vote set TYPE=3 where id='$id'  ";

			if($f_extension=='jpeg')
				$update_type = "update vote set TYPE=4 where id='$id'  ";

			if($f_extension=='mp4')
				$update_type = "update vote set TYPE=5 where id='$id'  ";

			$stmtach = OCIPARSE($con,$update_type);
			if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
			{	
				ocirollback($con);            
				exit();
			}
			else ocicommit($con);
		} //if type=6
			
	} // decide=1

	//*******************手動輸入 (only type3~5 operate here)
   	if($type==3 || $type==4 ||$type==5 ){
	
		//***************第一次新增(update detail, row, if_hyperlink)
		if($done==1){
			//*******************detail : (是否需要顯示作者單位, 創作理念) 
			for($i=1;$i<=2;$i++)
			{
				if($_POST['detail'][$i]){
					$c_value[$i]=$_POST['detail'][$i];
					$c_value[$i]=1;
				}
				else $c_value[$i]=0;
			}
			for($i=1;$i<=2;$i++) $detail .= $c_value[$i];

			$row=$_POST['row'];

			//*******************有超連結
			if($_POST['address'][0]) 
				$update_vot = "update vote set  IF_HYPERLINK=1 where id='$id'  ";
			
			$stmtach = OCIPARSE($con,$update_vot);
			if(!OCIEXECUTE($stmtach,OCI_DEFAULT))
			{	
				ocirollback($con);            
				exit();
			}
			else ocicommit($con);
		} //done=1

		//************************************取上傳檔案的資料
		for($i=1;$i<=$_POST['quantity'];$i++){
			//********************
			if($done==2) $no=$no+1;
			//********************
				$name[$i]=$_POST['name'][$i-1];
				$description[$i]=$_POST['description'][$i-1];
				$author[$i]=$_POST['author'][$i-1];
				$auth_dpt[$i]=$_POST['auth_dpt'][$i-1];

			//*******************************************upload file
			if ($_FILES["file"]["error"][$i-1] > 0){
				echo "Error: " . $_FILES["file"]["error"][$i-1];}
			else{
				$f_id=$id*1000;
				if($done==1) $file_name[$i]=$f_id+$i;
				if($done==2) $file_name[$i]=$f_id+$no;

				$file[$i]=$_FILES["file"]["name"][$i-1];
				$file_extension[$i]  = substr ($file[$i], -3);

				$fname[$i]=$file_name[$i].".".$file_extension[$i];    //改過的黨名跟副檔名合併

				move_uploaded_file($_FILES["file"]["tmp_name"][$i-1],"images/".$fname[$i]);}
			

			//*******************第一次 insert into masterpiece 
			if($done==1){
				if($_POST['address'][$i-1]) {
					$address[$i]=$_POST['address'][$i-1];
					$insert_masterpiece= "insert into masterpiece values('$id',$i,'$name[$i]','$description[$i]','$author[$i]','$auth_dpt[$i]','$fname[$i]','$address[$i]')";
				}
				else{
					$insert_masterpiece= "insert into masterpiece  (ID, NO, NAME, DESCRIPTION, AUTHOR, DEPARTMENT, FILE_NAME) values('$id',$i,'$name[$i]','$description[$i]','$author[$i]','$auth_dpt[$i]','$fname[$i]')";
				}
				$insert_masterpiece =iconv("UTF-8","BIG5//IGNORE", $insert_masterpiece);}
			//*******************第二次 insert into masterpiece 
			else if($done==2){
				if($_POST['address'][$i-1]) {
					$address[$i]=$_POST['address'][$i-1];
					$insert_masterpiece= "insert into masterpiece values('$id',$no,'$name[$i]','$description[$i]','$author[$i]','$auth_dpt[$i]','$fname[$i]','$address[$i]')";
				}
				else{
					$insert_masterpiece= "insert into masterpiece  (ID, NO, NAME, DESCRIPTION, AUTHOR, DEPARTMENT, FILE_NAME) values('$id',$no,'$name[$i]','$description[$i]','$author[$i]','$auth_dpt[$i]','$fname[$i]')";
				}
				$insert_masterpiece =iconv("UTF-8","BIG5//IGNORE", $insert_masterpiece);}
			
			//echo "a:".$insert_masterpiece."<br>";
			$stmt = OCIPARSE($con,$insert_masterpiece );
			if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
			{
				ocirollback($con);            
				exit();
			}
			else ocicommit($con);
		} //for
	
   } // decide = 2
//******************************************************update  questionnaire done=2
if ($done==1  ) {
	if($type==1)
		$update_done = "update vote set DONE=2 , ROW_NUM=$row, ORDER_BY=$order_by  where id='$id' ";
	else 
		$update_done = "update vote set DONE=2 , ROW_NUM=$row, DETAIL='$detail'  where id='$id' ";

}
	//echo 	"a:".$update_done;
$stmt = OCIPARSE($con,$update_done );

if(!OCIEXECUTE($stmt,OCI_DEFAULT)) 
{
	ocirollback($con);            
	exit();
}
else ocicommit($con);

?>