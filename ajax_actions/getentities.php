<?php

	include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/engine/start.php';

	global $CONFIG;
	$ENTITY_SHOW_HIDDEN_OVERRIDE = true;

	admin_gatekeeper();

	// Prepare views by processing form inputs (current entity type, subtype, limit, offset, sort, displayed properties)
	// and defining some commonly used variables
	include_once dirname(__FILE__) . '/includes/prepare_views.php';
	
	$wheres = array();
	$metadata_name_value_pairs = array();
	$extra_tables = array();
	foreach ($_REQUEST as $key => $value) {
		if ((strpos($key, 'filter:') === 0) && (!empty($value))) {
			$keyparts = explode(':', $key);
			if (in_array($keyparts[1], $entity_properties)) {
				$wheres[] = 'e.' . $keyparts[1] . ' like "%' . $value . '%"';
			} else if (in_array($keyparts[1], $core_properties[$type])) {
				$extra_tables[] = $type;
				$wheres[] = $db_tables_short[$type] . '.' . $keyparts[1] . ' like "%' . $value . '%"';
			} else {
				$metadata_name_value_pairs[] = array('name' => $keyparts[1], 'operand' => 'LIKE', 'value' => '%' . $value . '%');
			}
		}
	}

	$options = array(
		'offset' => $offset,
		'limit' => $limit,
		'full_view' => false,
	);
	if (!empty($extra_tables)) {
		$options['joins'] = array();
		foreach ($extra_tables as $table) {
			$options['joins'][] = "JOIN {$db_tables[$table]} {$db_tables_short[$table]} ON ({$db_tables_short[$table]}.guid = e.guid)";
		}
	}
	
	if ($type != 'entity') $options['type'] = $type;
	if ($subtype) $options['subtype'] = $subtype;
	if ($type == 'site') $options['site_guids'] = ELGG_ENTITIES_ANY_VALUE;
	if (!empty($wheres)) {
		$options['wheres'] = $wheres;
	}
	

	$__get_entities_function = 'elgg_get_entities';
	if (in_array($sort, $entity_properties)) {
		$options['order_by'] = $sort . ' ' . $direction;
	} else if (in_array($sort, $core_properties[$type])) {
		$options['joins'][] = "JOIN {$db_tables[$type]} {$db_tables_short[$type]} ON ({$db_tables_short[$type]}.guid = e.guid)";
		$options['order_by'] = $db_tables_short[$type] . '.' . $sort . ' ' . $direction;
	} else {
		$__get_entities_function = 'elgg_get_entities_from_metadata';
		$options['order_by_metadata'] = array('name' => $sort, 'direction' => strtoupper($direction), 'as' => 'text');
	}
	
	if (!empty($metadata_name_value_pairs)) {
		$__get_entities_function = 'elgg_get_entities_from_metadata';
		$options['metadata_name_value_pairs'] = $metadata_name_value_pairs;
	}
	
	if (!empty($options['joins'])) {
		$options['joins'] = array_unique($options['joins']);
	}
	
	$count = $__get_entities_function(array_merge(array('count' => TRUE), $options));
	$entities = $__get_entities_function($options);
	set_view_location('entities/entity_list', $CONFIG->pluginspath . 'entity_browser/views/mod/' . $view_type . '/');
	
	echo elgg_view('entities/entity_list',array(
		'entities' => $entities,
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
?>

<script type="text/javascript">

	// Handle pagination. Requested offset will be saved into hidden input form variable
	$('a.pagination_number, a.pagination_previous, a.pagination_next').click(function() {
		var targetUrl = $(this).attr('href');
		var regxp = /offset=(\d+)/;
		var matches = regxp.exec(targetUrl);
		$('input#offset').val(matches[1]);
		$('div#entity_content').hide();
		$('div#entity_loader').show();
		$('form#view_entities').ajaxSubmit(function(data) {
			// $('div#entity_loader').hide();
			// $('div#entity_content').show();
			$('div#entity_container').html(data);
		});
		return false;				
	});

</script>