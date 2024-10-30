<table>
    <tr class="tr-head">
        <td class="checkbox-column">
            <div class="unchecked" id="cb_<?php echo $module; ?>_all" onclick="saveAllCheckboxRule('<?php echo $module; ?>','<?php echo $widget_id; ?>')"></div>
        </td>
        <td class="title-column"><?php echo $text_title; ?></td>
    </tr>

    <?php foreach($browsers as $browser) { ?>
    <tr class="miwowidgets_row" >
        <td class="checkbox-column" >
            <div class="<?php echo $browser->checkstate; ?>" name="cb_<?php echo $module; ?>" value="<?php echo $browser->id; ?>" id="cb_browser_<?php echo $browser->id; ?>" onclick="saveCheckboxRule('<?php echo $module; ?>', '<?php echo $browser->id; ?>', '<?php echo $widget_id; ?>')" ></div>
        </td>
        <td><span><?php echo $browser->title; ?></span></td>
    </tr>
    <?php } ?>
</table>