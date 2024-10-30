<div class="miwowidget-top-content">
    <table>
        <thead>
            <tr>
                <td><?php echo $text_head_module; ?></td>
                <td><?php echo $text_head_item; ?></td>
                <td><?php echo $text_head_status; ?></td>
            </tr>
        </thead>
        <?php foreach($rules as $rule) { ?>
        <tr>
            <td><?php echo $rule['name']; ?> </td>
            <td><?php echo $rule['item_name']; ?></td>
            <td><?php echo $rule['status']; ?></td>
        </tr>
        <?php } ?>
    </table>
    <input type="button" onclick="hideTop()" class="button button-primary right" value="<?php echo $text_hide; ?>">
</div>
<div class="miwowidget-top-clear"></div>
