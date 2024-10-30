<table>
    <tr class="tr-head">
        <td class="checkbox-column">
            <div class="unchecked" id="cb_<?php echo $module; ?>_all" onclick="saveAllCheckboxRule('<?php echo $module; ?>','<?php echo $widget_id; ?>')"></div>
        </td>
        <td class="title-column"><?php echo $text_title; ?></td>
    </tr>

    <?php foreach($customposttypes as $customposttype) { ?>
    <tr class="miwowidgets_row" >
        <td class="checkbox-column" >
            <div class="<?php echo $customposttype->checkstate; ?>" name="cb_<?php echo $module; ?>" value="<?php echo $customposttype->name; ?>" id="cb_<?php echo $module; ?>_<?php echo $customposttype->name; ?>" onclick="saveCheckboxRule('<?php echo $module; ?>', '<?php echo $customposttype->name; ?>', '<?php echo $widget_id; ?>')" ></div>
        </td>
        <td><span><?php echo $customposttype->label; ?></span></td>
    </tr>
    <?php } ?>
</table>