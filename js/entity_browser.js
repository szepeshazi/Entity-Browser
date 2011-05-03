jQuery(document).ready(function () {

    var eBrowser = (function() {

        var outer_layout = null;
        var current_extras = null;
        var properties_tree;
        var loading = '<br /><br /><center><img src="' + wwwroot + 'mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif"></center>';
        var tip_loading = '<div id="entity_info_div" style="width: 160px; height: 80px;"><center><br /><br /><img src="' + wwwroot + 'mod/entity_browser/js/jquery.jstree/themes/classic/throbber.gif"></center></div>';

        var options = {
            entities: {
                type: 'entity',
                offset: 0,
                limit: 10,
                sort: 'time_created',
                direction: 'desc',
                displayed_properties: [],
                filters: [],
                selected_guids: [],
                view_type: 'raw'
            },
            annotations: {
                offset: 0,
                limit: 10,
                sort: 'id',
                direction: 'desc',
                displayed_properties: [],
                view_type: 'raw'
            },
            relationships: {
                offset: 0,
                limit: 10,
                sort: 'time_created',
                direction: 'desc',
                displayed_properties: [],
                view_type: 'raw'
            },
            river: {
                offset: 0,
                limit: 10,
                sort: 'posted',
                direction: 'asc',
                displayed_properties: [],
                view_type: 'raw'
            }
        };

        var _load_options = function() {
            $.ajax({
                url: wwwroot + 'mod/entity_browser/ajax_actions/load_options.php',
                success: function(data, textStatus, request) {
                    this.options = data;
                }
            });
        };

        var _save_options = function() {
            $.ajax({
                url: wwwroot + 'mod/entity_browser/ajax_actions/save_options.php',
                data: {options: options}
            });
        }

        var _load_entities = function() {
            $('div#entity_content').hide();
            $('div#entity_loader').show();
            $('form#view_entities').ajaxSubmit(function(data) {
                $('div#entity_container').html(data);
                _load_entities_callback();
            });
        }

        var _bind_pagination = function() {
            $('a.pagination_number, a.pagination_previous, a.pagination_next').live("click", function() {
                var targetUrl = $(this).attr('href');
                var regxp = /offset=(\d+)/;
                var matches = regxp.exec(targetUrl);
                var offset = matches[1];
                $('input#offset').val(offset);
                options.entities.offset = offset;
                _load_entities();
                return false;
            });
        }

        var _bind_sorting = function() {
            // Handle column sorting. Sort property and direction will be saved into hidden input form variables
            $('a.sort').live("click", function() {
                var target = $(this).attr('id');
                var target_parts = target.split(':');
                var sort = target_parts[1];
                var direction = target_parts[2];
                $('#sort').val(sort);
                $('#direction').val(direction);
                options.entities.sort = sort;
                options.entities.direction = direction;
                _load_entities();
                return false;
            });
        }

        var _bind_selection = function() {
            // Toggle selection of all entities in the table
            $('#select_all_guids').live("click", function() {
                $('.guid_selector').attr('checked', $(this).attr('checked'));
                options.entities.selected_guids = [];
                $('#view_entities input[type="checkbox"]:checked').each(function() {
                    options.entities.selected_guids.push($(this).val());
                });
                _toggle_action_trigger();
            });
            $('#selected_action').live("click", function() {
                _toggle_action_trigger();
            });
            $('.guid_selector').live("click", function() {
                options.entities.selected_guids = [];
                $('#view_entities input[type="checkbox"]:checked').each(function() {
                    options.entities.selected_guids.push($(this).val());
                });
                _toggle_action_trigger();
            });

        }

        var _toggle_action_trigger = function() {
            // Enable the action button if a) an action type was selected and b) at least one entity was selected to perform the action on
            var enable_action_trigger = $('#selected_action option:selected"').val() != 0;
            enable_action_trigger = enable_action_trigger && ($('.guid_selector:checked').length > 0);
            if (enable_action_trigger) {
                $('#action_trigger').removeAttr('disabled');
            } else {
                $('#action_trigger').attr('disabled', 'disabled');
            }
        };

        var _bind_action_trigger = function() {
            $('#action_trigger').live("click", function() {
                $('#offset').val(0);
                $('#entity_content').hide();
                $('#entity_loader').show();
                $('#view_entities').attr('action', wwwroot + 'mod/entity_browser/ajax_actions/do.php');
                $('#view_entities').ajaxSubmit(function(data) {
                    $('#entity_loader').html(data);
                    $('#view_entities').attr('action', wwwroot + 'mod/entity_browser/ajax_actions/getentities.php');
                    $('#view_entities').ajaxSubmit(function(data) {
                        $('#entity_loader').hide();
                        $('#entity_content').show();
                        $('#entity_container').html(data);
                    });
                });
                return false;
            });
        };

        var _bind_filters = function() {
            $('#apply_filters').live("click", function() {
                $('#offset').val(0);
                _load_entities();
                return false;
            });
        };

        var _bind_edit_links = function() {
            $('a.edit_link').live("click", function() {
                var targetUrl = $(this).attr('href');
                var regxp = /guid=(\d+)/;
                var matches = regxp.exec(targetUrl);
                $('#action_guid').val(matches[1]);
                $('#view_entities').attr('action', wwwroot + 'mod/entity_browser/edit.php');
                _load_entities();
                return false;
            });
        };

        var _get_time_info = function(element, callback) {

        };

        var _get_entity_info = function(element, callback) {
            $.ajax({
                url: wwwroot + 'mod/entity_browser/ajax_actions/entity_info.php?guid=' + parseInt(element.html()),
                global: false,
                success: function(data, textStatus, request) {
                    callback(data);
                    return true;
                }
            });
            return tip_loading;
        };

        var _get_property_info = function(element, callback) {
            var this_row = element.parents('tr');
            var guid = $(this_row).find('input.guid_selector').val();
            var cell_index =  $(this_row).find('td').index(element);
            var property_cell = $(this_row).parents('table').find('thead th:nth-child(' + (cell_index + 1) +')');
            var property = $(property_cell).find('a.sort').html();
            if (!property) property = $(property_cell).find('span').html();
            $.ajax({
                url: wwwroot + 'mod/entity_browser/ajax_actions/property_info.php?guid=' + guid + '&property=' + property,
                global: false,
                success: function(data, textStatus, request) {
                    callback(data);
                    return true;
                }
            });
            return tip_loading;
        };

        var _bind_info_tips = function() {
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
                        return _get_entity_info($(this), updateCallback);
                    } else if ($(this).hasClass('truncated')) {
                        return _get_property_info($(this), updateCallback);
                    }
                }
            });
        };

        var _bind_info_types = function() {
            $('a.info_type').live("click", function() {
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
                $(this).find('img').attr('src', wwwroot + 'mod/entity_browser/_graphics/' + icons[index] + '.png');
            });
        };

        var _bind_toggle_extras = function() {
            $('.view_toggle').live("click", function() {
                var id = $(this).attr('id');
                if (id == current_extras) {
                    outer_layout.toggle('south');
                } else {
                    outer_layout.open('south');
                    current_extras = id;
                }
                if (!outer_layout.state.south.isClosed) {
                    var selected_guids = [];
                    $('#view_entities input[type="checkbox"]:checked').each(function() {
                        selected_guids.push($(this).val());
                    });
                    $('#extras_content').hide();
                    $('#extras_loader').show();
                    outer_layout.sizePane('south', 60);
                    $.ajax({
                        url: wwwroot + 'mod/entity_browser/ajax_actions/get' + id + '.php',
                        data: {selected_guids: selected_guids},
                        global: false,
                        success: function(data, textStatus, request) {
                            $('#extras_loader').hide();
                            $('#extras_content').html(data);
                            $('#extras_content').show();
                            outer_layout.sizePane('south', 180);
                        }
                    });
                }
            });
        };

        var _bind_load_options = function() {
            $('form#view_options').submit(function() {
                $('#options_content').hide();
                $('#options_loader').show();
                $(this).ajaxSubmit(function(data) {
                    $('#options_container').html(data);
                    $('#entity_container').html(loading);
                    $('#view_options').attr('action', wwwroot + 'mod/entity_browser/ajax_actions/getentities.php');
                    $('#view_options').ajaxSubmit(function(data) {
                        $('#entity_container').html(data);
                        $('#view_options').attr('action', wwwroot + 'mod/entity_browser/ajax_actions/getoptions.php');
                    });
                });
                return false;
            });
        };

        var _load_entities_callback = function() {
            // Trigger filter form submission when enter was pressed in one of the filter input fields
            $('input.filter').bind('keypress', function(e) {
                if ((e.keyCode || e.which) == 13) {
                    _load_entities();
                    return false;
                }
            });
            _bind_info_tips();
        };

        var _load_options_callback = function() {
            _bind_load_options();
        };

        var _init_entity_tree = function() {
            // Create jstree for entities
            var entity_tree = $("#entity_tree").jstree({
                "plugins" : [ "themes", "json_data", "ui"],
                "json_data" : {
                    "ajax" : {
                        "url" : wwwroot + "mod/entity_browser/ajax_actions/getnodes.php",
                        "data" : function (n) {
                            return {
                                "operation" : "get_children",
                                "id" : n.attr ? n.attr("id").replace("node_","") : 'entity'
                            };
                        },
                    }
                },
                "ui" : {
                    "initially_select" : [ "entity" ]
                },
                "core" : {
                    "initially_open" : [ "entity"]
                }
            });

            //  Bind entity tree clicks to refresh other panes
            $("div#entity_tree a").live("click", function(e) {
                options.entities.type = $(this).parent('li').attr('id');
                var entitiesUrl = wwwroot + 'mod/entity_browser/ajax_actions/getentities.php?type=' + options.entities.type;
                var optionsUrl = wwwroot + 'mod/entity_browser/ajax_actions/getoptions.php?type=' + options.entities.type;
                $('#entity_container').html(loading);
                $('#options_container').html(loading);
                console.debug('refreshing js tree', options.entities);
                $("#properties_tree").jstree("refresh");
                $('#entity_container').load(entitiesUrl, null, _load_entities_callback);
                $('#options_container').load(optionsUrl, null, _load_options_callback);
            });

        };

        var _init_properties_tree = function() {
            // Create jstree for entity properties
            properties_tree = $("#properties_tree").jstree({
                "plugins" : [ "themes", "json_data", "ui", "checkbox"],
                "json_data" : {
                    "ajax" : {
                        "url" : wwwroot + "mod/entity_browser/ajax_actions/getproperties.php",
                        "data" : function() {
                            return options.entities;
                        }
                    }
                },
                "ui" : {
                    "initially_select" : [ "properties" ]
                },
                "core" : {
                    "initially_open" : [ "properties"]
                }
            });
        };

        var _bind_all_on_init = function() {
            _bind_pagination();
            _bind_sorting();
            _bind_selection();
            _bind_action_trigger();
            _bind_filters();
            _bind_edit_links();
            _bind_info_types();
            _bind_toggle_extras();
        };

        var _init = function() {
            // Initialize jstrees
            _init_properties_tree();
            _init_entity_tree();

            // Create layout and hide south pane by default
            outer_layout = $('#layout_container').layout({ applyDefaultStyles: true });
            outer_layout.close('south');

            // Load default content for entities and options containers
            $('#entity_container').html(loading);
            $('#options_container').html(loading);
            var initialEntities = wwwroot + 'mod/entity_browser/ajax_actions/getentities.php?type=entity';
            var initialOptions = wwwroot + 'mod/entity_browser/ajax_actions/getoptions.php?type=entity';
            $('#entity_container').load(initialEntities, null, _load_entities_callback);
            $('#options_container').load(initialOptions, null, _load_options_callback);

            // Bind all events to handle pagination, sorting, filtering and miscellaneous ajax actions
            _bind_all_on_init();
        };

        return {
            init: _init
        };

    })();

    eBrowser.init();
});
