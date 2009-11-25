<p>
	<label for="sk_title">
		<?php _e('Title:', 'sk'); ?>
		<input class="widefat" id="sk_title" name="sk_title" type="text" value="<?php echo $title; ?>" />
	</label>
</p>

<p>
	<label for="sk_avatars">
		<input type="checkbox" class="checkbox" id="sk_avatar" name="sk_avatar"<?php
			if($status) echo " checked"; ?> /> <?php _e('Show Avatar', 'sk'); ?>
	</label>
</p>

<p>
	<label for="sk_avatars">
		<input type="checkbox" class="checkbox" id="sk_registered" name="sk_registered"<?php
		if($registered) echo " checked"; ?> /> <?php _e('Registered users only', 'sk'); ?>
		</label>
</p>
				
<p>
	<label for="sk_replies">
		<input type="checkbox" class="checkbox" id="sk_replies" name="sk_replies"<?php
			if($replies) echo " checked"; ?> /> <?php _e('Allow replies', 'sk'); ?>
	</label>
</p>
			
<p>
	<label for="sk_alert_about_emails">
		<input type="checkbox" class="checkbox" id="sk_alert_about_emails" name="sk_alert_about_emails"<?php
		if($alert_about_emails) echo " checked"; ?> /> <?php _e('Alert users about posting emails in their comments', 'sk'); ?>
	</label>
</p>

<p>
	<label for="sk_num"><?php
		_e('Items per page','sk'); ?>:
		<select class="widefat" id="sk_items" name="sk_items">
			<option<?php echo $selected5; ?>>5</option>
			<option<?php echo $selected10; ?>>10</option>
			<option<?php echo $selected15; ?>>15</option>
			<option<?php echo $selected20; ?>>20</option>
		</select>
	</label>
</p>

<p>
	<label for="sk_num"><?php
		_e('Refresh rate','sk'); ?>: <?php _e('(this option is in an early stage, take caution)', 'sk'); ?>
	<select class="widefat" id="sk_refresh" name="sk_refresh">
		<option<?php echo $selectedrefresh0; ?> value="0"><?php _e('Never','sk'); ?></option>
		<option<?php echo $selectedrefresh5; ?> value="5">5 <?php _e('seconds','sk'); ?></option>
		<option<?php echo $selectedrefresh10; ?> value="10">10 <?php _e('seconds','sk'); ?></option>
		<option<?php echo $selectedrefresh60; ?> value="60">60 <?php _e('seconds','sk'); ?></option>
	</select>
	</label>
</p>

<p>
	<label for="sk_num"><?php
		_e('Max days to hold a PC blacklisted','sk'); ?>:
		<select class="widefat" id="sk_bl_days" name="sk_bl_days">
			<option<?php echo $selecteddays1; ?>>1</option>
			<option<?php echo $selecteddays2; ?>>2</option>
			<option<?php echo $selecteddays5; ?>>5</option>
			<option<?php echo $selecteddays7; ?>>7</option>
			<option<?php echo $selecteddays14; ?>>14</option>
			<option<?php echo $selecteddays0; ?> value="0">Forever</option>
		</select>
	</label>
</p>

<p>
	<label for="sk_num"><?php
			_e('Max pending messages from blacklisted PC','sk'); ?>:
		<select class="widefat" id="sk_bl_maxpending" name="sk_bl_maxpending">
			<option<?php echo $selectedmaxpending0; ?> value="0"><?_e('None', 'sk');?></option>
			<option<?php echo $selectedmaxpending1; ?>>1</option>
			<option<?php echo $selectedmaxpending2; ?>>2</option>
			<option<?php echo $selectedmaxpending5; ?>>5</option>
			<option<?php echo $selectedmaxpending10; ?>>10</option>
		</select>
	</label>
</p>

<input type="hidden" id="sk-submit" name="sk-submit" value="1" />