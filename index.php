<?php
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$browser = ((strpos($ua, 'iPhone') !== false) || (strpos($ua, 'iPod') !== false) || (strpos($ua, 'Android') !== false));
		if ($browser == true) {
		$browser = 'sp';
	}
	if ($browser == 'sp') {
    // <!-- スマホの場合に読み込むソースを記述 -->
    require('index_sp.php');
    } else {
    // <!-- タブレット・PCの場合に読み込むソースを記述 -->
    require('index_pc.php');
    }
?>
