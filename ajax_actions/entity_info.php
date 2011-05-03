<?php 

	include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php";
	admin_gatekeeper();
	
	$ENTITY_SHOW_HIDDEN_OVERRIDE = true;
	
	$guid = get_input('guid');
	if ($entity = get_entity($guid)) {
		$content = elgg_view_entity($entity, false);
	} else {
		$content = 'Requested entity not found.';
	}
	echo '<div id="entity_info_div">' . $content . '</div>';
?>