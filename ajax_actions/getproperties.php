<?php

    include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php";
    // Prepare views by processing form inputs (current entity type, subtype, limit, offset, sort, displayed properties)
    // and defining some commonly used variables
    include_once dirname(__FILE__) . '/includes/prepare_views.php';

    admin_gatekeeper();

    $ENTITY_SHOW_HIDDEN_OVERRIDE = true;

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

    $properties = array(
        'data' => array('title' => "Properties", 'attr' => array('id' => 'properties')),
        'state' => 'open',
        'children' => array()
    );

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

    foreach ($meta_names as $meta_name) {
        $properties['children'][] = array('data' => array('title' => $meta_name), 'attr' => array('id' => $meta_name));
    }

    header("HTTP/1.0 200 OK");
    header('Content-type: text/json; charset=utf-8');
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Pragma: no-cache");
    echo json_encode($properties);
    die();
?>