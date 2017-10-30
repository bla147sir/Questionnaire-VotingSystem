<?php session_start(); 
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_pie.php');
require_once ('jpgraph/jpgraph_pie3d.php');

$id=$_GET["ID"];
$i=$_GET["I"];
		$m=m.$i;
		$n=n.$i;
		$maxi=maxi.$i;
$w=$_GET["W"];
$_SESSION["ID"]=$id;
$len=$_GET["len"];
$lenth=$len*6;

$max=((ceil($_SESSION["$maxi"]/5)))*5;

 
$data1y=$_SESSION["$n"];
$cut=count($_SESSION["$n"]);

$graph=new PieGraph($lenth+200,250,'auto');
$graph->SetScale("textlin");

$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);
$p1 = new PiePlot3D($data1y);

$p1->SetCenter(0.5,0.5);
$p1->SetSize(0.35,0.5);
////////////////////////////////
$p1->value->SetFont(FF_CHINESE,FS_NORMAL,10);
$p1->SetLabels( $_SESSION["$m"]);

$p1->SetLabelPos(0.6,0.2);
$p1->SetLabelType(PIE_VALUE_PER);
$p1->value->Show();

$graph->title->SetFont(FF_CHINESE, FS_NORMAL); 


$graph->Add($p1);



$p1->SetSliceColors(array('#30afcf','#f04f3f','#f0f000','#60ef3f'));



$p1->ExplodeAll(8);
$graph->Stroke();

?>