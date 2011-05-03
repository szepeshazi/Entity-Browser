<?php 

	session_name('Elgg');
	session_start();

	if (isset($_SESSION['theme_builder_prefs'])) {
		$prefs = $_SESSION['theme_builder_prefs'];
	} else {
		$prefs = '';
	}

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
    header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
    header("Cache-Control: no-cache, must-revalidate" );
    header("Pragma: no-cache" );
    header("Content-type: application/json");
	
    echo $prefs;
	exit;
?>