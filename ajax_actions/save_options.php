<?php
    session_name('Elgg');
    session_start();
    $_SESSION['entity_browser_options'] = $_REQUEST['options'];
    error_log((print_r($_SESSION['entity_browser_options'], 1)));
    exit;
?>