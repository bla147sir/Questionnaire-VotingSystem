<?
//*********************************************************************(步驟2)
				if(isset($_POST['import']) || $_SESSION["done"]==0){
						$file=$_FILES["file"]["name"];
						$file_extension  = substr ($file, -3); 
						
						//*****************改過的黨名跟副檔名合併
						if($_SESSION["done"]==0)				
							$fname="e".$id.".".$file_extension;   // elector 
						else if($type==1)
							$fname="c".$id.".".$file_extension;   // candidate
						else if($type==2)
							$fname="s".$id.".".$file_extension;   // slogan
						else if($type==6)
							$fname="m".$id.".".$file_extension;   // masterpiece
						
						move_uploaded_file($_FILES["file"]["tmp_name"],"import/".$fname);

						//*****************.csv 
						if($file_extension=='csv'){
							$i=0;
							
								if (($handle = fopen("import/".$fname, "r")) !== FALSE) {
									while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
											//*********************************
											if($type==1 || $type==2 ){
												for ($c=0; $c < 5; $c++) {
													$d[$i][$c]=$data[$c] ;
														if($i>0) //第一行不要取
															$_SESSION['data'][$i+1][$c+1]=$d[$i][$c];
															//	echo $i.":".$c.":".$_SESSION['data'][$i+1][$c+1]."<br>";	
												} // for
											} // if
											//*********************************
											else {
												for ($c=0; $c < 4; $c++) {
													$d[$i][$c]=$data[$c] ;
														if($i>0) //第一行不要取
															$_SESSION['data'][$i+1][$c+1]=$d[$i][$c];
															//	echo $i.":".$c.":".$_SESSION['data'][$i+1][$c+1]."<br>";	
												} // for
											} // if
									
										$i++;
										
									$_SESSION['count']=$i;
									} // while
									
									fclose($handle);
								} // if
							
						} // if =.csv
						//*****************.xls
						if($file_extension=='xls'){
							
								require_once 'Excel/reader.php';
								$data = new Spreadsheet_Excel_Reader();
								$data->setOutputEncoding('BIG5');
								$data->read('import/'.$fname);
								error_reporting(E_ALL ^ E_NOTICE);
								//*************************************************** 
									for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
										for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
											$value[$i][$j] = $data->sheets[0]['cells'][$i][$j];
											$_SESSION['data'][$i-1][$j]=$value[$i][$j];
											//echo $i.":".$j.":".$_SESSION['data'][$i-1][$j];
											//echo "<br>";
										}
									

									}//for
						
							$_SESSION['count']=$i-1;
						} // if=.xls

							include("v_design_insertDB.php");
							
							if($type){
								$url2 = "v_design3.php?ID=$id"; 
								echo "<script type='text/javascript'>";
								echo "window.location.href='$url2'";
								echo "</script>";
							}
							
					} //if import
				
				