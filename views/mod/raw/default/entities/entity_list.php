<?php
/**
 * View a list of entities
 *
 * @package Elgg
 * @author Curverider Ltd <info@elgg.com>
 * @link http://elgg.com/
 *
 */

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
	
	
	// Define css classes for filter input fields 
	// (use the smallest possible size depending on the property type to save space in the entity table) 
	$small_input = array('guid', 'type', 'subtype', 'owner_guid', 'site_guid', 'container_guid', 'access_id', 'enabled');
	$medium_input = array('time_created', 'time_updated', 'last_action');

	// Icon definitions for tooltip type selections
	// (Hovering on property values can pop up different type of info boxes)
	$info_icons = array('no_info' => 'question', 'entity_info' => 'globe', 'time_info' => 'clock');
	// Info type definitions for general entity properties
	$default_infos = array(
		'info_type:guid' => 'entity_info', 
		'info_type:owner_guid' => 'entity_info', 
		'info_type:container_guid' => 'entity_info',
		'info_type:time_created' => 'time_info',
		'info_type:time_updated' => 'time_info',
		'info_type:last_action' => 'time_info',
	);
	
	$nav .= elgg_view('navigation/pagination',array(
		'baseurl' => $baseurl,
		'offset' => $offset,
		'count' => $count,
		'limit' => $limit,
	));
	
	$action_url = $CONFIG->wwwroot . 'mod/entity_browser/ajax_actions/do.php';
	$actions = array(
		'enable' =>  array('name' => elgg_echo('entity_browser:actions:enable'), 'url' => $action_url . '?action=enable'),
		'disable' =>  array('name' => elgg_echo('entity_browser:actions:disable'), 'url' => $action_url . '?action=disable'),
		'touch' =>  array('name' => elgg_echo('entity_browser:actions:touch'), 'url' => $action_url . '?action=touch'),
		'delete' =>  array('name' => elgg_echo('entity_browser:actions:delete'), 'url' => $action_url . '?action=delete'),
	);

	// Iterate over fetched entities and gather all their metadata names
	if (is_array($entities) && sizeof($entities) > 0) {
		$meta_names = array();
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
		// If no properties were selected to display in the entity table, display all of them (default behavior)
		// (All properties = type independent entity properties + type dependent properties + metadata names)
		if (empty($displayed_properties)) {
			$displayed_properties = array_merge($entity_properties, $core_properties[$type], $meta_names);
		}
	}
	
	// Initialize tooltip types
	// (Hovering on property values can pop up different type of info boxes)
	$info_array = array();
	foreach($displayed_properties as $property) {
		$info_name = 'info_type:' .  $property;
		$info_type = get_input($info_name, null);
		if ($info_type) {
			$info_array[$info_name] = $info_type;
		}
	}
	if (empty($info_array)) {
		$info_array = $default_infos;
	}
	
?>

<div id="entity_content">
	<?php 
		if (is_array($entities) && sizeof($entities) > 0) {
			echo $nav;
		}
	?>
	<form id="view_entities" name="view_entities" action="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/getentities.php">
		<?php echo elgg_view('input/securitytoken'); ?>
		<table cellspacing="0" style="overflow: auto;">
			<thead>
				<tr>
					<th><input type="checkbox" name="select_all_guids" id="select_all_guids"></input></th>
					<?php 
						foreach ($displayed_properties as $property) {
							$dir = 'desc';
							$class = '';
							if ($sort == $property) {
								$class = 'active';
								if ($direction == 'desc') {
									$dir = 'asc';
								}
							}
					?>
						<th>
						<?php if (!in_array($property, $core_properties[$type])) { ?>
							<a href="#" id="sort:<?php echo $property . ':' . $dir; ?>" class="sort <?php echo $class; ?> <?php echo $direction; ?>"><?php echo $property; ?></a>
						<?php } else { ?>
							<span><?php echo $property; ?></span>
						<?php } ?>	  
						<?php $info_type = isset($info_array['info_type:' . $property]) ? $info_array['info_type:' . $property] : 'no_info'; ?>
						<a href="#" class="info_type <?php echo $info_type; ?>"><img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/_graphics/<?php echo  $info_icons[$info_type]; ?>.png" /></a>
						</th>
					<?php 
						} 
					?>
					<th><?php echo elgg_echo('entity_browser:actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
					$entity_counter = 0;	
					foreach ($entities as $entity) { 
				?>
				<tr<?php echo ((($entity_counter++ % 2) == 0) ? ' class="odd"' : ''); ?>>
					<td>
						<input type="checkbox" name="selected_guids[]" value="<?php echo $entity->guid?>" class="guid_selector"></input>
					</td>
					<?php 
						foreach ($displayed_properties as $property) {
							$info_type = isset($info_array['info_type:' . $property]) ? $info_array['info_type:' . $property] : 'no_info'; 
							$property_text = is_array($entity->$property) ? htmlspecialchars(implode(', ', $entity->$property)) : htmlspecialchars($entity->$property);
							if (strlen($property_text) > 25) {
								$property_text = substr($property_text, 0, 25) . '...';
								$td_class = 'infobox ' . $info_type . ' truncated';
							} else {
								$td_class = 'infobox ' . $info_type;
							}
					?>
					<td class="<?php echo $td_class; ?>">
						<?php echo $property_text; ?>
					</td>
					<?php } ?>
					<td>
						<?php
							$edit_url = 'action=edit&guid=' . $entity->guid;
						?>
						<a class="edit_link" href="<?php echo $edit_url; ?>"><?php echo elgg_echo('entity_browser:actions:edit'); ?></a> 
					</td>
				</tr>
				<?php }  ?>
			</tbody>
			<tfoot>
				<tr id="filters">
					<td><?php echo elgg_echo('entity_browser:filters'); ?></td>
					<?php 
						foreach ($displayed_properties as $property) {
							if (!in_array($property, $core_properties[$type])) {
								$filter = get_input('filter:' . $property, '');
								$class = 'filter';
								if (in_array($property, $small_input)) {
									$class .= ' small';
								} else if (in_array($property, $medium_input)) {
									$class .= ' medium';
								}
					?>
						<td>
							<input class="<?php echo $class; ?>" type="text" name="filter:<?php echo $property; ?>" value="<?php echo $filter; ?>"></input>
						</td>
					<?php 
							} else { 
					?>
						<td />
					<?php 
							}
						} 
					?>
					<td>
						<button type="button" name="apply_filters" id="apply_filters"><?php echo elgg_echo('entity_browser:filters:apply'); ?></button>
					</td>
				</tr>
			</tfoot>
		</table>
	
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
		<select name="selected_action" id="selected_action">
		<?php 
			$all_actions = array_merge(array('0' => array('name' => '-----------')), $actions);
			foreach ($all_actions as $key => $value) {?>
			<option value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
		<?php } ?>
		</select>
		<?php echo elgg_echo('entity_browser:label:selected_entities'); ?>
		<button type="button" name="action_trigger" id="action_trigger" disabled="disabled">Go</button>
	</form>
	<?php 
		if (is_array($entities) && sizeof($entities) > 0) {
			echo $nav;
		}
	?>
</div>
<div id="entity_loader" style="display: none;">
	<br />
	<br />
	<center>
		<img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif">
	</center>
</div>

<script type="text/javascript">

	// Handle column sorting. Sort property and direction will be saved into hidden input form variables
	$('a.sort').click(function() {
		$('#entity_content').hide();
		$('#entity_loader').show();
		var target = $(this).attr('id');
		var target_parts = target.split(':');
		$('#sort').val(target_parts[1]);
		$('#direction').val(target_parts[2]);
		$('#view_entities').ajaxSubmit(function(data) {
			$('#entity_loader').hide();
			$('#entity_content').show();
			$('#entity_container').html(data);
		});
	});
	
	// Toggle selection of all entities in the table
	$('#select_all_guids').change(function() {
		$('.guid_selector').attr('checked', $(this).attr('checked'));
		toggleActionTrigger();
	});

	// Load entities matching all entered filters
	$('#apply_filters').click(function() {
		$('#offset').val(0);
		$('#entity_content').hide();
		$('#entity_loader').show();
		$('#view_entities').ajaxSubmit(function(data) {
			$('#entity_loader').hide();
			$('#entity_content').show();
			$('#entity_container').html(data);
		});
	});

	// Trigger filter form submission when enter was pressed in one of the filter input fields
	$('input.filter').bind('keypress', function(e) {
		if ((e.keyCode || e.which) == 13) {
			$('#entity_content').hide();
			$('#entity_loader').show();
			$('#view_entities').ajaxSubmit(function(data) {
				$('#entity_loader').hide();
				$('#entity_content').show();
				$('#entity_container').html(data);
			});
			return false;
		}
	});

	$('#action_trigger').click(function() {
		$('#offset').val(0);
		$('#entity_content').hide();
		$('#entity_loader').show();
		$('#view_entities').attr('action','<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/do.php');
		$('#view_entities').ajaxSubmit(function(data) {
			$('#entity_loader').html(data);
			$('#view_entities').attr('action','<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/ajax_actions/getentities.php');
			$('#view_entities').ajaxSubmit(function(data) {
				$('#entity_loader').hide();
				$('#entity_content').show();
				$('#entity_container').html(data);
			});
		});
	});
	

	$('a.edit_link').click(function() {
		$('#entity_content').hide();
		$('#entity_loader').show();
		var targetUrl = $(this).attr('href');
		var regxp = /guid=(\d+)/;
		var matches = regxp.exec(targetUrl);
		$('#action_guid').val(matches[1]);
		$('#view_entities').attr('action', '<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/edit.php');
		$('#view_entities').ajaxSubmit(function(data) {
			$('#entity_loader').hide();
			$('#entity_content').show();
			$('#entity_container').html(data);
		});
		return false;
	});

	$('a.info_type').click(function() {
		var classes = ['no_info', 'entity_info', 'time_info'];
		var icons = ['question', 'globe', 'clock'];
	    var allCells = $(this).parents('tr').children();
	    var cellIndex = allCells.index($(this).parents('th'));
		var index = 0;
		while (index < classes.length) {
			if ($(this).hasClass(classes[index])) {
				$(this).removeClass(classes[index]);
				$(this).parents('table').find('tbody tr td:nth-child(' + (cellIndex + 1) +')').removeClass(classes[index]);
				break;
			}
			index++;
		}
		if (++index == classes.length) index = 0;
		$(this).addClass(classes[index]);
		var current_property = $(this).prev().html();
		var input_field = $('input#info_type\\:' + current_property);
		input_field.val(classes[index]);
		$(this).parents('table').find('tbody tr td:nth-child(' + (cellIndex + 1) +')').addClass(classes[index]);
		$(this).find('img').attr('src', '<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/_graphics/' + icons[index] + '.png');
		
	});

	
	$('td.infobox').poshytip({
		className: 'tip-green',
		followCursor: true,
		slide: false,
		beforeDisplay: function(element) {
			return $(element).hasClass('time_info') || $(element).hasClass('entity_info') || $(element).hasClass('truncated');
		},
		content: function(updateCallback) {
			if ($(this).hasClass('time_info')) {
				return new Date(parseInt($(this).html()) * 1000) + '';
			} else if ($(this).hasClass('entity_info')) {
				$.ajax({
					url: '/mod/entity_browser/ajax_actions/entity_info.php?guid=' + parseInt($(this).html()),
					global: false,
					success: function(data, textStatus, request) {
						updateCallback(data);
						return true;
					},
				});
				return '<div id="entity_info_div" style="width: 160px; height: 80px;"><center><br /><br /><img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif"></center></div>';
			} else if ($(this).hasClass('truncated')) {
				var this_row = $(this).parents('tr');
				var guid = $(this_row).find('input.guid_selector').val();
				var cell_index =  $(this_row).find('td').index($(this));
				var property_cell = $(this_row).parents('table').find('thead th:nth-child(' + (cell_index + 1) +')');
				var property = $(property_cell).find('a.sort').html();
				if (!property) property = $(property_cell).find('span').html();
				$.ajax({
					url: '/mod/entity_browser/ajax_actions/property_info.php?guid=' + guid + '&property=' + property,
					global: false,
					success: function(data, textStatus, request) {
						updateCallback(data);
						return true;
					},
				});
				return '<div id="entity_info_div" style="width: 160px; height: 80px;"><center><br /><br /><img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif"></center></div>';
			}
		}
	});

	$('#selected_action').change(function() {
		toggleActionTrigger();
	});

	$('.guid_selector').change(function() {
		toggleActionTrigger();
	});

	function toggleActionTrigger() {
		var enable_action_trigger = $('#selected_action option:selected"').val() != 0;
		enable_action_trigger = enable_action_trigger && ($('.guid_selector:checked').length > 0);
		if (enable_action_trigger) {
			$('#action_trigger').removeAttr('disabled');
		} else {
			$('#action_trigger').attr('disabled', 'disabled');
		}
	}
	
	
</script>