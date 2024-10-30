<table>
    <tr class="tr-head">
        <td class="checkbox-column">
            <div class="unchecked" id="cb_<?php echo $module; ?>_all" onclick="saveAllCheckboxRule('<?php echo $module; ?>','<?php echo $widget_id; ?>')"></div>
        </td>
        <td class="title-column"><?php echo $text_title; ?></td>
    </tr>

    <?php foreach($months as $month) { ?>
    <tr class="miwowidgets_row" >
        <td class="checkbox-column" >
            <div class="<?php echo $month->checkstate; ?>" name="cb_<?php echo $module; ?>" value="<?php echo $month->id; ?>" id="cb_month_<?php echo $month->id; ?>" onclick="saveCheckboxRule('<?php echo $module; ?>', '<?php echo $month->id; ?>', '<?php echo $widget_id; ?>')" ></div>
        </td>
        <td><span><?php echo $month->title; ?></span></td>
    </tr>
    <?php } ?>
</table>