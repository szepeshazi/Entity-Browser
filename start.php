<?php

	/**
	 * Elgg entity browser
	 * 
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Andras Szepeshazi
	 * @copyright Andras Szepeshazi
	 * @link http://wamped.org
	 */


    global $CONFIG;

	/**
	 * Plugin initialisation
	 */
    function entity_browser_init() {

		// Admin menu
    	register_elgg_event_handler('pagesetup','system','entity_browser_adminmenu');
    	
	}
	
    /**
     * Sets up the admin menu.
     */ 
    function entity_browser_adminmenu() {
        if (get_context() == 'admin' && isadminloggedin()) {
            add_submenu_item(elgg_echo('entity_browser:admin:title'), "/mod/entity_browser/admin.php");
        }
    }
    
    
	// Initialise entity browser plugin
	register_elgg_event_handler('init','system','entity_browser_init');

?>