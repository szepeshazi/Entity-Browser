<?php

	include_once dirname(dirname(dirname(__FILE__))) . "/engine/start.php";

	global $CONFIG;
	$ENTITY_SHOW_HIDDEN_OVERRIDE = true;
	
	admin_gatekeeper();

	// Prepare views by processing form inputs (current entity type, subtype, limit, offset, sort, displayed properties)
	// and defining some commonly used variables
	include_once dirname(__FILE__) . '/ajax_actions/includes/prepare_views.php';
	
	$guid = get_input('action_guid');
	$entity = get_entity($guid);
	
	if ($entity) {
		echo elgg_view('entity_browser/edit',array(
			'entity' => $entity,
			'count' => $count,
			'offset' => $offset,
			'limit' => $limit,
			'displayed_properties' => $displayed_properties,
			'view_type' => $view_type,
			'sort' => $sort,
			'direction' => $direction,
			'type' => $type,
			'current_type' => $current_type,
			'entity_properties' => $entity_properties,
			'core_properties' => $core_properties
		));
	} else {
		echo elgg_echo('entity_browser:edit:notfound');
	}
?>