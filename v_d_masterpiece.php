<?	
	if($done==2){  // if need hyperlink in first , then show it in second
		$select_hyperlink="select * from masterpiece where ID='$id'";
		$stmt=ociparse($con,$select_hyperlink);     
		ociexecute($stmt,OCI_DEFAULT); 
		$t= OCIFetchStatement($stmt,$result);
		$hyperlink=$result['HYPERLINK'][0];

}

?>
	<table class="table table-bordered">
	<tr class="success"><td>
	<h5><font color="red">【作者】【單位系級】【作品名字】【創作理念】皆可不填</font></h5>	
	</tr>
	</td>
	</table>

<div class='container'>
			<div class="col-xs-4"><label class="control-label">作品數量:</label>
				<div class="input-group">
					 <input type="text" class="form-control" name="quantity" maxlength="3" value="<?echo $_POST['quantity']; ?>">
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit" name="submit">送出</button>
						</span>
					</div><!-- /input-group -->
			</div><!-- /.col-xs-4 -->
			<br>
			<!--是否要有超連結 --> 

			<? if($done==1){ ?>
			<strong>是否要有超連結:</strong>
				<label class="radio-inline">
					<input type="radio" name="address"  value="1" <?php if (isset($_POST['address']) && $_POST['address']=="1") echo "checked";?>>是 	
				</label>
				<label class="radio-inline">
					<input type="radio" name="address"  value="0" <?php if ($_POST['address']!=1) echo "checked";?> >否
				</label>
			<? }?>
		</div><!--row-->
		<br>
		<? if(isset($_POST['submit'])){

			$address=$_POST['address'] ; 
		
		
		if($done==1){
		//是否顯示作者單位/創作理念 
			echo "<div class=\"form-group\"><strong>是否顯示作者單位/創作理念</strong>";
			 
					$c=array("","作者單位","創作理念");
					echo "<label class=\"checkbox-inline\">
						<input type=\"checkbox\" name=\"detail[1]\" value=\"1\" checked>$c[1]
						</label>";
						
					for($i=2;$i<=2;$i++){
						echo "<label class=\"checkbox-inline\">
							<input type=\"checkbox\" name=\"detail[$i]\" value=\"$i\">$c[$i]
							</label>";
					}
					
		
		?>
		</div>
		<!--每行顯示筆數-->
		
		<div class="form-group "><strong>每行顯示筆數</strong>
						<select name="row"  class="form-control" >              
						<option value="1" selected>1</option>
						<option value="2" >2</option>
						<option value="3" >3</option>
						</select> 
		</div>
		
		<br>
		<?} //if done=1 ?> 
		<table class="table table-striped"><tr>
		<td width="50">筆數</td><td width="150">作者</td><td width="150">單位/系級</td><td>作品名字</td><td>檔案上傳</td></tr>
		

		<? for($i=1;$i<=$_POST['quantity'];$i++){ ?>
							
		<tr>
		<!--筆數-->

		<td><? echo $i;?></td>
							
		<!--作者-->
							
		<td width="150"><div class="form-group col-xs-12">
		<input type='text'  class="form-control" name='author[]'  value='<? echo $author;?>' maxlength="5"/></div></td>
													
		<!--單位-->
							
		<td width="150"><div class="form-group col-xs-12">
		<input type='text'  class="form-control" name='auth_dpt[]'  value='<? echo $auth_dpt;?>' maxlength="8"/></div></td>

		<!--作品名字-->
							
		<td width="150"><div class="form-group col-xs-12">
		<input type='text'  class="form-control" name='name[]'   maxlength="8"/></div></td>
		
		<!--檔案上傳-->
							
		<td> <div class="form-group ">
		<input type="hidden" name="MAX_FILE_SIZE" value="10000000000"> <!--10MB-->    
		<input name="file[]" type="file" >
		</div></td>
		
		</tr>		
		<tr>	
		<!--創作理念-->
		<td></td>					
		<td colspan="4"><div class="form-group col-xs-12 ">
		<textarea class="form-control" name='description[]' placeholder="創作理念"  value='<? echo $description;?>' maxlength="100" rows="1" /></textarea></div></td>					

		</tr>
		
		<? if($address==1||$hyperlink){ ?>
		<tr>	
		<!--超連結網址-->
		<td></td>					
		<td colspan="4"><div class="form-group col-xs-12 ">
		<textarea class="form-control" name='address[]' placeholder="超連結網址"  value='<? echo $address;?>' maxlength="100" rows="1" /></textarea></div></td>					

		</tr>
							
		<? } //address=1 
		  }//for ?> 

		</table>
						
		<div class="text-right " >
		<button type="submit" name="B1" class="btn btn-default">下一步</button>
		</div> 
	
		<?}//submit ?>
</div> <!--container-->

