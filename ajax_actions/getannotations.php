<?php
    include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php";
    admin_gatekeeper();

    $ENTITY_SHOW_HIDDEN_OVERRIDE = true;

    $entity_guids = get_input('selected_guids');

    $message = null;
    if (is_array($entity_guids) && !empty($entity_guids)) {
        $annotations = array();
        foreach($entity_guids as $entity_guid) {
            $annotations[] = get_annotations($entity_guid);
        }
    }

    echo print_r($annotations, 1);
?>