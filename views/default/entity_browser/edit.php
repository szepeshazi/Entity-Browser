<?php 

	global $CONFIG;
	
	$entity 				= $vars['entity'];
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
	
	if ($entity) {
		$non_editables = array('guid', 'type', 'subtype');

		$meta_names = array();
		$meta = get_metadata_for_entity($entity->guid);
		if (is_array($meta) && !empty($meta)) {
			foreach ($meta as $m) {
				if (!in_array($m->name, $meta_names)) {
					$meta_names[] = $m->name;
				}
			}
		}
		
		$all_properties = array_merge($entity_properties, $core_properties[$entity->type], $meta_names);
		
?>
<div id="entity_content">
	<form id="edit_entity" name="edit_entity" action="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/save.php">
		<input type="hidden" name="entity_guid" value="<?php echo $entity->guid; ?>"></input>
		<input type="hidden" name="type" value="<?php echo $current_type; ?>"></input>
		<input type="hidden" id="offset" name="offset" value="<?php echo $offset; ?>"></input>
		<input type="hidden" name="limit" value="<?php echo $limit; ?>"></input>
		<input type="hidden" id="sort" name="sort" value="<?php echo $sort; ?>"></input>
		<input type="hidden" id="direction" name="direction" value="<?php echo $direction; ?>"></input>
		<input type="hidden" id="action_guid" name="action_guid" value=""></input>
		<?php foreach ($displayed_properties as $property) { ?>
			<input type="hidden" name="displayed_properties[]" value="<?php echo $property; ?>"></input>
		<?php } ?>
		<?php 
			foreach ($displayed_properties as $property) {
				$info_type = isset($info_array['info_type:' . $property]) ? $info_array['info_type:' . $property] : 'no_info';
		?>
			<input type="hidden" id="info_type:<?php echo $property?>" name="info_type:<?php echo $property?>" value="<?php echo $info_type; ?>"></input>
		<?php } ?>
		<input type="hidden" name="view_type" value="<?php echo $view_type; ?>"></input>

			<fieldset>
			<table id="edit_entity_table">
				<thead>
					<tr>
						<th><?php echo elgg_echo('entity_browser:edit:property:name'); ?></th>
						<th><?php echo elgg_echo('entity_browser:edit:property:value'); ?></th>
						<th><?php echo elgg_echo('entity_browser:edit:property:prompt:remove'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
						foreach ($all_properties as $property) {
					?>
						<tr>
							<td><?php echo $property; ?></td>
							<td>
							<?php if (in_array($property, $non_editables)) { ?>
								<?php echo is_array($entity->$property) ? htmlspecialchars(implode(', ', $entity->$property)) : htmlspecialchars($entity->$property); ?>
							<?php } else { ?>
								<input type="text" name="property:<?php echo $property; ?>" value="<?php echo is_array($entity->$property) ? htmlspecialchars(implode(', ', $entity->$property)) : htmlspecialchars($entity->$property); ?>"></input>
							<?php } ?>
							</td>
							<td>
								<?php if (in_array($property, $meta_names)) { ?>
									<button class="remove_property" type="button" name="remove"><?php echo elgg_echo('entity_browser:edit:property:remove'); ?></button>
								<?php } ?>
							</td>
						</tr>
					<?php 
						} 
					?>
				</tbody>
			</table>
		</fieldset>
		<div><button type="button" name="add_property" id="add_property"><?php echo elgg_echo('entity_browser:edit:property:add'); ?></button></div>
		<button type="button" name="submit" id="submit"><?php echo elgg_echo('save'); ?></button>
		<button type="button" name="cancel" id="cancel"><?php echo elgg_echo('cancel'); ?></button>
	</form>
</div>
<div id="entity_loader" style="display: none;">
	<br />
	<br />
	<center>
		<img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif">
	</center>
</div>


<script type="text/javascript">
	$('#add_property').click(function() {
		$('#edit_entity_table > tbody:last').append('<tr><td><input name="new_meta_names[]" type="text"></td><td><input name="new_meta_values[]" type="text"></td><td><button class="remove_property" type="button" name="remove">Remove</button></td></tr>');
	});

	$('.remove_property').live('click', function() {
		$(this).parents('tr').remove();
	});

	$('button#cancel').click(function() {
		$('#entity_content').hide();
		$('#entity_loader').show();
		$('#edit_entity').attr('action','<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/getentities.php');
		$('#edit_entity').ajaxSubmit(function(data) {
			$('#entity_container').html(data);
		});
	});

	$('button#submit').click(function() {
		$('#entity_content').hide();
		$('#entity_loader').show();
		$('#edit_entity').ajaxSubmit(function(data) {
			$('#entity_loader').html(data);
			$('#edit_entity').attr('action','<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/getentities.php');
			$('#edit_entity').ajaxSubmit(function(data) {
				$('#entity_container').html(data);
			});
		});
	});
	
</script>

<?php 
	}
?>