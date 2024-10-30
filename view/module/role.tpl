<table>
    <tr class="tr-head">
        <td class="checkbox-column">
            <div class="unchecked" id="cb_<?php echo $module; ?>_all" onclick="saveAllCheckboxRule('<?php echo $module; ?>','<?php echo $widget_id; ?>')"></div>
        </td>
        <td class="title-column"><?php echo $text_title; ?></td>
    </tr>

    <?php foreach($roles as $role) { ?>
    <tr class="miwowidgets_row" >
        <td class="checkbox-column" >
            <div class="<?php echo $role->checkstate; ?>" name="cb_<?php echo $module; ?>" value="<?php echo $role->id; ?>" id="cb_<?php echo $module; ?>_<?php echo $role->id; ?>" onclick="saveCheckboxRule('<?php echo $module; ?>', '<?php echo $role->id; ?>', '<?php echo $widget_id; ?>')" ></div>
        </td>
        <td><span><?php echo $role->title; ?></span></td>
    </tr>
    <?php } ?>
</table>