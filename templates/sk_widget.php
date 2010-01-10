<?php
	//Create the nonce
	$nonce = wp_create_nonce('schreikasten');

	if(function_exists('minimax_version') && minimax_version()>=0.3) {

	//Get current user
	global $current_user;
	get_currentuserinfo();
	
	$options = get_option('widget_sk');
	$avatar = $options['avatar']; 
	$req = sk_require_name_and_email();
	
	//Get maxchars
	$maxchars = 255;
	if(isset($options['maxchars'])) $maxchars = $options['maxchars'];
	
	//Update blacklist dates
	sk_blacklist_update();
	
	//Set name and email on cookie name
	$alias="";
	$email="";
	
	if($_COOKIE['comment_author_' . COOKIEHASH]) {
		$alias=$_COOKIE['comment_author_' . COOKIEHASH];
		$email=$_COOKIE['comment_author_email_' . COOKIEHASH];
	}
	
	$anonymous_avatar=sk_plugin_url('/img/anonymous.jpg');
	$uri_sk=sk_plugin_url('/ajax/content.php');
	$uri_skadd=sk_plugin_url('/ajax/add_comment.php'); ?><script type='text/javascript'>
	/* <![CDATA[ */
	
	var mm_add = new minimax('<?php echo $uri_skadd; ?>', 'sk_content');
	var mm_get = new minimax('<?php echo $uri_sk; ?>', 'sk_content');
	
	function sk_timer() {
		var sk_timer_div = document.getElementById('sk_timer');
		if(sk_timer_div.value) clearTimeout(sk_timer_div.value);
		<?php $time=$options['refresh']*1000; ?>
		timer_id=setTimeout( 'sk_refresh();' , <?php echo $time; ?>);
		sk_timer_div.value=timer_id;
	}
	
	function sk_refresh() {
		var sk_timer_div = document.getElementById('sk_timer');
		<?php if($options['refresh']>0) {		
		echo "\nsk_timer();";
		} ?>
		mm_get.post('nonce=<?php echo $nonce; ?>&page='+document.getElementsByName('sk_page')[0].value);		
	}

	function check_email( cadena ) {
		var answer=false;
		var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;
		if (filter.test(cadena)) {
			answer=true;
		}
		return answer;
	}
	
	function email_intext ( cadena ) {
		var answer=false;
		var emailsArray = cadena.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi);
		if (emailsArray)
			answer=true;
		return answer;
	}
	
	function for_delete() {
		document.getElementById("sk_for_id").value='0';
		document.getElementById("sk_for_name").innerHTML='';
		document.getElementById("sk_for_tr").className='sk-for-nai';
	}
	
	function for_set(id, name) {
		document.getElementById("sk_for_id").value=id;
		document.getElementById("sk_for_name").innerHTML=name;
		var text=document.getElementsByName("sk_text")[0].value;
		document.getElementsByName("sk_text")[0].value='<?php _e('For', 'sk'); ?> '+name+' - '+text;
		document.getElementById("sk_for_tr").className='sk-for-yep';
	}
		
	function sk_pressButton() {
		//document.getElementById('sk_throbber-text').innerHTML='';
		var alias=document.getElementsByName("sk_alias")[0].value;
		var text=document.getElementsByName("sk_text")[0].value;
		var email=document.getElementsByName("sk_email")[0].value;
		var skfor=document.getElementsByName("sk_for_id")[0].value;
		<?php	if ($req) { ?>
		if(!check_email(email)) {
			alert('<?php _e("E-mail is required", "sk"); ?> ');
			return false;
		}
		<?php } ?>
		<?php if($options['alert_about_emails']) { ?>
		if(email_intext ( text ) ) {
			check=confirm("<?php _e("To prevent identification theft, we recomend\\nthat you do not include e-mail adresses.\\nDo you want to continue?", "sk"); ?>");
			if(!check) {
				return false;
			}
		}
		<?php } ?>
		document.getElementById('th_sk_alias').innerHTML = alias.replace(/&/gi,"&amp;");
		document.getElementById('th_sk_text').innerHTML = text.replace(/&/gi,"&amp;");
		email=email.replace(/&amp;/gi,"y");
		alias=alias.replace(/&/gi,"%26");
		text=text.replace(/&/gi,"%26");
		var post = "nonce=<?php echo $nonce; ?>&alias="+alias+"&email="+email+"&text="+text+"&for="+skfor;
		for_delete();
		document.getElementsByName('sk_page')[0].value=1;
		document.getElementsByName("sk_text")[0].value="";
		mm_add.post(post);
		<?php
			$options = get_option('widget_sk');
			if(sk_is_blacklisted() || 1 == get_option('comment_moderation')) {
				$message=__('Your message has been sent. Comments have\nto be approved before posted.', 'sk');
				if(sk_is_blacklisted()) {
					$message=__('Your message has been sent but this PC was blacklisted.\nComments have to be approved before posted.', 'sk');
				} ?>
		alert('<?php echo $message; ?>');
		this.disabled=true;
		<?php } ?>
		}
/* ]]> */
</script>
<a name='sk_top'></a>
<table width='100%' border='0'>
	<tr><td width="20px"></td><td width="100%"></td></tr><?php
	if(sk_only_registered_users() && $current_user->ID==0) { ?>
	<tr>
		<td colspan="2" id="skwarning">
			<input type='hidden' id='sk_timer' value=''/><input type='hidden' name='sk_page' value='1'/>
			<?php printf( __('You must be <a href="%s">signed in</a> to post a comment', 'sk'), wp_login_url(get_permalink())); ?>.
		</td>
	</tr><?php } else {
	if(sk_is_blacklisted()) {
		if(sk_can_not_accept_more_messages()) { ?>
	<tr><td colspan="2"><?php _e("This PC was blacklisted. At this time comments cannot be posted.", "sk"); $disabled=" disabled"; ?></td></tr><?php
		}
	} ?>	
	<?
	if($current_user->ID==0) { ?>
	<tr>
		<td>Alias:</td>
		<td>
			<input class='sk-text' type='text' name='sk_alias' value='<?php echo $alias; ?>'/>
		</td>
	</tr>
	<tr>
		<td>Email:</td>
		<td>
			<input class='sk-text' type='text' name='sk_email' value='<?php echo $email; ?>'/>
		</td>
	</tr><?php
	} ?>
	<tr class='sk-for-nai' id='sk_for_tr'>
		<td><?php _e('For', 'sk'); ?>:</td>
		<td><span id='sk_for_name'></span>&nbsp;<img src="<?php echo sk_plugin_url('/img/clear.png'); ?>" align="top" border="0" alt="" onclick='for_delete();' /><input id='sk_for_id' name='sk_for_id' type='hidden' size='5' value='0'/></td>
	</tr>
	<tr>
		<td colspan='2' align='right'><textarea rows="" cols="" class='sk-area' name='sk_text' onkeypress="
			var key;
			if(window.event)
				key = window.event.keyCode; //IE
			else
				key = event.keyCode;
			if(this.value.length><?php echo $maxchars; ?>-1 &amp;&amp; !(key==8 || key==37 || key==38 || key==39 || key==40) )
			return false;"></textarea></td>
	</tr>
</table>
<table width='100%'>		
	<tr>
		<td align="right" class='sk-little'><?php if($current_user->ID==0) { ?>
			<?php _e('Mail will not be published', 'sk'); ?><br/><?php if ($req) _e("(but it's required)", "sk"); else if($avatar) _e("(but it's used for avatar)", "sk"); ?><?php } else { ?>
			<?php printf(__('Loged in as %s', 'sk'), $current_user->display_name); ?>
					<br/><a href="<?php 
							if(is_home()) {
								echo wp_logout_url(get_option('home'));
							} else {
								echo wp_logout_url(get_permalink());
							} ?>" title="<?php _e('Log out', 'sk'); ?>"><?php _e('Log out', 'sk'); ?>.</a>
				<input name='sk_alias' type='hidden' value='<?php echo $current_user->display_name; ?>'/>
				<input name='sk_email' type='hidden' value='<?php echo $current_user->user_email; ?>'/><?php
		} ?>
		</td>
		<td align="right" width='50px'>
			<input type='hidden' id='sk_timer' value=''/><input type='hidden' name='sk_page' value='1'/><input<?php echo $disabled; ?> type='button' class='sk-button' value='<?php _e('Submit', 'sk'); ?>' onclick='sk_pressButton();'/></td>
	</tr><?php } 
	$uri_img=sk_plugin_url('/img/loading.gif');
	?>
</table>
	<div id='sk_content'></div>
	<div id='sk_page'><div id='throbber-page' class='off'></div></div>
	<script type='text/javascript'>
		var sk_semaphore=new Semaphore();
		mm_add.setSemaphore(sk_semaphore);
		mm_add.setThrobber('throbber-img', 'on', 'off');
		mm_get.setSemaphore(sk_semaphore);
		mm_get.setThrobber('throbber-page', 'on', 'off');
		sk_refresh();</script>
<?php } else {
			printf(__('You have to install <a href="%s" target="_BLANK">minimax 0.3</a> in order for this plugin to work', 'sk'), "http://wordpress.org/extend/plugins/minimax/" );
} ?>
