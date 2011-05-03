<?php 

	/**
	 * Processes all common input variables for ajax forms and defines some commonly used variables
	 */

	$entity_properties = array('guid', 'type', 'subtype', 'owner_guid', 'site_guid', 'container_guid', 'access_id', 'time_created', 'time_updated', 'last_action', 'enabled');
	$core_properties = array(
		'entity' => array(),
		'user' => array('name', 'username', 'password', 'salt', 'email' , 'language', 'code', 'banned', 'admin', 'prev_last_action', 'last_login', 'prev_last_login'),
		'object' => array('title', 'description'),
		'group' => array('name', 'description'),
		'site' => array('name', 'description', 'url')
	);
	
	$displayed_properties = get_input('displayed_properties');
	$view_type = get_input('view_type', 'raw');
	$sort = get_input('sort', 'time_created');
	$direction = get_input('direction', 'desc');
	$offset = get_input('offset', 0);
	$limit = get_input('limit', 10);
	
	$type = get_input('type');
	$current_type = $type;
	if (!in_array($type, array('entity', 'user', 'object', 'group', 'site'))) {
		$subtype = $type;
		$type = 'object';
	}
?>