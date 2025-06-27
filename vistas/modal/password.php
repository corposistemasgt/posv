<?php

if (isset($_REQUEST['user_id'])) {
    $user_id = intval($_REQUEST['user_id']);
    ?>
			<input type="hidden" value="<?php echo $user_id; ?>" id="user_id_mod" name="user_id_mod">
	<?php }?>