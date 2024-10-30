<?php if($active_language_plugin == ''){ ?>
    <div style="margin-left: 15px; margin-top: 15px"><span><strong><?php echo $error_not_installed_language_plugin; ?></strong></span></div>
<?php return; } else{ ?>
    <div style="padding: 20px; color: #d2340a"><span><strong><?php echo $text_active_multilanguage_plugin ?></strong> <?php echo $active_language_plugin; ?></span></div>
<?php } ?>
<table>
    <tr class="tr-head">
        <td class="checkbox-column">
            <div class="unchecked" id="cb_<?php echo $module; ?>_all" onclick="saveAllCheckboxRule('<?php echo $module; ?>','<?php echo $widget_id; ?>')"></div>
        </td>
        <td class="title-column"><?php echo $text_title; ?></td>
    </tr>

    <?php foreach($languages as $language) { ?>
    <tr class="miwowidgets_row" >
        <td class="checkbox-column" >
            <div class="<?php echo $language->checkstate; ?>" name="cb_<?php echo $module; ?>" value="<?php echo $language->id; ?>" id="cb_language_<?php echo $language->id; ?>" onclick="saveCheckboxRule('<?php echo $module; ?>', '<?php echo $language->id; ?>', '<?php echo $widget_id; ?>')" ></div>
        </td>
        <td><span><?php echo $language->title; ?></span></td>
    </tr>
    <?php } ?>
</table>