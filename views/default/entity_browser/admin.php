<script type="text/javascript">
jQuery(document).ready(function () {
	
	var entity_tree = $("#entity_tree")
		.jstree({ 
			"plugins" : [ "themes", "json_data", "ui"],
			"json_data" : { 
				"ajax" : {
					"url" : "<?php echo $vars['url']; ?>mod/entity_browser/ajax_actions/getnodes.php",
					"data" : function (n) { 
						return { 
							"operation" : "get_children", 
							"id" : n.attr ? n.attr("id").replace("node_","") : 'entity'
						}; 
					}
				}
			},
			"ui" : {
				"initially_select" : [ "entity" ]
			},
			"core" : { 
				"initially_open" : [ "entity"] 
			}
		});

		var outer_layout = $('#layout_container').layout({ applyDefaultStyles: true });
		var loading = '<br /><br /><center><img src="<?php echo $vars['url']; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif"></center>';

		$("div#entity_tree a").live("click", function(e) {
			var entitiesUrl = '<?php echo $vars['url']; ?>mod/entity_browser/ajax_actions/getentities.php?type=' + $(this).parent('li').attr('id');
			var optionsUrl = '<?php echo $vars['url']; ?>mod/entity_browser/ajax_actions/getoptions.php?type=' + $(this).parent('li').attr('id');
			$('#entity_container').html(loading);
			$('#options_container').html(loading);
			$('#entity_container').load(entitiesUrl);
			$('#options_container').load(optionsUrl, function() {
			});
		});

		$('#entity_container').html(loading);
		$('#options_container').html(loading);
		var initialEntities = '<?php echo $vars['url']; ?>mod/entity_browser/ajax_actions/getentities.php?type=entity';
		var initialOptions = '<?php echo $vars['url']; ?>mod/entity_browser/ajax_actions/getoptions.php?type=entity';
		$('#entity_container').load(initialEntities);
		$('#options_container').load(initialOptions);
			
});
</script>

<div id="layout_container" style="height:600px">
	<div class="ui-layout-center">
		<div id="entity_container">
		</div>
	</div>
	<div class="ui-layout-west">
		<div class="ui-layout-center-west">
			<div id="entity_tree">
			</div>
		</div>
	</div>
	<div class="ui-layout-east">
		<div id="options_container">
		</div>
	</div>
</div>


