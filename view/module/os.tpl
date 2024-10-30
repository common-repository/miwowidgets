<table>
    <tr class="tr-head">
        <td class="checkbox-column">
            <div class="unchecked" id="cb_<?php echo $module; ?>_all" onclick="saveAllCheckboxRule('<?php echo $module; ?>','<?php echo $widget_id; ?>')"></div>
        </td>
        <td class="title-column"><?php echo $text_title; ?></td>
    </tr>

    <?php foreach($oses as $os) { ?>
    <tr class="miwowidgets_row" >
        <td class="checkbox-column" >
            <div class="<?php echo $os->checkstate; ?>" name="cb_<?php echo $module; ?>" value="<?php echo $os->id; ?>" id="cb_os_<?php echo $os->id; ?>" onclick="saveCheckboxRule('<?php echo $module; ?>', '<?php echo $os->id; ?>', '<?php echo $widget_id; ?>')" ></div>
        </td>
        <td><span><?php echo $os->title; ?></span></td>
    </tr>
    <?php } ?>
</table>