<?php global $CONFIG; ?>

<script type="text/javascript">
    // Expose some variables for external js files
    var wwwroot = '<?php echo $CONFIG->wwwroot; ?>';
</script>

<div id="layout_container" style="height:700px">
    <div class="ui-layout-center">
        <div id="entity_container">
        </div>
    </div>
    <div class="ui-layout-south">
        <div id="extras_container">
            <div id="extras_content">
            </div>
            <div id="extras_loader" style="display: none;">
                <center>
                    <img src="<?php echo $CONFIG->wwwroot; ?>mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif">
                </center>
            </div>
        </div>
    </div>
    <div class="ui-layout-west">
        <div class="ui-layout-center-west">
            <div id="entity_tree">
            </div>
            <hr />
            <div id="properties_tree">
            </div>
        </div>
    </div>
    <div class="ui-layout-east">
        <div id="options_container">
        </div>
    </div>
</div>


