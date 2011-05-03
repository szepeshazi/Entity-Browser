<?php

	$entities 				= $vars['entities'];
	$count 					= $vars['count'];
	$offset 				= $vars['offset'];
	$limit 					= $vars['limit'];
	$displayed_properties 	= $vars['displayed_properties'];
	$view_type 				= $vars['view_type'];
	$sort 					= $vars['sort']; 
	$direction 				= $vars['direction']; 
	$type 					= $vars['type'];
	$current_type 			= $vars['current_type'];
	$entity_properties		= $vars['entity_properties'];
	$core_properties		= $vars['core_properties'];
	
	$nav = elgg_view('navigation/pagination',array(
		'baseurl' => $baseurl,
		'offset' => $offset,
		'count' => $count,
		'limit' => $limit,
	));

	$html = $nav;
	$context = get_context();
	set_context('search');
	if (is_array($entities) && sizeof($entities) > 0) {
		foreach($entities as $entity) {
			$html .= elgg_view_entity($entity, false);
		}
	}
	set_context($context);
	if ($count) {
		$html .= $nav;
	}

?>

<div id="entity_content">
	<form id="view_entities" name="view_entities" action="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/getentities.php">
		<input type="hidden" name="type" value="<?php echo $current_type; ?>"></input>
		<input type="hidden" id="offset" name="offset" value="<?php echo $offset; ?>"></input>
		<input type="hidden" name="limit" value="<?php echo $limit; ?>"></input>
		<input type="hidden" id="sort" name="sort" value="<?php echo $sort; ?>"></input>
		<input type="hidden" id="direction" name="direction" value="<?php echo $direction; ?>"></input>
		<?php foreach ($displayed_properties as $property) { ?>
			<input type="hidden" name="displayed_properties[]" value="<?php echo $property; ?>"></input>
		<?php } ?>
		<input type="hidden" name="view_type" value="<?php echo $view_type; ?>"></input>
		<?php echo $html; ?>
	</form>
</div>
<div id="entity_loader" style="display: none;">
	<br />
	<br />
	<center>
		<img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif">
	</center>
</div>