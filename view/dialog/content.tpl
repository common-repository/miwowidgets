<div id="miwowidget-inside-<?php echo $module; ?>" class="widget-inside miwowidget-inside">
    <div class="widget-top miwowidget-content-header">
        <div class="widget-title" style="width: 100%"><h4><?php echo $text_title; ?><span class="in-widget-title"></span></h4></div>
	</div>
    <?php if($show_filter == true) { ?>
    <div class="miwowidgets_filter">
        <?php if ($show_search_filter == true) { ?>
        <div class="miwowidgets_filter_search" id="miwowidgets_search_<?php echo $module; ?>">
            <label for="miwowidgets_search"><?php echo $text_search_filter; ?></label>
            <input type="text" value="" id="miwowidgets_search_field_<?php echo $module; ?>" onchange="getData('<?php echo $module; ?>', '<?php echo $widget_id; ?>', false);">
        </div>
        <?php } ?>

        <div class="miwowidgets_filter_clear " id="miwowidgets_clear_<?php echo $module; ?>">
            <a class="button" id="miwowidgets_clear_<?php echo $module; ?>" href="javascript:;" onclick="resetFilters('<?php echo $module; ?>', '<?php echo $widget_id; ?>');"><?php echo $button_text_reset_filters; ?></a>
        </div>

        <?php if($show_order_filter == true) { ?>
        <div class="miwowidgets_filter_order" id="miwowidgets_order_<?php echo $module; ?>">
            <label for="miwowidgets_order_field_<?php echo $module; ?>"><?php echo $text_order_filter; ?></label>
            <select id="miwowidgets_order_field_<?php echo $module; ?>" onchange="getData('<?php echo $module; ?>', '<?php echo $widget_id; ?>', false);">
                <?php foreach($order_filter_values as $value => $text) { ?>
                <option value="<?php echo $value; ?>"><?php echo $text; ?></option>
                <?php } ?>
            </select>
        </div>
        <?php } ?>

        <?php if($show_show_filter == true) { ?>
        <div class="miwowidgets_filter_show" id="miwowidgets_show_<?php echo $module; ?>">
            <label for="miwowidgets_show_field"><?php echo $text_show_filter; ?></label>
            <select id="miwowidgets_show_field_<?php echo $module; ?>" onchange="getData('<?php echo $module; ?>', '<?php echo $widget_id; ?>', false);">
                <?php foreach($show_filter_values as $value => $text) { ?>
                <option value="<?php echo $value; ?>"><?php echo $text; ?></option>
                <?php } ?>
            </select>
        </div>
        <?php } ?>
    </div>
    <?php } ?>

    <div id="miwowidgets_<?php echo $module; ?>" class="miwowidgets_content"></div>
</div>