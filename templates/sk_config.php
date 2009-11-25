<div class="wrap">
	<h2><?php _e('Schreikasten Configuration', 'sk') ?></h2>
	<form name="form1" method="post" action="<?php echo remove_query_arg(array('mode', 'id')); ?>">
		<table align="center">
			<?php
			if(!(function_exists('minimax') && minimax_version()==0.2)) { ?>
			<tr><td colspan="3">
					<?php _e('You have to install <a href="http://www.sebaxtian.com/acerca-de/minimax"  target="_BLANK">minimax 0.2</a> in order for this plugin to work', 'sk'); ?>
			</td></tr><?
} ?>
			<tr>
				<td colspan=3><input type="hidden" name="mode_x" value="api_x"><?php _e("You need an Akismet <a href='http://wordpress.com/api-keys/' target='_BLANK'>API</a> to enable the antispam filter.", 'sk' );?></td>
			</tr>
			<tr>
				<td><?php _e("Akismet API", 'sk' ); ?>:</td>
				<td><input type="text" name="sk_api_key" size="30" value="<?php echo $sk_api_key; ?>" /></td>
				<td><input type="submit" name="Submit" value="<?php _e('Update', 'sk' ) ?>" /></td>
			</tr>
			<tr><?php 
				if(strlen(get_option('sk_api_key'))==0) {
					echo "<td colspan=3 align='center' bgcolor='#FF0000'><strong>".__("Set API Key", 'sk')."</strong></td>";
				} else {
					if(sk_verify_key()) 
							echo "<td colspan=3 align='center' bgcolor='#00FF00'><strong>".__("API Key is valid", 'sk')."</strong></td>";
					else 
							echo "<td colspan=3 align='center' bgcolor='#FF0000'><strong>".__("API Key is not valid", 'sk')."</strong></td>";
				}?>
			</tr>
		</table>	
	</form>
</div>
