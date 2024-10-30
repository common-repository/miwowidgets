<div class="wrap">
    <h2><?php echo $text_title; ?></h2>
    <div style="width: 100%;">
        <fieldset style="border: solid 1px #e3e3e3; padding: 10px">
         <legend style="font-weight:bold; border: solid 1px #e3e3e3; padding: 5px"><?php echo $text_info; ?></legend>
            <?php echo $text_info_content; ?>
        </fieldset>
    </div>
    <div style="width: 350px; float: left">
        <form id="miwowidget_settings" method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label><?php echo $text_enable ?></label></th>
                    <td>
                        <input type="radio" name="miwowidget_settings[enable]" value="1" id="miwowidget_settings_enable_yes" <?php if($settings_enable == 1) echo 'checked'; ?> />
                        <label for="miwowidget_settings_enable_yes"><?php echo $text_yes ?></label>
                        <input type="radio" name="miwowidget_settings[enable]" value="0" id="miwowidget_settings_enable_no" <?php if($settings_enable == 0) echo 'checked'; ?> />
                        <label for="miwowidget_settings_enable_no"><?php echo $text_no ?></label>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label><?php echo $text_item_limit ?></label></th>
                    <td>
                        <select name="miwowidget_settings[item_count]">
                            <?php foreach ($count_select_options as $key => $option) { ?>
                            <option <?php echo ($settings_item_count == $key) ? 'selected' : ''; ?> value="<?php echo $key; ?>"><?php echo $option; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php if (!empty($module_setting_html)) {
                    foreach ($module_setting_html as $html){
                        echo $html;
                    }
                }?>
            </table>

            <div class="submit">
                <input id="button" onclick="formPost('#miwowidget_settings'); return false;" class="button button-primary" type="submit" value="<?php echo $btn_save_changes; ?>">
                <div style="margin-top: 5px; margin-left: 20px; float: left" id="form_message"></div>
            </div>
        </form>
    </div>
</div>