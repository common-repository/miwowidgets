<div style="height: 20px;"></div>
<table>
    <tr>
        <td class="left-td"><?php echo $text_show_widget; ?></td>
        <td>
            <input type="radio" value="1" name="show_in_time_range" <?php echo $time_yes; ?>><label for="show"><?php echo $text_in; ?></label>
            <input type="radio" value="0" name="show_in_time_range" <?php echo $time_no; ?>><label for="no"><?php echo $text_except; ?></label>
        </td>
    </tr>
    <tr>
        <td class="left-td"><label for="time_start"><?php echo $text_from; ?></label></td>
        <td><input class="time_input" type="text" id="time_start" value="<?php echo $time_start; ?>" ></td>
    </tr>
    <tr>
        <td class="left-td"><label for="time_end"><?php echo $text_to; ?></label></td>
        <td><input class="time_input" type="text" id="time_end" value="<?php echo $time_end; ?>" ></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <a class="button button-primary" style="float: left" href="javascript:;" onclick="saveTimeRule('<?php echo $module; ?>', '<?php echo $widget_id; ?>');"><?php echo $button_text_save; ?></a>
            <a class="button" style="float: left; margin-left: 10px" href="javascript:;" onclick="resetRule('<?php echo $module; ?>', '<?php echo $widget_id; ?>', 'time');"><?php echo $button_text_reset; ?></a>
            <div style="margin-top: 5px; margin-left: 20px" id="time_message"></div>
        </td>
    </tr>
</table>

<script type="text/javascript">
    jQuery("#time_start").datetimepicker({ datepicker:false, format:'H:i', closeOnDateSelect: true, mask:true });
    jQuery("#time_end").datetimepicker({ datepicker:false, format:'H:i', closeOnDateSelect: true, mask:true });
</script>