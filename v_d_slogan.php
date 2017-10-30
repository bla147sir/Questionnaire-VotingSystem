
<script type="text/javascript">

$(document).ready(function(){

    var counter = 1;
	var counter2= 0;
	var counter3= 0;
	var counter4= 0;
	var counter5= 0;
	var done = "<?echo $done;?>"; 
	var no = "<?echo $no;?>"; 
	var j=0;

	if(done==1) 
	{
		counter=1;	
		no=1;
		
	}
	else if(done==2) 
		{
			counter=(no*5)+1;
			
		}
	
	var r=0;
	var i=0;
	//*******************************************add button
    $("#addButton").click(function () {

    if(counter>100000){
            alert("Only 10 textboxes allow");
            return false;
    }   


	counter2=counter+1;
	counter3=counter+2;
	counter4=counter+3;
	counter5=counter+4;
	k=(counter/5)+0.8;

    var newTextBoxDiv = $(document.createElement('div'))
         .attr("id", 'TextBoxDiv' + counter);

    newTextBoxDiv.after().html('<div class="col-xs-1">'+ k + '</div>' +
          '<div class="col-xs-2"><input type="text" class="form-control" name="textbox' + counter + 
          '" id="textbox' + counter + '" value="" ></div>'+
          '<div class="col-xs-2"><input type="text" class="form-control" name="textbox' + counter + 
          '" id="textbox' + counter2 + '" value="" ></div>'+
          '<div class="col-xs-2"><input type="text" class="form-control" name="textbox' + counter + 
          '" id="textbox' + counter3 + '" value="" ></div>'+
          '<div class="col-xs-5"><input type="text" class="form-control" name="textbox' + counter +
          '" id="textbox' + counter4 + '" value="" ></div>'+'<br><br><br>'+
          '<input type="text" class="form-control" name="textbox' + counter + 
          '" id="textbox' + counter5 + '" value="" size="100" placeholder="description">'+'<br><br>');
	 

    newTextBoxDiv.appendTo("#TextBoxesGroup");
	counter=counter+5;
     });

	//*******************************************remove button
     $("#removeButton").click(function () {
    if(counter==1){
          alert("No more textbox to remove");
          return false;
       }   

	r=counter5;
	
		for(i=1;i<=5;i++)
		 {
			$("#TextBoxDiv" + r).remove();
			r--;
		 }
	
		counter=counter-5;
		counter5=counter5-5;
     });
	 
	 //*******************************************next step button
     $("#getButtonValue").click(function () {

    var msg = '';

	if(done==1){
		//****************取每行有幾筆
		var row = document.getElementById('t1').value;
		//****************是否要顯示作者/ 創作理念
		var selected=[];
        $("[name=detail]:checkbox:checked").each(function(){
          selected.push($(this).val());
          });
			
		var detail;
			if(selected.join()==1) detail="01";
				
			else if(selected.join()==2)	detail="10";
				
			else if(selected.join()=="") detail="00";
				
			else if(selected.join()==1,2) detail="11";
		
	} //done=1
	if(done==1) { j=1;	}
	else if(done==2) 	{ no++; j=(no-1)*5+1; }

	msg+=k;

	
    for(i=j; i<counter; i++){
         msg += ","+$('#textbox' + i).val()  ;
    }

	 id=<?echo $id; ?>;
	
	if(done==1)
    location.href="v_dymanic_db.php?ID="+id+'&value='+msg+'&no='+no+'&row='+row+'&detail='+detail;
	else
	location.href="v_dymanic_db.php?ID="+id+'&value='+msg+'&no='+no+'&detail='+detail;
     });
  });
</script>
