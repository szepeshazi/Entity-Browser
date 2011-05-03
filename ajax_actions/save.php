<?php 
	/**
	 * Save entity action
	 */

	include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/engine/start.php';

	global $CONFIG;
	$ENTITY_SHOW_HIDDEN_OVERRIDE = true;

	admin_gatekeeper();

	include_once dirname(__FILE__) . '/includes/prepare_views.php';
	
	$entity_guid = get_input('entity_guid');
	$entity = get_entity($entity_guid);

	// Gather all current meta names attached to the entity
	$meta_names = array();
	$meta = get_metadata_for_entity($entity_guid);
	if (is_array($meta) && !empty($meta)) {
		foreach ($meta as $m) {
			if (!in_array($m->name, $meta_names)) {
				$meta_names[] = $m->name;
			}
		}
	}
	
	// Update base properties
	$base_properties = array_merge($entity_properties, $core_properties[$entity->type]);
	foreach ($base_properties as $property) {
		$value = get_input('property:' . $property);
		if ($value) {
			$entity->$property = $value;
		}
	}
	
	// Remove all previous metadata
	clear_metadata($entity_guid);
		
	// Update previously existing metadata
	foreach ($meta_names as $property) {
		$value = get_input('property:' . $property);
		if ($value) {
			$entity->$property = $value;
		}
	}
	
	// Add any newly created metadata
	$new_meta_names = get_input('new_meta_names');
	$new_meta_values = get_input('new_meta_values');
	
	if (is_array($new_meta_names) && count($new_meta_names)) {
		for ($i = 0; $i < count($new_meta_names); $i++) {
			$entity->$new_meta_names[$i] = $new_meta_values[$i];
		}
	}

	if (!$entity->save()) {
		$message = 'Could not save entity.';
	} else {
		$message = 'Entity saved.';
	}
	
?>

<center>
<br />
	<?php echo $message; ?>
<br />
	Reloading entity list...
<br />
	<img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif">
</center>
