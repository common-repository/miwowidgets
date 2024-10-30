<div style="height: 20px;"></div>
<table>
    <tr>
        <td class="left-td"><?php echo $text_show_widget; ?></td>
        <td>
            <input type="radio" value="1" name="show_in_ip_range" <?php echo $ip_yes; ?>><label for="show"><?php echo $text_in; ?></label>
            <input type="radio" value="0" name="show_in_ip_range" <?php echo $ip_no; ?>><label for="no"><?php echo $text_except; ?></label>
        </td>
    </tr>
    <tr>
        <td class="left-td"><label for="ips"><?php echo $text_ip_list; ?></label></td>
        <td>
            <textarea style="width: 500px; height: 150px" class="ip_input"  id="text_ips"><?php echo $ips; ?></textarea><br/>
            <span><?php echo $text_note; ?></span>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <a class="button button-primary" style="float: left" href="javascript:;" onclick="saveIPRule('<?php echo $module; ?>', '<?php echo $widget_id; ?>');"><?php echo $button_text_save; ?></a>
            <a class="button" style="float: left; margin-left: 10px" href="javascript:;" onclick="resetRule('<?php echo $module; ?>', '<?php echo $widget_id; ?>', 'ip');"><?php echo $button_text_reset; ?></a>
            <div style="margin-top: 5px; margin-left: 20px" id="<?php echo $module; ?>_message"></div>
        </td>
    </tr>
</table>