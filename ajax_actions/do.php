<?php 

	include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php";
	admin_gatekeeper();

	$ENTITY_SHOW_HIDDEN_OVERRIDE = true;
	
	$entity_guids = get_input('selected_guids');
	$action = get_input('selected_action');

	$message = null;
	if (!in_array($action, array('enable', 'disable', 'touch', 'delete'))) {
		$message = 'Unrecognized action: ' . $action;
	} else {
		if (is_array($entity_guids)) {
			$entities = array();
			foreach($entity_guids as $entity_guid) {
				if ($entity = get_entity($entity_guid)) {
					$entities[] = $entity;
				} else {
					$message = 'Couldn\'t find entity (guid:' . $entity_guid . ')';
					break;
				}
			}
			if (!$message) {
				foreach ($entities as $entity) {
					switch ($action) {
						case 'enable':
							$entity->enable();
							break;
						case 'disable':
							$entity->disable();
							break;
						case 'touch':
							$entity->time_updated = time();
							$entity->save();
							break;
						case 'delete':
							$entity->delete();
							break;
					}
				}
			}
		} else {
			$message = 'The requested action could not interpret the list of target entities';
		}
	}
	
	if (!$message) {
		$message = 'Requested action successfully performed on selected entities.';
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
