				<div class="list-group">
                    <a href="index.php" class="list-group-item active">首頁</a>
                   <? 
				   if($_SESSION['Username']==null && $_SESSION['auth']!=1)
                  echo  "<a href=\"login.php\" class=\"list-group-item\">登入</a>";
				 
		
				$k=$_SESSION['Username'];

				$sql9="select * from member where IDENTITY='$k'";       
 
				 $stmt9=ociparse($con,$sql9); 
				 ociexecute($stmt9,OCI_DEFAULT); 
	  
				$nrows9 = OCIFetchStatement($stmt9,$results9);
				if($nrows9 ==1)
				{	
				  echo "<a href=\"design.php\" class=\"list-group-item\">新增問卷</a>";
					
                  echo  "<a href=\"statisticAll.php\" class=\"list-group-item\">我的問卷</a>";
				}

				$identity = $results9['IDENTITY'][0] ;    //帳號

				$status = $results9['STATUS'][0] ;   //身分
 
				if($status==1)
				{	
					  echo  "<a href=\"create.php\" class=\"list-group-item \">問卷建立者名單</a>";
				}
                ?>

                </div> 