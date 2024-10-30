<table>
    <tr class="tr-head">
        <td class="checkbox-column">
            <div class="unchecked" id="cb_<?php echo $module; ?>_all" onclick="saveAllCheckboxRule('<?php echo $module; ?>','<?php echo $widget_id; ?>')"></div>
        </td>
        <td class="title-column"><?php echo $text_title; ?></td>
    </tr>

    <?php foreach($days as $day) { ?>
    <tr class="miwowidgets_row" >
        <td class="checkbox-column" >
            <div class="<?php echo $day->checkstate; ?>" name="cb_<?php echo $module; ?>" value="<?php echo $day->id; ?>" id="cb_day_<?php echo $day->id; ?>" onclick="saveCheckboxRule('<?php echo $module; ?>', '<?php echo $day->id; ?>', '<?php echo $widget_id; ?>')" ></div>
        </td>
        <td><span><?php echo $day->title; ?></span></td>
    </tr>
    <?php } ?>
</table>