<?php

	include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php";
	admin_gatekeeper();

	$ENTITY_SHOW_HIDDEN_OVERRIDE = true;
	
	$node_id = get_input('id');
	switch ($node_id) {
		case 1:
		case 'entity':
			$options = array(
				'type' => 'user',
				'count' => true
			);
			$users_count = elgg_get_entities($options);
			$options['type'] = 'object';
			$objects_count = elgg_get_entities($options);
			$options['type'] = 'group';
			$groups_count = elgg_get_entities($options);
			$options['type'] = 'site';
			$options['site_guids'] = ELGG_ENTITIES_ANY_VALUE;
			$sites_count = elgg_get_entities($options);
			$entities_count = $users_count + $objects_count + $groups_count + $sites_count;
			
			$nodes = array(
				'data' => array('title' => "Entities ({$entities_count})", 'attr' => array('id' => 'entity')),
				'state' => 'open',
				'children' => array(
					array('data' => array('title' => "Users ({$users_count})"), 'attr' => array('id' => 'user')),
					array('data' => array('title' => "Objects ({$objects_count})"), 'attr' => array('id' => 'object'), 'state' => 'closed'),
					array('data' => array('title' => "Groups ({$groups_count})" ), 'attr' => array('id' => 'group')),
					array('data' => array('title' => "Sites ({$sites_count})"), 'attr' => array('id' => 'site')),
				)
			);
			break;
		case 'object':
		default:
			$entity_stats = get_entity_statistics();
			$nodes = array();
			foreach($entity_stats['object'] as $subtype => $count) {
				$nodes[] = array('data' => array('title' => "{$subtype} ({$count})"), 'attr' => array('id' => $subtype));
			}
			break;
			
	}
			
	
	header("HTTP/1.0 200 OK");
	header('Content-type: text/json; charset=utf-8');
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Pragma: no-cache");
	echo json_encode($nodes); 
	die();
?>

