<?php

	include_once dirname(dirname(dirname(__FILE__))) . "/engine/start.php";

	global $CONFIG;

	admin_gatekeeper();
	set_context('admin');
	set_page_owner($_SESSION['guid']);

	elgg_extend_view('metatags','entity_browser/metatags');
	set_view_location('page_elements/header', $CONFIG->pluginspath . 'entity_browser/views/mod/');
	
	$body = elgg_view_title(elgg_echo('entity_browser:admin:title'));
	
	$body .= elgg_view("entity_browser/admin");
	
	page_draw(elgg_echo('entity_browser:admin:title'), elgg_view_layout("one_column", $body));

?>
