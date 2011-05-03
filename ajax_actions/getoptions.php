<?php

	include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php";
	admin_gatekeeper();

	global $CONFIG;
	$ENTITY_SHOW_HIDDEN_OVERRIDE = true;
	
	// Prepare views by processing form inputs (current entity type, subtype, limit, offset, sort, displayed properties)
	// and defining some commonly used variables
	include_once dirname(__FILE__) . '/includes/prepare_views.php';
		
	$view_types = array('natural' => 'Natural view', 'raw' => 'Raw view');
	$results_at_a_time = array('10', '25', '50', '100', '250', '500');
	
	$options = array(
		'offset' => $offset,
		'limit' => $limit,
		'full_view' => false,
	);
	if ($type != 'entity') $options['type'] = $type;
	if ($subtype) $options['subtype'] = $subtype;
	if ($type == 'site') $options['site_guids'] = ELGG_ENTITIES_ANY_VALUE;
	
	$entities = elgg_get_entities($options);
	
	$meta_names = array_merge($entity_properties, $core_properties[$type]);
	foreach ($entities as $entity) {
		$meta = get_metadata_for_entity($entity->guid);
		if (is_array($meta) && !empty($meta)) {
			foreach ($meta as $m) {
				if (!in_array($m->name, $meta_names)) {
					$meta_names[] = $m->name;
				}
			}
		}
	}
?>
<div id="options_content">
	<form id="view_options" name="view_options" class="side_form" action="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/getoptions.php">
		<div class="form_field">
			<label for="displayed_properties[]">Displayed properties</label>
			<select name="displayed_properties[]" multiple="multiple">
				<?php foreach($meta_names as $meta_name) { ?>
					<option value="<?php echo $meta_name; ?>"<?php if (is_array($displayed_properties) && in_array($meta_name, $displayed_properties)) echo ' selected="selected"' ; ?>><?php echo $meta_name; ?></option>
				<?php } ?>
			</select>
		</div>
		
		<div class="form_field">
			<label for="view_type">View type</label>
			<select id="view_type" name="view_type">
				<?php foreach($view_types as $key => $value) { ?>
					<option value="<?php echo $key; ?>"<?php echo $view_type == $key ? ' selected="selected"' : ''; ?>><?php echo $value; ?></option>
				<?php } ?>
			</select>
		</div>
	
		<div class="form_field">
			<label for="limit">Show</label>
			<select name="limit">
				<?php foreach($results_at_a_time as $results) { ?>
					<option value="<?php echo $results; ?>"<?php echo $limit == $results ? ' selected="selected"' : ''; ?>><?php echo $results; ?></option>
				<?php } ?>
			</select>
			<span>results on a page</span>
		</div>
	
		<div class="form_field">
			<center><input type="submit" name="submit" value="Refresh"></input></center>
		</div>
		
		<input type="hidden" name="type" id="type" value="<?php echo $current_type; ?>"></input>
	</form>
</div>
<div id="options_loader" style="display: none;">
	<br />
	<br />
	<center>
		<img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif">
	</center>
</div>

<script type="text/javascript">
	var loading = '<br /><br /><center><img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif"></center>';
	$('#view_options').submit(function() { 
		$('#options_content').hide();
		$('#options_loader').show();
	    $(this).ajaxSubmit(function(data) {
			$('#options_container').html(data);
			$('#entity_container').html(loading);
			$('#view_options').attr('action','<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/getentities.php');
			$('#view_options').ajaxSubmit(function(data) {
				$('#entity_container').html(data);
				$('#view_options').attr('action','<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/getoptions.php');
			});
	    }); 
	    return false; 
	});
</script>
