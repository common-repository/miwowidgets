<table>
    <tr class="tr-head">
        <td class="checkbox-column">
            <div class="unchecked" id="cb_<?php echo $module; ?>_all" onclick="saveAllCheckboxRule('<?php echo $module; ?>','<?php echo $widget_id; ?>')"></div>
        </td>
        <td class="title-column"><?php echo $text_title; ?></td>
    </tr>

    <?php foreach($devices as $device) { ?>
    <tr class="miwowidgets_row" >
        <td class="checkbox-column" >
            <div class="<?php echo $device->checkstate; ?>" name="cb_<?php echo $module; ?>" value="<?php echo $device->id; ?>" id="cb_device_<?php echo $device->id; ?>" onclick="saveCheckboxRule('<?php echo $module; ?>', '<?php echo $device->id; ?>', '<?php echo $widget_id; ?>')" ></div>
        </td>
        <td><span><?php echo $device->title; ?></span></td>
    </tr>
    <?php } ?>
</table>