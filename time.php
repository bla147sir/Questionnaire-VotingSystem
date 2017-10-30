				<?	


					$id = $results1['ID'][$i] ;
					$title = $results1['TITLE'][$i] ;
					$title= iconv("BIG5","UTF-8",$title);
					$des = $results1['DESCRIPTION'][$i] ;
					$type= $results1['TYPE'][$i] ;

				$total_records=$nrows1;
				$dd=$results1['DATETIME'][$i]/1000000;
					$DA= sprintf("%d",$dd);
					$dddd=($results1['DATETIME'][$i]-(int)($results1['DATETIME'][$i]/100000000)*100000000)%100000000;

					$DAT= substr(sprintf("%d",$dddd),-6,6);
                   
					$dateY  = substr($DA,0,4);
					$dateM = substr($DA,4,2);
					$dateD =substr($DA,6,2);
					$dateH=substr($DAT,-6,2);

					$dateMin=substr($DAT,-4,2);
					$dateS=substr($DAT,-2,2);
					
					$Ymd= (int)date("Ymd");
					$His=(int)date("dHis");
			

					$dudu=$results1['DUE'][$i]/1000000;
					$Du= sprintf("%d",$dudu);
					$dduu=($results1['DUE'][$i]-(int)($results1['DUE'][$i]/100000000)*100000000)%100000000;
					$DuT= sprintf("%d",$dduu);
                   
					$dueY  = substr($Du,0,4);
					$dueM = substr($Du,4,2);
					$dueD =substr($Du,6,2);
					$dueH=substr($DuT,-6,2);

					$dueMin=substr($DuT,-4,2);
					$dueS=substr($DuT,-2,2);
					

					
					
					
					$date = $dateY.'/'.$dateM.'/'.$dateD.'　'.$dateH.':'.$dateMin.":".$dateS;
					$due= $dueY.'/'.$dueM.'/'.$dueD.'　'.$dueH.':'.$dueMin.":".$dueS;
					$department = $results1['DEPARTMENT'][$i];
					$undertaker = $results1['UNDERTAKER'][$i];
					$department= iconv("BIG5","UTF-8",$department);
					$undertaker= iconv("BIG5","UTF-8",$undertaker);
					$tt=NULL;
			$stuednt_array=explode("<br />",$title);
			foreach($stuednt_array as $index => $value)
				{$tt=$tt.$value;}
					
					$ii=$ii+1;

					echo "<tr  align=\"center\" height=\"30\"  ";
					if ($i%2==1)
					{echo "bgcolor=\"EEEEEE\"";}
					echo ">";
					echo "<td>";
					 if ($QV==1)        ///////投票系統//////////
					{ 
						if ($home==1)
							 echo "<a href=\"v_home.php?ID=$id\">";
						else 
						{
							if ($type==1)       ///人員票選
							echo "<a href=\"v_member.php?ID=$id\">";
						else if ($type==2)  ///如果是標語票選
							echo "<a href=\"v_work.php?ID=$id\">";
						else if ($type==3)  ///如果是上傳文字票選
							echo "<a href=\"v_work.php?ID=$id\">";
						else if ($type==4)  ///上傳圖片票選
							echo "<a href=\"v_work.php?ID=$id\">";
						else if ($type==5)  ///上傳影片票選
							echo "<a href=\"v_work.php?ID=$id\">";
						}
					} 
					else if ($QV==2)  //如果是問卷系統
					{	if ($home==1)
							 echo "<a href=\"q_home.php?ID=$id\">";
						else 
							echo "<a href=\"q_fillin.php?ID=$id\">";
					}
					
					echo "$tt</a></td>";


					?>