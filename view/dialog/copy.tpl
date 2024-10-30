<style type="text/css">
    .miwowidget-top-content {
        overflow-y: inherit;
        height: 150px;
        position: relative;
    }

    #widgets_chosen{
        float: left !important;
    }

</style>
<div class="miwowidget-top-content">
    <div style="float: left">
        <span style="float: left; margin-right: 15px; margin-top: 8px;"><?php echo $text_select_span ?></span>
        <select style="width: 300px; float: left" name="widgets" id="widgets" multiple class="chosen-select" data-placeholder="<?php echo $text_select ?>">
            <?php foreach($widgets as $widget) { ?>
            <option value="<?php echo $widget['value']; ?>" <?php echo $widget['disable']; ?> ><?php echo $widget['name'] ?></option>
            <?php } ?>
        </select>
        <input type="button" style="margin-left: 10px; float: left; height: 34px !important;" onclick="copy('<?php echo $widget_id; ?>')" class="button button-primary" value="<?php echo $text_copy; ?>">
        <div style="margin-top: 5px; margin-left: 20px; float: left;" id="date_message_top"></div>
    </div>

    <input type="button" style="margin: 5px;" onclick="hideTop()" class="button button-primary right" value="<?php echo $text_hide; ?>">
</div>
<div class="miwowidget-top-clear"></div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        var config = {
            '.chosen-select' : {},
            '.chosen-select-deselect' : {allow_single_deselect:true},
            '.chosen-select-width' : {width:"95%"}
        }
        jQuery('.chosen-select').chosen();
    });
</script>

