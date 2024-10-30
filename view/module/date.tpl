<div style="height: 20px;"></div>
<table>
    <tr>
        <td class="left-td"><?php echo $text_show_widget; ?></td>
        <td>
            <input type="radio" value="1" name="show_in_date_range" <?php echo $date_yes; ?>><label for="show"><?php echo $text_in; ?></label>
            <input type="radio" value="0" name="show_in_date_range" <?php echo $date_no; ?>><label for="no"><?php echo $text_except; ?></label>
        </td>
    </tr>
    <tr>
        <td class="left-td"><label for="date_start"><?php echo $text_from; ?></label></td>
        <td><input class="date_input" type="text" id="date_start" value="<?php echo $date_start; ?>" readonly></td>
    </tr>
    <tr>
        <td class="left-td"><label for="date_end"><?php echo $text_to; ?></label></td>
        <td><input class="date_input" type="text" id="date_end" value="<?php echo $date_end; ?>" readonly></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <a class="button button-primary" style="float: left" href="javascript:;" onclick="saveDateRule('<?php echo $module; ?>', '<?php echo $widget_id; ?>');"><?php echo $button_text_save; ?></a>
            <a class="button" style="float: left; margin-left: 10px" href="javascript:;" onclick="resetRule('<?php echo $module; ?>', '<?php echo $widget_id; ?>', 'date');"><?php echo $button_text_reset; ?></a>
            <div style="margin-top: 5px; margin-left: 20px" id="date_message"></div>
        </td>
    </tr>
</table>

<script type="text/javascript">
    //jQuery("#date_start").datetimepicker({ timepicker:false, format:'Y/m/d' });
    //jQuery("#date_end").datetimepicker({ timepicker:false, format:'Y/m/d' });
    jQuery(function(){
        jQuery('#date_start').datetimepicker({
            format:'Y/m/d',
            onShow:function( ct ){
                this.setOptions({
                    maxDate:jQuery('#date_end').val()?jQuery('#date_end').val():false
                })
            },
            timepicker:false,
            closeOnDateSelect: true
        });

        jQuery('#date_end').datetimepicker({
            format:'Y/m/d',
            onShow:function( ct ){
                this.setOptions({
                    minDate:jQuery('#date_start').val()?jQuery('#date_start').val():false
                })
            },
            timepicker:false,
            closeOnDateSelect: true
        });
    });
</script>