<table>
    <tr class="tr-head">
        <td class="checkbox-column">
            <div class="unchecked" id="cb_<?php echo $module; ?>_all" onclick="saveAllCheckboxRule('<?php echo $module; ?>','<?php echo $widget_id; ?>')"></div>
        </td>
        <td class="title-column"><?php echo $text_title; ?></td>
    </tr>

    <?php foreach($posts as $post) { ?>
    <tr class="miwowidgets_row" >
        <td class="checkbox-column" >
            <div class="<?php echo $post->checkstate; ?>" name="cb_<?php echo $module; ?>" value="<?php echo $post->ID; ?>" id="cb_post_<?php echo $post->ID; ?>" onclick="saveCheckboxRule('<?php echo $module; ?>', '<?php echo $post->ID; ?>', '<?php echo $widget_id; ?>')" ></div>
        </td>
        <td><span><?php echo $post->post_title; ?></span></td>
    </tr>
    <?php } ?>
</table>

<div class="border"></div>
<?php if($page_count > 1) { ?>
<ul class="paginate pag">
    <li class="single"><?php echo $text_pagination; ?></li>
    <?php if($current != 1) { ?>
    <li><a onclick="paginate('<?php echo $current-1; ?>', '<?php echo $module; ?>', '<?php echo $widget_id; ?>'); return false;" href="#"><?php echo $text_prev; ?></a></li>
    <?php } ?>
    <?php for($i=1; $i<=$page_count; $i++){ ?>
        <?php if($current == $i) { ?>
            <li class="current"><?php echo $i; ?></li>
        <?php } else { ?>
            <li><a onclick="paginate('<?php echo $i; ?>', '<?php echo $module; ?>', '<?php echo $widget_id; ?>'); return false;" href="#"><?php echo $i; ?></a></li>
        <?php } ?>
    <?php } ?>
    <?php if($current != $page_count) { ?>
    <li><a onclick="paginate( '<?php echo $current+1; ?>', '<?php echo $module; ?>', '<?php echo $widget_id; ?>'); return false;" href="#"><?php echo $text_next; ?></a></li>
    <?php } ?>
</ul>
<?php } ?>
<div style="float: right; margin-right: 10px;">
    <span><?php echo $text_item_count; ?></span>
    <select id="miwowidgets_item_count_<?php echo $module; ?>" onchange="getData('<?php echo $module; ?>', '<?php echo $widget_id; ?>', false);">
        <?php foreach($count_select_options as $key => $option) { ?>
        <option <?php if($item_count == $key) echo 'selected'; ?> value="<?php echo $key; ?>"><?php echo $option; ?></option>
        <?php } ?>
    </select>
</div>

