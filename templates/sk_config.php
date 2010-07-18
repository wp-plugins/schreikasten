<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div> <h2><?php _e('Schreikasten Settings', 'sk') ?></h2>
	<form name="form1" method="post" action="<?php echo remove_query_arg(array('mode', 'id')); ?>">
		<table class="form-table" style="width: 500px;">
			<?php if(strlen(get_option('sk_api_key'))==0 || !sk_verify_key()) {
				echo "<tr><td colspan='2'><div class='updated'><p>".sprintf(__("You need an Akismet <a href='%s' target='_BLANK'>API</a> to enable the antispam filter.", 'sk' ), 'http://wordpress.com/api-keys/')."</p></div></td></tr>"; 
			}?>
			<tr>
				<td style="width: 300px;"><?php _e("Akismet API", 'sk' ); ?>:</td>
				<td style="width: 200px;"><input type="text" name="sk_api_key" size="30" value="<?php echo $sk_api_key; ?>" /></td>
			</tr>
			<tr>
				<?php 
				if(strlen(get_option('sk_api_key'))==0) {
					update_option('sk_api_key_accepted',false);
					echo "<td colspan='2' style='text-align: center;'><strong>".sprintf(__("Set the <a href='%s' target='_BLANK'>API Key</a> if you require the antispam filter.", 'sk'), 'http://wordpress.com/api-keys/')."</strong></td>";
				} else {
					if(sk_verify_key()) {
						update_option('sk_api_key_accepted',true);
						echo "<td colspan='2' style='text-align: center; background: #00FF00'><strong>".__("API Key is valid.", 'sk')."</strong></td>";
					} else {
						update_option('sk_api_key_accepted',false); 
						echo "<td colspan='2' style='text-align: center; background: #FF0000'><strong>".__("API Key is not valid.", 'sk')."<br/>".sprintf(__("Set the <a href='%s' target='_BLANK'>API Key</a> if you require the antispam filter.", 'sk'), 'http://wordpress.com/api-keys/')."</strong></td>";
					}
				}?>
			</tr>

<tr>
	<td><?php _e('Show Avatar', 'sk'); ?>:
</td>
	<td>
		<input type="checkbox" class="checkbox" id="sk_avatar" name="sk_avatar"<?php
			if($status) echo " checked"; ?> />
	</td>
</tr>

<tr>
	<td>
		<?php 
		_e('Registered users only', 'sk'); ?>:
		</td><td>
		<select id="sk_registered" name="sk_registered">
			<option<?php echo $registered1; ?> value='1'><?php _e('Use general configuration', 'sk'); ?></option>
			<option<?php echo $registered2; ?> value='2'><?php _e('Yes', 'sk'); ?></option>
			<option<?php echo $registered3; ?> value='3'><?php _e('No', 'sk'); ?></option>
		</select>
</td>
</tr>

<tr>
	<td>
	<?php 
		_e('Comments must be moderated', 'sk'); ?>:
		</td><td>
		<select id="sk_moderation" name="sk_moderation">
			<option<?php echo $moderation0; ?> value='0'><?php _e('Use general configuration', 'sk'); ?></option>
			<option<?php echo $moderation1; ?> value='1'><?php _e('Yes', 'sk'); ?></option>
			<option<?php echo $moderation2; ?> value='2'><?php _e('No', 'sk'); ?></option>
		</select>
</td>
</tr>

<tr>
	<td>
	<?php
		_e('Requiere e-mail','sk'); ?>:
		</td><td>
		<select id="sk_requiremail" name="sk_requiremail">
			<option<?php echo $require1; ?> value='1'><?php _e('Use general configuration', 'sk'); ?></option>
			<option<?php echo $require2; ?> value='2'><?php _e('Yes', 'sk'); ?></option>
			<option<?php echo $require3; ?> value='3'><?php _e('No', 'sk'); ?></option>
		</select>
</td>
</tr>

<tr>
	<td>
	<?php
		_e('Announce comments (send e-mail)','sk'); ?>:
		</td><td>
		<select id="sk_announce" name="sk_announce">
			<option<?php echo $announce1; ?> value='1'><?php _e('Use general configuration', 'sk'); ?></option>
			<option<?php echo $announce2; ?> value='2'><?php _e('Send e-mail', 'sk'); ?></option>
			<option<?php echo $announce3; ?> value='3'><?php _e('Don\'t send e-mail', 'sk'); ?></option>
		</select>
	</td>
</tr>

<tr>
	<td>
		<?php _e('Allow replies', 'sk'); ?>:
	</td>
	<td>
		<input type="checkbox" class="checkbox" id="sk_replies" name="sk_replies"<?php
			if($replies) echo " checked"; ?> />
		</td>
</tr>

<tr>
	<td>
		<?php _e('Alert users about posting e-mails in their comments', 'sk'); ?>:
</td>
	<td>
		<input type="checkbox" class="checkbox" id="sk_alert_about_emails" name="sk_alert_about_emails"<?php
		if($alert_about_emails) echo " checked"; ?> />
	</td>
</tr>

<tr>
	<td><?php
		_e('Number of characters allowed per comment','sk'); ?>: 
		</td><td>
		<input type="text" name="sk_maxchars" style="width: 50px;" value="<?php echo $maxchars; ?>">
</td>
</tr>

<tr>
	<td>
	<?php
		_e('Refresh rate','sk'); ?><a title="<?php _e('This option is in an early stage, take caution.', 'sk'); ?>" style="cursor: pointer;">(!)</a>:
	</td><td>
	<select id="sk_refresh" name="sk_refresh">
		<option<?php echo $selectedrefresh0; ?> value="0"><?php _e('Never','sk'); ?></option>
		<option<?php echo $selectedrefresh5; ?> value="5">5 <?php _e('seconds','sk'); ?></option>
		<option<?php echo $selectedrefresh10; ?> value="10">10 <?php _e('seconds','sk'); ?></option>
		<option<?php echo $selectedrefresh60; ?> value="60">60 <?php _e('seconds','sk'); ?></option>
	</select>
</td>
</tr>

<tr>
	<td>
	<?php
		_e('Max days to hold a PC blacklisted','sk'); ?>:
	</td><td>
		<select id="sk_bl_days" name="sk_bl_days">
			<option<?php echo $selecteddays1; ?>>1</option>
			<option<?php echo $selecteddays2; ?>>2</option>
			<option<?php echo $selecteddays5; ?>>5</option>
			<option<?php echo $selecteddays7; ?>>7</option>
			<option<?php echo $selecteddays14; ?>>14</option>
			<option<?php echo $selecteddays0; ?> value="0"><?php _e('Forever','sk'); ?></option>
		</select>
</td>
</tr>

<tr>
	<td>
	<?php
			_e('Max pending messages from blacklisted PC','sk'); ?>:
		</td><td>
		<select id="sk_bl_maxpending" name="sk_bl_maxpending">
			<option<?php echo $selectedmaxpending0; ?> value="0"><?_e('None', 'sk');?></option>
			<option<?php echo $selectedmaxpending1; ?>>1</option>
			<option<?php echo $selectedmaxpending2; ?>>2</option>
			<option<?php echo $selectedmaxpending5; ?>>5</option>
			<option<?php echo $selectedmaxpending10; ?>>10</option>
		</select>
</td>
</tr>
<tr>
	<td colspan='2'><input type="hidden" class="checkbox" id="sk_rss" name="sk_rss" value="<?php
			echo $rss; ?>" /><input id="sk_title" name="sk_title" type="hidden" size="30" value="<?php echo $title; ?>" /><input type="hidden" name="sk_items" style="width: 30px;" value="<?php echo $items; ?>"><input type="hidden" name="sk-submit" value="true"><input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></td>
</tr>
</table>
	</form>
</div>
