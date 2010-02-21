<div class="wrap">
	<h2><?php _e('Schreikasten Configuration', 'sk') ?></h2>
	<form name="form1" method="post" action="<?php echo remove_query_arg(array('mode', 'id')); ?>">
		<table align="center">
			<tr><td colspan="3"><div class='updated'><p>
					<?php
			if(!function_exists('minimax_version') || minimax_version()<SK_MNMX_V) { printf(__('You have to install <a href="%s" target="_BLANK">minimax %1.1f</a> in order for this plugin to work.', 'mudslide'), "http://wordpress.org/extend/plugins/minimax/", SK_MNMX_V ); }?></p>
				<p><?php _e("You need an Akismet <a href='http://wordpress.com/api-keys/' target='_BLANK'>API</a> to enable the antispam filter.", 'sk' );?></p></div>
			</td></tr>
			<tr>
				<td><?php _e("Akismet API", 'sk' ); ?>:</td>
				<td><input type="text" name="sk_api_key" size="30" value="<?php echo $sk_api_key; ?>" /></td>
				<td><input type="hidden" name="mode_x" value="api_x"><input type="submit" name="Submit" value="<?php _e('Update', 'sk' ) ?>" /></td>
			</tr>
			<tr><?php 
				if(strlen(get_option('sk_api_key'))==0) {
					echo "<td colspan=3 align='center' bgcolor='#FF0000'><strong>".__("Set API Key", 'sk')."</strong></td>";
				} else {
					if(sk_verify_key()) {
						update_option('sk_api_key_accepted',true);
						echo "<td colspan=3 align='center' bgcolor='#00FF00'><strong>".__("API Key is valid", 'sk')."</strong></td>";
					} else {
						update_option('sk_api_key_accepted',false); 
						echo "<td colspan=3 align='center' bgcolor='#FF0000'><strong>".__("API Key is not valid", 'sk')."</strong></td>";
					}
				}?>
			</tr>
		</table>	
	</form>
</div>
