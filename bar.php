<?php session_start(); 



require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_bar.php');




$i=$_GET["I"];
		$m=m.$i;
		$n=n.$i;
		$maxi=maxi.$i;
$w=$_GET["W"];

$len=$_GET["len"];
$lenth=$len*5;

$max=((ceil($_SESSION["$maxi"]/5)))*5;

 
$data1y=$_SESSION["$n"];

$graph=new Graph($lenth+150,(int)$w,250);
$graph->SetScale("textlin");

$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);

////////////////////////////////
$graph->Set90AndMargin($lenth,40,40,40);
$graph->img->SetAngle(90);

$graph->yaxis->SetTickPositions(array(0,$max/5,$max/5*2,$max/5*3,$max/5*4,$max),array(50,100,150,200,250));
$graph->SetBox(false);

$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels( $_SESSION["$m"]);

$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

$b1plot = new BarPlot($data1y);

$gbplot = new GroupBarPlot(array($b1plot));
$graph->Add($gbplot);

$b1plot->SetColor("white");
$b1plot->SetFillColor("#eeaa11");

$graph->title->SetFont(FF_CHINESE, FS_NORMAL); 
$graph->xaxis->SetFont(FF_CHINESE, FS_NORMAL);
$graph->Stroke();

?>