<?php
/**
 * View a list of entities
 *
 * @package Elgg
 * @author Curverider Ltd <info@elgg.com>
 * @link http://elgg.com/
 *
 */

    global $CONFIG;

    $entities                 = $vars['entities'];
    $count                     = $vars['count'];
    $offset                 = $vars['offset'];
    $limit                     = $vars['limit'];
    $displayed_properties     = $vars['displayed_properties'];
    $view_type                 = $vars['view_type'];
    $sort                     = $vars['sort'];
    $direction                 = $vars['direction'];
    $type                     = $vars['type'];
    $current_type             = $vars['current_type'];
    $entity_properties        = $vars['entity_properties'];
    $core_properties        = $vars['core_properties'];


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

    $extra_views = (array('annotations' => 'Annotations', 'relationships' => 'Relationships', 'river' => 'River entries'));

    // Iterate over fetched entities and gather all their metadata names
    $only_users = true;
    if (is_array($entities) && sizeof($entities) > 0) {
        $meta_names = array();
        foreach ($entities as $entity) {
            if (!($entity instanceof ElggUser)) {
                $only_users = false;
            }
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

    if ($only_users) {
        $actions['ban'] = array('name' => elgg_echo('entity_browser:actions:ban'), 'url' => $action_url . '?action=ban');
        $actions['unban'] = array('name' => elgg_echo('entity_browser:actions:unban'), 'url' => $action_url . '?action=unban');
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
<style>
    th a.sort.active.desc {
        background: url("<?php echo $CONFIG->wwwroot ?>mod/entity_browser/_graphics/down.png") no-repeat;
    }

    th a.sort.active.asc {
        background: url("<?php echo $CONFIG->wwwroot ?>mod/entity_browser/_graphics/up.png") no-repeat;
    }
</style>
<div id="entity_content">
    <?php
        if (is_array($entities) && sizeof($entities) > 0) {
            echo $nav;
        }
    ?>
    <form id="view_entities" name="view_entities" action="<?php echo $CONFIG->wwwroot ?>mod/entity_browser/ajax_actions/getentities.php">
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
                            <a href="#" id="sort:<?php echo $property . ':' . $dir; ?>" class="sort <?php echo $class; ?> <?php echo $direction; ?>"><?php echo $property; ?></a>
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
                        foreach ($displayed_properties as $property) :
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
                    <?php endforeach ?>
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
        <?php foreach($extra_views as $key => $value) : ?>
        <button type="button" name="<?php echo $key; ?>" class="view_toggle" id="<?php echo $key;?>"><?php echo $value; ?></button>
        <?php endforeach; ?>
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
