<?php

//預設每頁筆數(依需求修改)

 //預設頁數
 $num_pages = 1;
 //若已經有翻頁，將頁數更新
 if (isset($_GET['page'])) {
   $num_pages = $_GET['page'];

   } 

 //本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
 $startRow_records = ($num_pages -1) * $pageRow_records;

 //計算總頁數=(總筆數/每頁筆數)後無條件進位。
 $total_pages = ceil($total_records/$pageRow_records);



// 顯示的頁數範圍
$range = 3;
 
// 若果正在顯示第一頁，無需顯示「前一頁」連結
if ($num_pages > 1) {
	// 使用 << 連結回到第一頁
	echo " <a href=index.php?page=1> <kbd> << </kbd> </a> ";
	// 前一頁的頁數
	$prevpage = $num_pages - 1;
	// 使用 < 連結回到前一頁
	echo " <a href=index.php?page=".$prevpage."> <kbd> < </kbd></a> ";
} // end if
 
// 顯示當前分頁鄰近的分頁頁數
for ($x = (($num_pages - $range) - 1); $x < (($num_pages + $range) + 1); $x++) {
	// 如果這是一個正確的頁數...
	if (($x > 0) && ($x <= $total_pages)) {
		// 如果這一頁等於當前頁數...
		if ($x == $num_pages) {
			// 不使用連結, 但用高亮度顯示
			echo "　<kbd>".$x."</kbd>　";
			// 如果這一頁不是當前頁數...
		} else {
			// 顯示連結
			echo " <a href=index.php?page=".$x."> <kbd> ".$x." </kbd></a> ";
		} // end else
	} // end if
} // end for
 
// 如果不是最後一頁, 顯示跳往下一頁及最後一頁的連結
if ($num_pages != $total_pages) {
	// 下一頁的頁數 
	$nextpage = $num_pages + 1;
	// 顯示跳往下一頁的連結
	echo " <a href=\"index.php?page=$nextpage\" ><kbd> > </kbd> </a> ";
	// 顯示跳往最後一頁的連結
	echo " <a href=\"index.php?page=$total_pages\"><kbd> >> </kbd></a> ";
} // end if
?>