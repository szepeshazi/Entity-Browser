<?php 

	include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php";
	admin_gatekeeper();

	$ENTITY_SHOW_HIDDEN_OVERRIDE = true;
	
	$guid = get_input('guid');
	$property = get_input('property');
	if ($entity = get_entity($guid)) {
		$content = $entity->$property;
		if (is_array($content)) {
			$content = implode(', ', $content);
		}
	} else {
		$content = 'Requested entity not found.';
	}
	echo '<div id="entity_info_div">' . $content . '</div>';
?>