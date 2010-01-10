<?php
			if(!function_exists('minimax_version') && minimax_version()<0.3) { ?>
<p>
	<label for="sk_warning">
					<?php printf(__('You have to install <a href="%s" target="_BLANK">minimax 0.3</a> in order for this plugin to work', 'sk'), "http://wordpress.org/extend/plugins/minimax/" ); ?>
	</label>
</p><?
} ?>

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
	<label for="sk_num"><?php 
		_e('Registered users only', 'sk'); ?>:
		<select class="widefat" id="sk_registered" name="sk_registered">
			<option<?php echo $registered1; ?> value='1'><?php _e('Use general configuration', 'sk'); ?></option>
			<option<?php echo $registered2; ?> value='2'><?php _e('Yes', 'sk'); ?></option>
			<option<?php echo $registered3; ?> value='3'><?php _e('No', 'sk'); ?></option>
		</select>
		</label>
</p>

<p>
	<label for="sk_num"><?php
		_e('Requiere e-mail','sk'); ?>:
		<select class="widefat" id="sk_requiremail" name="sk_requiremail">
			<option<?php echo $require1; ?> value='1'><?php _e('Use general configuration', 'sk'); ?></option>
			<option<?php echo $require2; ?> value='2'><?php _e('Yes', 'sk'); ?></option>
			<option<?php echo $require3; ?> value='3'><?php _e('No', 'sk'); ?></option>
		</select>
	</label>
</p>

<p>
	<label for="sk_num"><?php
		_e('Announce comments (send e-mail)','sk'); ?>:
		<select class="widefat" id="sk_announce" name="sk_announce">
			<option<?php echo $announce1; ?> value='1'><?php _e('Use general configuration', 'sk'); ?></option>
			<option<?php echo $announce2; ?> value='2'><?php _e('Send e-mail', 'sk'); ?></option>
			<option<?php echo $announce3; ?> value='3'><?php _e('Don\'t send e-mail', 'sk'); ?></option>
		</select>
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
		if($alert_about_emails) echo " checked"; ?> /> <?php _e('Alert users about posting e-mails in their comments', 'sk'); ?>
	</label>
</p>

<p>
	<label for="sk_num"><?php
		_e('Number of characters allowed per comment','sk'); ?>: <input type="text" name="sk_maxchars" style="width: 50px;" value="<?php echo $maxchars; ?>">
	</label>
</p>

<p>
	<label for="sk_num"><?php
		_e('Items per page','sk'); ?>: <input type="text" name="sk_items" style="width: 30px;" value="<?php echo $items; ?>">
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
			<option<?php echo $selecteddays0; ?> value="0"><?php _e('Forever','sk'); ?></option>
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
