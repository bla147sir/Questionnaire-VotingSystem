			
						
						
						<? //$pageRow_records=5;
						$END=$num_pages*$pageRow_records;

						?>
						<table width="900px" valgin="botton" algin="center" >
						<tr align="center" bgcolor="#CEE0EC" >
							<td width="400" align="center" scope="row" height="30" >活動名稱</td>
							<td width="100" >開始日期</td> 
							<td width="100">結束日期</td>
							<td width="150">主辦單位</td>
							<td  width="80">承辦人</td>
						<td width="70">備註</td>
						</tr>
	<?php


			

		//預設頁數

		//若已經有翻頁，將頁數更新

		$total_records=$nrows1+$nrows2;
		//本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
		 $startRow_records =$num_pages*$pageRow_records;
$END=$num_pages*$pageRow_records;
		//計算總頁數=(總筆數/每頁筆數)後無條件進位。
		$total_pages = ceil($total_records/$pageRow_records);


	include("connect.php");
		$Q=0;
	$V=0;
			
	$i=$startRow_records;
	for ($i=0; $i<($nrows1+$nrows2) ;$i++)
				{
					$Vtime=sprintf($results1['DATETIME'][$V]);
					$Qtime=sprintf($results2['DATETIME'][$Q]);
					if ($V>=$nrows1)
					{
						$QV[$i][0]=2;
						 $QV[$i][1]="$Q";
						 $Q=$Q+1;
					}
					else if ($Q>=$nrows2||$Vtime<=$Qtime)
					{
					 $QV[$i][0]=1;
					 $QV[$i][1]="$V";
					 $V=$V+1;
					}
					else if ($Vtime>$Qtime)
					{
						$QV[$i][0]=2;
						$QV[$i][1]="$Q";
						$Q=$Q+1;
						}
					else if ($Vtime<=$Qtime)
					{
					 $QV[$i][0]=1;
					 $QV[$i][1]="$V";
					 $V=$V+1;
					}
					
				}

				


				for($i=$END-$pageRow_records; $i<$nrows1+$nrows2&&$i<$END;$i++) 
				{					
					if($QV[$i][0]==1)
					{
						
						$id = $results1['ID'][$QV[$i][1]] ;
						$title = $results1['TITLE'][$QV[$i][1]] ;
						$dd=$results1['DATETIME'][$QV[$i][1]]/1000000;
						$dddd=($results1['DATETIME'][$QV[$i][1]]-(int)($results1['DATETIME'][$QV[$i][1]]/100000000)*100000000)%100000000;
						$dudu=$results1['DUE'][$QV[$i][1]]/1000000;
						$dduu=($results1['DUE'][$QV[$i][1]]-(int)($results1['DUE'][$QV[$i][1]]/100000000)*100000000)%100000000;
						$department = $results1['DEPARTMENT'][$QV[$i][1]];
						$undertaker = $results1['UNDERTAKER'][$QV[$i][1]];
						$email = $results1['ACCOUNT'][$QV[$i][1]]."@cc.ncue.edu.tw"; 
						$tel = $results1['TEL'][$QV[$i][1]]; 
						$home=$results1['QHOME'][$QV[$i][1]];
						$analyze= $results1['ANALYZE'][$QV[$i][1]];
						$type=$results1['TYPE'][$QV[$i][1]];

					
						
					}
							 		
					else if($QV[$i][0]==2)
					{
						$id = $results2['ID'][$QV[$i][1]] ;
						$title = $results2['TITLE'][$QV[$i][1]] ;
						$dd=$results2['DATETIME'][$QV[$i][1]]/1000000;
						$dddd=($results2['DATETIME'][$QV[$i][1]]-(int)($results2['DATETIME'][$QV[$i][1]]/100000000)*100000000)%100000000;
						$dudu=$results2['DUE'][$QV[$i][1]]/1000000;
						$dduu=($results2['DUE'][$QV[$i][1]]-(int)($results2['DUE'][$QV[$i][1]]/100000000)*100000000)%100000000;
						$department = $results2['DEPARTMENT'][$QV[$i][1]];
						$undertaker = $results2['UNDERTAKER'][$QV[$i][1]];
						$email = $results2['ACCOUNT'][$QV[$i][1]]."@cc.ncue.edu.tw"; 
						$tel = $results2['TEL'][$QV[$i][1]]; 
						$home=$results2['QHOME'][$QV[$i][1]];
						$analyze= $results2['ANALYZE'][$QV[$i][1]];
						$type=$results2['TYPE'][$QV[$i][1]];
					}
						
					
					
					$title= iconv("BIG5","UTF-8",$title);
					
					$DA= sprintf("%d",$dd);
					$DAT= substr(sprintf("%d",$dddd),-6,6);
                   
					$dateY  = substr($DA,0,4);
					$dateM = substr($DA,4,2);
					$dateD =substr($DA,6,2);
					$dateH=substr($DAT,-6,2);

					$dateMin=substr($DAT,-4,2);
					$dateS=substr($DAT,-2,2);


					
					$Du= sprintf("%d",$dudu);
					$DuT= sprintf("%d",$dduu);
   
					$dueY  = substr($Du,0,4);
					$dueM = substr($Du,4,2);
					$dueD =substr($Du,6,2);
					$dueH=substr($DuT,-6,2);

					$dueMin=substr($DuT,-4,2);
					$dueS=substr($DuT,-2,2);

					$date = $dateY.'/'.$dateM.'/'.$dateD.'　'.$dateH.':'.$dateMin.":".$dateS;
					$due= $dueY.'/'.$dueM.'/'.$dueD.'　'.$dueH.':'.$dueMin.":".$dueS;

					$department= iconv("BIG5","UTF-8",$department);
					$undertaker= iconv("BIG5","UTF-8",$undertaker);


			$tt=NULL;
			$stuednt_array=explode("<br />",$title);
			foreach($stuednt_array as $index => $value)
				{$tt=$tt.$value;}
			
					
					 echo "<tr  align=\"center\" height=\"30\"  ";
					 if ($i%2==1)
					 {echo "bgcolor=\"EEEEEE\"";}
					 
					echo ">";
	 				 echo "<td >" ;
					 if ($QV[$i][0]==2)  //如果是問卷系統
					{	if ($home==1)
							 echo "<a href=\"q_home.php?ID=$id\" class=\"link_style1\">";
						else 
							echo "<a href=\"q_fillin.php?ID=$id\" class=\"link_style1\">";
					}
					else if ($QV[$i][0]==1)        ///////投票系統//////////
					{ 
							 echo "<a href=\"v_home.php?ID=$id\" class=\"link_style1\">";

					}
					echo "$tt</a></td>";
	 				 echo "<td  align=\"center\"> $date </td>";
					 echo "<td  align=\"center\"> $due </td>";
					 echo "<td > $department</td>";
					 echo "<td ><a href=\"mailto:$email\" target=\"_blank\">$undertaker </a></td> ";
					 echo "<td >";
					  if ($analyze==1)
					 { if ($QV[$i][0]==2)
						 echo "<a href=\"q_statistic.php?ID=$id\">看結果</a>";
						else if ($QV[$i][0]==1)
							echo "<a href=\"v_statistic.php?ID=$id\">看結果</a>";
					}
				     echo "</td></tr>" ;
					


}?>
</table>
					

