function getPopup(a, title) {
    var _widget_id = getWidgetId(a);

    if (_widget_id == '') {
        return;
    }

    var href = 'admin-ajax.php?action=miwowidgets_ajax&route=dialog/dialog&widget_id=' + _widget_id;
    jQuery.post(href, function(data) {
        var popup = jQuery(document.createElement('div'));
        jQuery(popup).html(data);

        jQuery(popup).dialog({
            modal: true,
            title: title,
            resizable : false,
            width: '80%',
            dialogClass: 'miwowidgets-dialog',
            open: function( event, ui ) {
                jQuery('.ui-dialog-content').addClass('miwowidget-dialog');

                getWidgetButtons(_widget_id);
            },
            close: function(event, ui) {
                jQuery(this).remove();
            }
        });
    });
}

function getData(module, widget_id, head_click) {
    var html = jQuery('#miwowidgets_'+ module).html()

    jQuery('.miwowidget-inside').removeClass('display-block');
    jQuery('#miwowidget-inside-'+ module).addClass('display-block');

    jQuery('.miwowidget-top').removeClass('miwowidget-active');
    jQuery('#miwowidget-tab-'+ module).addClass('miwowidget-active');

    if(head_click == true && html != ''){
        return;
    }

    var filter_select_val,filter_order_val,filter_title_val,item_count_val = '';
    var filter_select = document.getElementById("miwowidgets_show_field_" + module);
    var filter_order =  document.getElementById("miwowidgets_order_field_" + module);
    var filter_title =  document.getElementById("miwowidgets_search_field_" + module);
    var item_count =  document.getElementById("miwowidgets_item_count_" + module);

    if (filter_select) {
        filter_select_val = filter_select.value;
    }

    if (filter_order) {
        filter_order_val = filter_order.value;
    }

    if (filter_title) {
        filter_title_val = filter_title.value;
    }

    if (item_count) {
        item_count_val = item_count.value;
    }


    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module,
        data: { task: 'loaddata', module: module, widget_id: widget_id, filter_select: filter_select_val, filter_order: filter_order_val, filter_title: filter_title_val, item_count: item_count_val },
        beforeSend : function(){
            jQuery('#miwowidgets_'+ module).html('<div class="loading"></div>');
        },
        success : function(html){
            jQuery('#miwowidgets_'+ module).html(html);
        }
    });
}

function paginate(page, module, widget_id){
    var filter_select_val,filter_order_val,filter_title_val = '', item_count_val = 10;
    var filter_select = document.getElementById("miwowidgets_show_field_" + module);
    var filter_order =  document.getElementById("miwowidgets_order_field_" + module);
    var filter_title =  document.getElementById("miwowidgets_search_field_" + module);
    var item_count =  document.getElementById("miwowidgets_item_count_" + module);

    if (filter_select) {
        filter_select_val = filter_select.value;
    }

    if (filter_order) {
        filter_order_val = filter_order.value;
    }

    if (filter_title) {
        filter_title_val = filter_title.value;
    }

    if (item_count) {
        item_count_val = item_count.value;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module,
        data: { task: 'loaddata', module: module, widget_id: widget_id, filter_select: filter_select_val, filter_order: filter_order_val, filter_title: filter_title_val, paged:page, item_count: item_count_val },
        beforeSend : function(){
            jQuery('#miwowidgets_'+ module).html('<div class="loading"></div>');
        },
        success : function(html){
            jQuery('#miwowidgets_'+ module).html(html);
        }
    });
}

function resetFilters(module, widget_id){
    document.getElementById("miwowidgets_show_field_" + module).value = 'all';
    document.getElementById("miwowidgets_order_field_" + module).value = 'a_z';
    document.getElementById("miwowidgets_search_field_" + module).value = '';

    getData(module, widget_id, false);
}

function saveCheckboxRule(module, post_id, widget_id){
    var current_state = jQuery('#cb_' + module + '_' + post_id).attr('class');
    var next_state = _getNextState(current_state);
    var state = 1;

    if (current_state == 'loading_16') {
        return;
    }

    if (next_state == 'unchecked') {
         state = 0;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=saverule',
        data: { status: state, module: module, widget_id: widget_id, value: post_id },
        dataType: 'json',
        beforeSend : function(){
            jQuery('#cb_'+ module + '_' + post_id).removeClass(current_state);
            jQuery('#cb_'+ module + '_' + post_id).addClass('loading_16');
        },
        success : function(json){
            if (json['error']) {
                alert(json['error']);
                jQuery('#cb_'+ module + '_' + post_id).removeClass('loading_16');
                jQuery('#cb_'+ module + '_' + post_id).addClass(current_state);
                return;
            }

            jQuery('#cb_'+ module + '_' + post_id).removeClass('loading_16');
            jQuery('#cb_'+ module + '_' + post_id).addClass(next_state);
        }
    });
}

function saveAllCheckboxRule(module, widget_id){
    var current_state = jQuery('#cb_' + module + '_all').attr('class');
    var next_state = _getNextState(current_state);
    var state = 1;

    if (current_state == 'loading_16') {
        return;
    }

    if (next_state == 'unchecked') {
         state = 0;
    }

    var ids = {};

    jQuery("div[name='cb_"+module+"']").each( function(key, cb){
        ids[key] = jQuery(cb).attr('value');
    });

    if (ids.length == 0) {
        return;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=savemultirule',
        data: { status: state, module: module, widget_id: widget_id, value: ids },
        dataType: 'json',
        beforeSend : function(){
            jQuery('#cb_' + module + '_all').removeClass(current_state);
            jQuery('#cb_' + module + '_all').addClass('loading_16');
        },
        success : function(json){
            if (json['error']) {
                alert(json['error']);
                jQuery('#cb_' + module + '_all').removeClass('loading_16');
                jQuery('#cb_' + module + '_all').addClass(current_state);
                return;
            }

            jQuery('#cb_' + module + '_all').removeClass('loading_16');
            jQuery('#cb_' + module + '_all').addClass(next_state);

            jQuery("div[name='cb_"+module+"']").each( function(key, cb){
                jQuery(cb).removeClass(current_state);
                jQuery(cb).addClass(next_state);
            });
        }
    });
}

function saveRadioRule(module, widget_id, radio){
    var status;

    if (radio.value == '1') {
        status = 1
    }
    else if (radio.value == '0') {
        status = 0;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=saverule',
        data: { status: status, module: module, widget_id: widget_id, value: 'no-need'},
        dataType: 'json',
        beforeSend : function(){
            jQuery("#"+module+"_message").html('');
            jQuery("#"+module+"_message").addClass('loading_16');
        },
        success : function(json){
            var result = _resultAction(module, json);

            if(result == false) {
                return;
            }
        }
    });
}

function saveTimeRule(module, widget_id){
    var start   = document.getElementById("time_start");
    var end     = document.getElementById("time_end");
    var state  = jQuery("input[name='show_in_time_range']:checked");

    if (state.length == 0) {
        jQuery("#time_message").html('<span style="color: red;">Please, select Show/Not Show</span>');
        return false;
    }

    if (start == null) {
        jQuery("#time_message").html('<span style="color: red;">Please, select start time</span>');
        return false;
    }

    if (end == null) {
        jQuery("#time_message").html('<span style="color: red;">Please, select end time</span>');
        return false;
    }

    var value = '{"start":"' + start.value + '", "end":"' + end.value + '"}';

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=saverule',
        data: { status: state.val(), module: module, widget_id: widget_id, value: value },
        dataType: 'json',
        beforeSend : function(){
            jQuery("#time_message").html('');
            jQuery("#time_message").addClass('loading_16');
        },
        success : function(json){
            var result = _resultAction(module, json);

            if(result == false) {
                return;
            }
        }
    });
}

function saveDateRule(module, widget_id){
    var start   = document.getElementById("date_start");
    var end     = document.getElementById("date_end");
    var state  = jQuery("input[name='show_in_date_range']:checked");

    if (state.length == 0) {
        jQuery("#date_message").html('<span style="color: red;">Please, select Show/Not Show</span>');
        return false;
    }

    if (start == null) {
        jQuery("#date_message").html('<span style="color: red;">Please, select start date</span>');
        return false;
    }

    if (end == null) {
        jQuery("#date_message").html('<span style="color: red;">Please, select end date</span>');
        return false;
    }

    var value = '{"start":"' + start.value + '", "end":"' + end.value + '"}';

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=saverule',
        data: { status: state.val(), module: module, widget_id: widget_id, value: value },
        dataType: 'json',
        beforeSend : function(){
            jQuery("#date_message").html('');
            jQuery("#date_message").addClass('loading_16');
        },
        success : function(json){
            var result = _resultAction(module, json);

            if(result == false) {
                return;
            }
        }
    });
}

function saveIPRule(module, widget_id){
    var ips   = document.getElementById("text_ips");
    var state  = jQuery("input[name='show_in_ip_range']:checked");

    if (state.length == 0) {
        jQuery("#ip_message").html('<span style="color: red;">Please, select In IPs/Except IPs</span>');
        return false;
    }

    if (ips == null) {
        jQuery("#ip_message").html('<span style="color: red;">IP list empty !</span>');
        return false;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=saverule',
        data: { status: state.val(), module: module, widget_id: widget_id, value: ips.value },
        dataType: 'json',
        beforeSend : function(){
            jQuery("#ip_message").html('');
            jQuery("#ip_message").addClass('loading_16');
        },
        success : function(json){
            var result = _resultAction(module, json);

            if(result == false) {
                return;
            }
        }
    });
}

function saveURLRule(module, widget_id){
    var urls   = document.getElementById("text_urls");
    var state  = jQuery("input[name='show_in_url_range']:checked");

    if (state.length == 0) {
        jQuery("#url_message").html('<span style="color: red;">Please, select In URLs/Except URLs</span>');
        return false;
    }

    if (urls == null) {
        jQuery("#url_message").html('<span style="color: red;">URL list empty !</span>');
        return false;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=saverule',
        data: { status: state.val(), module: module, widget_id: widget_id, value: urls.value },
        dataType: 'json',
        beforeSend : function(){
            jQuery("#url_message").html('');
            jQuery("#url_message").addClass('loading_16');
        },
        success : function(json){
            var result = _resultAction(module, json);

            if(result == false) {
                return;
            }
        }
    });
}

function saveCustomphpRule(module, widget_id){
    var code   = document.getElementById("text_customphp");

    if (code == null) {
        jQuery("#url_message").html('<span style="color: red;">Code content empty !</span>');
        return false;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=saverule',
        data: { status: 0, module: module, widget_id: widget_id, value: code.value },
        dataType: 'json',
        beforeSend : function(){
            jQuery("#customphp_message").html('');
            jQuery("#customphp_message").addClass('loading_16');
        },
        success : function(json){
            var result = _resultAction(module, json);

            if(result == false) {
                return;
            }
        }
    });
}

function saveGeoRule(module, widget_id, type){
    var values = jQuery('#geo_'+type).chosen().val();
    var state  = jQuery("input[name='show_in_geo_"+type+"_range']:checked");

    if (state.length == 0) {
        jQuery("#"+module+"_message_"+type).html('<span style="color: red;">Please, select Show/Not Show</span>');
        return false;
    }

    if (values.length < 1) {
        jQuery("#"+module+"_message_"+type).html('<span style="color: red;">Please, select '+ _capitalise(type) +'</span>');
        return false;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=savegeorule',
        data: { status: state.val(), module: module, widget_id: widget_id, type:type, value: values },
        dataType: 'json',
        beforeSend : function(){
            jQuery("#"+module+"_message_"+type).html('');
            jQuery("#"+module+"_message_"+type).addClass('loading_16');
        },
        success : function(json){
            jQuery("#"+module+"_message_" + type).removeClass('loading_16');

            if (json['error']) {
                jQuery("#"+module+"_message_" + type).html('<span style="color: #D01729;">'+ json['error'] +'</span>');
                return false;
            }
            else if(json['success']){
                jQuery("#"+module+"_message_" + type).html('<span style="color: #359d26;">'+ json['success'] +'</span>');
                return true;
            }
        }
    });
}

function resetRule(module, widget_id, type){
    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=resetrule',
        data: { module: module, widget_id: widget_id},
        dataType: 'json',
        beforeSend : function(){
            jQuery("#"+module+"_message").html('');
            jQuery("#"+module+"_message").addClass('loading_16');
        },
        success : function(json){
            var result = _resultAction(module, json);

            if (result == false) {
                return;
            }

            if (type == 'date') {
                jQuery('#date_start').val('');
                jQuery('#date_end').val('');
                jQuery("input[name='show_in_date_range']").each(function(){
                    jQuery(this).attr("checked", false);
                });
            }

            if (type == 'time') {
                jQuery('#time_start').val('');
                jQuery('#time_end').val('');
                jQuery("input[name='show_in_time_range']").each(function(){
                    jQuery(this).attr("checked", false);
                });
            }

            if (type == 'ip') {
                jQuery('#text_ips').val('');
                jQuery("input[name='show_in_ip_range']").each(function(){
                    jQuery(this).attr("checked", false);
                });
            }

            if (type == 'url') {
                jQuery('#text_urls').val('');
                jQuery("input[name='show_in_url_range']").each(function(){
                    jQuery(this).attr("checked", false);
                });
            }

            if (type == 'customphp') {
                jQuery('#text_customphp').val('');
            }
        }
    });
}

function resetGeoRule(module, widget_id, type){
    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=resetrule',
        data: { module: module, widget_id: widget_id, type: type},
        dataType: 'json',
        beforeSend : function(){
            jQuery("#"+module+"_message_"+type).html('');
            jQuery("#"+module+"_message_"+type).addClass('loading_16');
        },
        success : function(json){
            jQuery("#"+module+"_message_" + type).removeClass('loading_16');

            if (json['error']) {
                jQuery("#"+module+"_message_" + type).html('<span style="color: #D01729;">'+ json['error'] +'</span>');
            }
            else if (json['success']){
                jQuery("#"+module+"_message_" + type).html('<span style="color: #359d26;">'+ json['success'] +'</span>');

                jQuery("input[name='show_in_geo_"+type+"_range']").each(function(){
                    jQuery(this).attr("checked", false);
                });

                jQuery("#geo_"+type).val([]);
                jQuery("#geo_"+type).trigger("chosen:updated");
            }
        }
    });
}

function formPost(form_id) {
    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&&route=setting/setting&task=save',
        data: jQuery(form_id).serialize(),
        dataType: 'json',
        beforeSend : function(){
            jQuery("#form_message").html('');
            jQuery("#form_message").addClass('loading_16');
        },
        success : function(json){
            jQuery("#form_message").removeClass('loading_16');

            if (json['error']) {
                jQuery("#form_message").html('<span style="color: #D01729;">'+ json['error'] +'</span>');
            }
            else if (json['success']){
                jQuery("#form_message").html('<span style="color: #359d26;">'+ json['success'] +'</span>');
            }
        }
    });
}

function _resultAction(module, json){
    jQuery("#"+module+"_message").removeClass('loading_16');

    if (json['error']) {
        jQuery("#"+module+"_message").html('<span style="color: #D01729;">'+ json['error'] +'</span>');
        return false;
    }
    else if(json['success']){
        jQuery("#"+module+"_message").html('<span style="color: #359d26;">'+ json['success'] +'</span>');
        return true;
    }
}


function _getNextState(current_state) {
    if(current_state == 'checked') {
        return 'unchecked';
    }
    else {
        return 'checked';
    }
}

function _capitalise(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

function getWidgetId(a) {
    var form = a.parentNode.parentNode.parentNode;
    var input = form.elements.namedItem("widget-id");

    if (input != null) {
        return input.value;
    }

    return '';
}
 /** For Mods with tabs ***/


function getTabsData(module, tabname, widget_id) {
    var modtab = module+ '_' +tabname;

    var filter_select_val,filter_order_val,filter_title_val,item_count_val = '';
    var filter_select = document.getElementById("miwowidgets_show_field_" + modtab);
    var filter_order =  document.getElementById("miwowidgets_order_field_" + modtab);
    var filter_title =  document.getElementById("miwowidgets_search_field_" + modtab);
    var item_count =  document.getElementById("miwowidgets_item_count_" + modtab);

    if (filter_select) {
        filter_select_val = filter_select.value;
    }

    if (filter_order) {
        filter_order_val = filter_order.value;
    }

    if (filter_title) {
        filter_title_val = filter_title.value;
    }

    if (item_count) {
        item_count_val = item_count.value;
    }


    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/'+module,
        data: { task: 'loaddata' + tabname, module: module, widget_id: widget_id, filter_select: filter_select_val, filter_order: filter_order_val, filter_title: filter_title_val, item_count: item_count_val },
        beforeSend : function(){
            jQuery('#miwowidgets_'+ modtab).html('<div class="loading"></div>');
        },
        success : function(html){
            jQuery('#miwowidgets_'+ modtab).html(html);
        }
    });
}

function resetTabsFilters(module, tabname, widget_id){
    var modtab = module+ '_' +tabname;

    document.getElementById("miwowidgets_show_field_" + modtab).value = 'all';
    document.getElementById("miwowidgets_order_field_" + modtab).value = 'a_z';
    document.getElementById("miwowidgets_search_field_" + modtab).value = '';

    getTabsData(module, tabname, widget_id);
}

function saveTabsCheckboxRule(module, tabname, post_id, widget_id){
    var modtab = module+ '_' +tabname;

    var current_state = jQuery('#cb_' + modtab + '_' + post_id).attr('class');
    var next_state = _getNextState(current_state);
    var state = 1;

    if (current_state == 'loading_16') {
        return;
    }

    if (next_state == 'unchecked') {
         state = 0;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=savetabsrule',
        data: { status: state, module: modtab, widget_id: widget_id, value: post_id },
        dataType: 'json',
        beforeSend : function(){
            jQuery('#cb_'+ modtab + '_' + post_id).removeClass(current_state);
            jQuery('#cb_'+ modtab + '_' + post_id).addClass('loading_16');
        },
        success : function(json){
            if (json['error']) {
                alert(json['error']);
                jQuery('#cb_'+ modtab + '_' + post_id).removeClass('loading_16');
                jQuery('#cb_'+ modtab + '_' + post_id).addClass(current_state);
                return;
            }

            jQuery('#cb_'+ modtab + '_' + post_id).removeClass('loading_16');
            jQuery('#cb_'+ modtab + '_' + post_id).addClass(next_state);
        }
    });
}

function saveTabsAllCheckboxRule(module, tabname, widget_id){
    var modtab = module+ '_' +tabname;

    var current_state = jQuery('#cb_' + modtab + '_all').attr('class');
    var next_state = _getNextState(current_state);
    var state = 1;

    if (current_state == 'loading_16') {
        return;
    }

    if (next_state == 'unchecked') {
         state = 0;
    }

    var ids = {};

    jQuery("div[name='cb_"+modtab+"']").each( function(key, cb){
        ids[key] = jQuery(cb).attr('value');
    });

    if (ids.length == 0) {
        return;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module + '&task=savetabsmultirule',
        data: { status: state, module: modtab, widget_id: widget_id, value: ids },
        dataType: 'json',
        beforeSend : function(){
            jQuery('#cb_' + modtab + '_all').removeClass(current_state);
            jQuery('#cb_' + modtab + '_all').addClass('loading_16');
        },
        success : function(json){
            if (json['error']) {
                alert(json['error']);
                jQuery('#cb_' + modtab + '_all').removeClass('loading_16');
                jQuery('#cb_' + modtab + '_all').addClass(current_state);
                return;
            }

            jQuery('#cb_' + modtab + '_all').removeClass('loading_16');
            jQuery('#cb_' + modtab + '_all').addClass(next_state);

            jQuery("div[name='cb_"+modtab+"']").each( function(key, cb){
                jQuery(cb).removeClass(current_state);
                jQuery(cb).addClass(next_state);
            });
        }
    });
}

function paginateTabs(page, module, tabname, widget_id){
    var modtab = module+ '_' +tabname;

    var filter_select_val,filter_order_val,filter_title_val = '', item_count_val = 10;
    var filter_select = document.getElementById("miwowidgets_show_field_" + modtab);
    var filter_order =  document.getElementById("miwowidgets_order_field_" + modtab);
    var filter_title =  document.getElementById("miwowidgets_search_field_" + modtab);
    var item_count =  document.getElementById("miwowidgets_item_count_" + modtab);

    if (filter_select) {
        filter_select_val = filter_select.value;
    }

    if (filter_order) {
        filter_order_val = filter_order.value;
    }

    if (filter_title) {
        filter_title_val = filter_title.value;
    }

    if (item_count) {
        item_count_val = item_count.value;
    }

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=module/' + module,
        data: { task: 'loaddata'+tabname, module: module, widget_id: widget_id, filter_select: filter_select_val, filter_order: filter_order_val, filter_title: filter_title_val, paged:page, item_count: item_count_val },
        beforeSend : function(){
            jQuery('#miwowidgets_'+ modtab).html('<div class="loading"></div>');
        },
        success : function(html){
            jQuery('#miwowidgets_'+ modtab).html(html);
        }
    });
}

function getWidgetButtons(widget_id) {
    var buttons = '<input type="button" value="Quick View" class="button button-primary miwowidget-top-button right" onclick="quickView(\''+widget_id+'\')">' +
                  '<input type="button" value="Copy" class="button button-primary miwowidget-top-button right" onclick="copyView(\''+widget_id+'\')">' +
                  '<input type="button" value="Reset" class="button button-primary miwowidget-top-button right" onclick="resetWidget(\''+widget_id+'\')">' +
                  '<div style="margin-right:10px; margin-top: 10px; float: right; font-size: 12px !important; height: 16px" id="reset_message"></div>';

    jQuery('.miwowidgets-dialog .ui-widget-header').append(buttons);
}

function quickView(widget_id) {
    jQuery('#miwowidget-top').empty();
    jQuery('#miwowidget-top').css('display', 'block');

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=dialog/dialog',
        data: { task: 'quickView', widget_id: widget_id },
        beforeSend : function(){
            jQuery('#miwowidget-top').append('<div class="loading"></div>');
        },
        success : function(html){
            jQuery('#miwowidget-top').html(html);
        }
    });
}

function copyView(widget_id) {
    jQuery('#miwowidget-top').empty();
    jQuery('#miwowidget-top').css('display', 'block');

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=dialog/dialog',
        data: { task: 'copyView', widget_id: widget_id },
        beforeSend : function(){
            jQuery('#miwowidget-top').append('<div class="loading"></div>');
        },
        success : function(html){
            jQuery('#miwowidget-top').html(html);
        }
    });
}

function copy(widget_id) {
    var selected_widgets=jQuery('#widgets').chosen().val()

    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=dialog/dialog',
        data: {task: 'copy', from_widget_id: widget_id, to_widget_ids: selected_widgets},
        dataType: 'json',
        beforeSend: function () {
            jQuery('#date_message_top').empty();
            jQuery('#date_message_top').addClass('loading_16');
        },
        success: function (json) {
            jQuery('#date_message_top').empty();
            jQuery('#date_message_top').removeClass('loading_16');

            if (json['error']) {
                jQuery('#date_message_top').html('<span style="color: #D01729;">'+ json['error'] +'</span>');
                return;
            }
            else if(json['success']){
                jQuery('#date_message_top').html('<span style="color: #359d26;">'+ json['success'] +'</span>');
            }
        }
    });
}

function resetWidget(widget_id) {
    jQuery.ajax({
        type: "POST",
        url: 'admin-ajax.php?action=miwowidgets_ajax&route=dialog/dialog',
        data: {task: 'resetWidget', widget_id: widget_id},
        dataType: 'json',
        beforeSend: function () {
            jQuery('#reset_message').empty();
            jQuery('#reset_message').addClass('loading_16');
        },
        success: function (json) {
            jQuery('#reset_message').empty();
            jQuery('#reset_message').removeClass('loading_16');

            if (json['error']) {
                jQuery('#reset_message').html('<span style="color: #D01729;">'+ json['error'] +'</span>');
                return;
            }
            else if(json['success']){
                jQuery('#reset_message').html('<span style="color: #359d26;">'+ json['success'] +'</span>');
            }
        }
    });
}

function hideTop() {
    jQuery('#miwowidget-top').empty();
    jQuery('#miwowidget-top').css('display', 'none');
}

function moveVisibilityButton(widget) {
    var visibilityButton = widget.find('a.miwowidgets-visibility').first();
    visibilityButton.insertBefore(widget.find('input.widget-control-save'));

    visibilityButton
        .parent()
        .removeClass('widget-control-noform')
        .find('.spinner')
        .remove()
        .css('float', 'left')
        .prependTo(visibilityButton.parent());
}

jQuery(function($) {
    $('.widget').each(function() {
        moveVisibilityButton($(this));
    });

    $(document).on('widget-added', function(e, widget) {
        if (widget.find('div.widget-control-actions a.miwowidgets-visibility').length === 0) {
            moveVisibilityButton(widget);
        }
    } );
});