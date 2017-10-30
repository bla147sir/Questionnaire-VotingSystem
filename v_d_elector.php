<?
include("connect.php");

session_start();

$id=$_SESSION["get_id"];
$value=$_GET['value'];
$depart=$_GET['depart'];

$sql_dept_name="select DEPT_FULL_NAME from STFDEPT where DEPT_NO='$value' ";

	$stmt_dept_name=ociparse($con,$sql_dept_name); 
	ociexecute($stmt_dept_name,OCI_DEFAULT); 
	  
	$nrows_dept_name = OCIFetchStatement($stmt_dept_name,$results_dept_name);

	$dept_chn_name=$results_dept_name['DEPT_FULL_NAME'][0];
	$dept_chn_name=iconv("BIG5","UTF-8", $dept_chn_name);

	echo $dept_chn_name;

if($depart==1)
 echo "考績/甄審委員名單";
else if ($depart==2)
 echo "編制內職員";
else if ($depart==3)
 echo "各學院專任教師";
else if ($depart==4)
 echo "性別平等教育委員會(職工代表)";
else if ($depart==5)
 echo "校務會議研究人員代表";
else if ($depart==6)
 echo "校務會議職員代表";

?>
<html>

<head>
    <title>選擇人員</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-1.2.6.pack.js"></script>  
	<script type="text/javascript" src="js/jquery.autocomplete.js"></script>

	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

<script type="text/javascript">

function add_org(fbox, tbox) {
        var flag = 0; //判斷目的選單是否已存在相同文字
        var no = new Option();

        no.value =fbox.value.slice(4,11);
        no.text = fbox.value.slice(0,3);
		
        for (var j = 0; j < tbox.options.length; j++) {
            if (tbox.options[j].value == no.value) flag = 1;
        }

        if (flag == 0) tbox.options[tbox.options.length] = no; //目的選單不存在相同文字才做
        fbox.value = "";
    }

  var c = 0;

    function move(fbox, tbox) {
        c = 0;
        var flag = 0; //判斷目的選單是否已存在相同文字
        for (var i = 0; i < fbox.options.length; i++) {
            if (fbox.options[i].selected && fbox.options[i].value != "") {
                fbox.options[i].selected = false;
                c++;
                var no = new Option();
                no.value = fbox.options[i].value;
                no.text = fbox.options[i].text;
                flag = 0;
                for (var j = 0; j < tbox.options.length; j++) {
                    if (tbox.options[j].value == no.value) flag = 1;
                }
                if (flag == 0) tbox.options[tbox.options.length] = no; //目的選單不存在相同文字才做
                fbox.options[i].value = "";
                fbox.options[i].text = "";
            }
        }
        BumpUp(fbox);
    }

	  function BumpUp(box) {
        for (var i = 0; i < box.options.length; i++) {
            if (box.options[i].value == "") {
                for (var j = i; j < box.options.length - 1; j++) {
                    box.options[j].value = box.options[j + 1].value;
                    box.options[j].text = box.options[j + 1].text;
                }
                var ln = i;
                break;
            }
        }
        if (ln < box.options.length) box.options.length -= 1;
        if (c > 1) {
            c--;
            BumpUp(box);
        }
    }

	function move_v(n) {
        var index = document.form1.list2.selectedIndex;
        var list2 = document.form1.list2;
        var total = list2.options.length - 1;
        if (n == "-") to = -1;
        else to = 1;
        if (index == -1) return;
        if (to == 1 && index == total) return;
        if (to == -1 && index == 0) return;
        var items = new Array;
        var values = new Array;
        for (i = total; i >= 0; i--) {
            items[i] = list2.options[i].text;
            values[i] = list2.options[i].value;
        }
        for (i = total; i >= 0; i--) {
            if (index == i) {
                list2.options[i + to] = new Option(items[i], values[i], 0, 1);
                list2.options[i] = new Option(items[i + to], values[i + to]);
                i--;
            } else list2.options[i] = new Option(items[i], values[i]);
        }
        list2.focus();
    }

</script>

<script type="text/javascript">

$(function() {
  
        $("#t1").autocomplete('q.php', {
            matchContains: true,
			delay: 0,
            width: 150,
            max: 100,
            scroll: true
        });

});

function save_result(obj,tbox) {
 
	var str = "";
        var c = 0;
        for (var i = 0; i < tbox.options.length; i++) {
            if (tbox.options[i].value != "") {
                c++;
                if (c == 1) str = tbox.options[i].value;
                else str = str + '、' + tbox.options[i].value;
            }
        }

	location.href="v_d_elector.php?value="+obj+'&list_text=' + str;
    }

 
function change(obj,tbox){

	var str = "";
        var c = 0;
        for (var i = 0; i < tbox.options.length; i++) {
            if (tbox.options[i].value != "") {
                c++;
                if (c == 1) str = tbox.options[i].value;
                else str = str + '、' + tbox.options[i].value;
            }
        }


	location.href="v_d_elector.php?depart="+obj+'&list_text=' + str;

}

</script>

<script>

	//提交存值 
function submitForm(){

//form1.submit();

n2 = document.form1.list2; 
n2_len= n2.options.length; 
var n2_val=''; 
for(i=0;i<n2_len;i++){ 
n2_val+=n2.options[i].value; 
if(i!=n2_len-1){ 
n2_val+=','; 
} 
}
//alert(n2_val);//存值：例如：

 location.href="save_data.php?value="+n2_val;

} 
</script>
	
</head>

<body>
	
<form name="form1" action="" method="POST" enctype="multipart/form-data">
  <input type="hidden" name='list_data' value=''>
  <input type="hidden" name='save_flag' value='0'>
  <input type="hidden" name='field_type' value='5'>
<table width="800" height="400" class="table table-striped">
  <tr>
    <td width="300" height="70">
      <select name='dept_qry' onchange="save_result(this.value,document.form1.list2)"; class="form-control">
                            <option value=''>請選擇單位</option>
                            <option value='M00'>校長室</option>
                            <option value='M10'>副校長室</option>
                            <option value='M20'>教務處</option>
                            <option value='M30'>學生事務處</option>
                            <option value='M40'>總務處</option>
                            <option value='M60'>圖書館</option>
                            <option value='M70'>秘書室</option>
                            <option value='M80'>人事室</option>
                            <option value='M90'>主計室</option>
                            <option value='MA0'>師資培育中心</option>
                            <option value='MD0'>體育室</option>
                            <option value='ME0'>學生心理諮商與輔導中心</option>
                            <option value='MF0'>電子計算機中心</option>
                            <option value='MI0'>教學卓越中心</option>
                            <option value='MJ0'>進修學院</option>
                            <option value='MK0'>數位學習中心</option>
                            <option value='ML0'>語文中心</option>
                            <option value='MM0'>國際暨兩岸事務處</option>
                            <option value='MN0'>環境保護暨安全衛生中心</option>
                            <option value='MO0'>人類研究倫理治理中心</option>
                            <option value='MP0'>研究倫理審查委員會</option>
                            <option value='N10'>研究發展處</option>
                            <option value='N20'>社區心理諮商及潛能發展中心</option>
                            <option value='N30'>特殊教育中心</option>
                            <option value='N50'>科學教育中心</option>
                            <option value='N60'>技職教育中心</option>
                            <option value='N70'>環境教育中心</option>
                            <option value='N90'>科技研究總中心</option>
                            <option value='O20'>創新育成中心</option>
                            <option value='100'>教育學院</option>
                            <option value='110'>輔導與諮商學系</option>
                            <option value='120'>特殊教育學系</option>
                            <option value='130'>教育研究所</option>
                            <option value='140'>復健諮商研究所</option>
                            <option value='170'>婚姻與家族治療研究所</option>
                            <option value='200'>理學院</option>
                            <option value='210'>科學教育研究所</option>
                            <option value='220'>數學系</option>
                            <option value='230'>物理學系</option>
                            <option value='240'>生物學系</option>
                            <option value='250'>化學系</option>
                            <option value='260'>光電科技研究所</option>
                            <option value='270'>生物技術研究所</option>
                            <option value='280'>統計資訊研究所</option>
                            <option value='300'>技術及職業教育學院</option>
                            <option value='310'>工業教育與技術學系</option>
                            <option value='320'>人力資源管理研究所</option>
                            <option value='330'>財務金融技術學系</option>
                            <option value='340'>車輛科技研究所</option>
                            <option value='350'>數位學習研究所</option>
                            <option value='400'>文學院</option>
                            <option value='410'>英語學系</option>
                            <option value='420'>國文學系</option>
                            <option value='430'>地理學系</option>
                            <option value='440'>美術學系</option>
                            <option value='460'>兒童英語研究所</option>
                            <option value='470'>翻譯研究所</option>
                            <option value='480'>台灣文學研究所</option>
                            <option value='4A0'>歷史學研究所</option>
                            <option value='500'>工學院</option>
                            <option value='510'>機電工程學系</option>
                            <option value='520'>電機工程學系</option>
                            <option value='530'>電子工程學系</option>
                            <option value='540'>資訊工程學系</option>
                            <option value='570'>電信工程學研究所</option>
                            <option value='600'>管理學院</option>
                            <option value='610'>資訊管理學系</option>
                            <option value='620'>會計學系</option>
                            <option value='630'>企業管理學系</option>
                            <option value='700'>社會科學暨體育學院</option>
                            <option value='710'>運動學系</option>
                            <option value='720'>通識教育中心</option>
                            <option value='770'>運動健康研究所</option>
                            <option value='780'>公共事務與公民教育學系</option>
                        </select>
    <br>
      <select name="meet_qry" id="meet_qry" onchange="change(this.value,document.form1.list2)"; class="form-control">
				<option value='0'>請選擇人員群組</option>
				<option value='1'>考績/甄審委員名單</option>
				<option value='2'>編制內職員</option>
				<option value='3'>各學院專任教師</option>
				<option value='4'>性別平等教育委員會(職工代表)</option>
				<option value='5'>校務會議研究人員代表</option>
				<option value='6'>校務會議職員代表</option>
      </select>
    </td>
    <td width="100">&nbsp;</td>
    <td colspan="2">
	<div class="form-group col-xs-7">
    <input type="text" name="t1" id="t1" class="form-control" />
	</div>
	<div class="form-group col-xs-3">
	<input type="button" name="submit1" value="新增(自動查詢)" class='btn btn-primary'
	onclick="add_org(document.form1.t1,document.form1.list2)" ;>
	</div>
    </td>
  </tr>
  <tr>
    <td height="28"><span class="label label-success">可選擇人員</span></td>
    <td>&nbsp;</td>
    <td colspan="2"><span class="label label-success">已選擇人員</span></td>
  </tr>
  <tr>
    <td height="300">
    <select multiple size="13" name="list1" style="width:220px;font-size:14px;color:black" class="form-control">
	</select>
	<br>
	<span style="font-size:10pt;color:#0000FF">[ 可使用 Shift 或 Ctrl 做多項選擇 ]</span>
    </td>
    <td>
    <input type="button" value="   &gt;&gt;   "  class='btn btn-primary' onclick="move(document.form1.list1,document.form1.list2)" name="btn1">
    <br>
	<br>
	<input type="button" value="   &lt;&lt;   "  class='btn btn-primary' onclick="move(document.form1.list2,document.form1.list1)" name="btn2">
    </td>
    <td width="300">
    <select multiple size="13" name="list2" style="width:220px;font-size:14px;color:blue" class="form-control">
<?
	
  $list_text="";
  if ($_POST[list_data] != NULL)  $list_text=$_POST[list_data];
  if ($_GET[list_text] != NULL)   $list_text=$_GET[list_text];


  if ($list_text != NULL) //若有帶字串過來 
  {      
    $receive_array=explode("、",$list_text); ////將機關依、符號分割並存於array
    $receive="";
    while (list($k,$receive_value) = each ($receive_array))
    {
      $receive_value=$receive_value;

		if(strlen($receive_value)==7)
		{
		$sql6="select CRJB_DEPART from PSFCRJB where CRJB_EMPL_NO='$receive_value' ";

		$stmt6=ociparse($con,$sql6); 
		ociexecute($stmt6,OCI_DEFAULT); 
	 
		$nrows6 = OCIFetchStatement($stmt6,$results6);

		$d_no=$results6['CRJB_DEPART'][0];

		$receive_value= $d_no."a".$receive_value;
		}

	  $str_array1=explode('a',$receive_value);

		$pno=$str_array1[1];

		$sql4="select * from PSFEMPL where EMPL_NO ='$pno' ";

		$stmt4=ociparse($con,$sql4); 
		ociexecute($stmt4,OCI_DEFAULT); 
	  
		$nrows4 = OCIFetchStatement($stmt4,$results4);

		$chn_name=$results4['EMPL_CHN_NAME'][0];
		$chn_name=iconv("BIG5","UTF-8", $chn_name);

      if ($receive_value != NULL) echo "<option value='$receive_value'>$chn_name</option>";
    }
  }
//-----------------------------------------------------------  


if($_SESSION["count_elector"]==0)
{
$sql_check="select * from ELECTOR where ID='$id'";
$stmt_check=ociparse($con,$sql_check); 
ociexecute($stmt_check,OCI_DEFAULT); 
	  
$nrows_check = OCIFetchStatement($stmt_check,$results_check);

for($i=0;$i<$nrows_check;$i++)
{
$name[$i]=$results_check['NAME'][$i];
$dep[$i]=$results_check['DEPARTMENT'][$i];
$pno[$i]=$results_check['P_NO'][$i];


$name[$i]=iconv("BIG5","UTF-8", $name[$i]);

// -----------------------------------------------------  

$sql2="select * from  STFDEPT where DEPT_FULL_NAME='$dep[$i]' ";
$stmt2=ociparse($con,$sql2); 
ociexecute($stmt2,OCI_DEFAULT); 
	  
$nrows2 = OCIFetchStatement($stmt2,$results2);

$d_no[$i]=$results2['DEPT_NO'][0];

$var[$i]=$d_no[$i].'a'.$pno[$i];
//--------------------------------------------------------

echo "<option value='$var[$i]'>$name[$i]</option>";

}

$_SESSION["count_elector"]=$_SESSION["count_elector"]+1;
}
?>


</select>
	<br>
	<span style="font-size:10pt;color:#0000FF">[ 可針對已選擇項目做順序調整 ]</span>
    </td>
    <td width="100">
	<input type="button" value="往上移" onclick="move_v('-');return false" class='btn btn-primary'  name="B3">
	<br> 
	<br>
    <input type="button" value="往下移" onclick="move_v('+');return false" class='btn btn-primary'  name="B4">
	</td>
  </tr>
  <tr>
    <td height="28"></td>
    <td>
    <input type="button" value="確定" name="yes" class='btn btn-primary'  onClick="submitForm();" >
    <input type="button" value="取消" name="no"  class='btn btn-primary' >
	</td>
    <td colspan="2"></td>
  </tr>
  
</table>

</form>
</body>

</html>

<?

echo "<script type=\"text/javascript\">";
echo "document.all.list1.options.length=0;";   // 避免重複資料
$sql="select distinct(EMPL_CHN_NAME),EMPL_NO from PSFCRJB,PSFEMPL where empl_no=crjb_empl_no and substr( CRJB_DEPART,1,2)='".substr($value,0,2)."' and crjb_quit_date is null and substr(EMPL_NO,1,1) IN  ('0','5','7','3')";
 

		$stmt=ociparse($con,$sql); 
		ociexecute($stmt,OCI_DEFAULT); 
	  
		$nrows = OCIFetchStatement($stmt,$results);

		for($i=0; $i<$nrows ;$i++) 
		{	
			$d[$i]=$results['EMPL_NO'][$i];
			$str="$value"."a"."$d[$i]";
			$b[$i]=$results['EMPL_CHN_NAME'][$i] ;
			$b[$i]=iconv("BIG5","UTF-8", $b[$i]);

			echo "document.all.list1.options.add(new Option('".$b[$i]."','".$str."'));";
		}

for($k=1;$k<=6;$k++)
{	if($depart==$k)
	{
		echo "document.all.list1.options.length=0;";
		 include("sql_meet_$k.php"); 
	}	
}


echo "</script>"

?>
