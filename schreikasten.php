<?php
/*
Plugin Name: Schreikasten
Plugin URI: http://www.sebaxtian.com/acerca-de/schreikasten
Description: A shoutbox using ajax and akismet.
Version: 0.11.13
Author: Juan Sebastián Echeverry
Author URI: http://www.sebaxtian.com
*/

/* Copyright 2008-2010 Sebaxtian (email : sebaxtian@gawab.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

define ("SK_NOT_FILTERED", 0);
define ("SK_HAM", 1);
define ("SK_SPAM", 2);
define ("SK_MOOT", 3);
define ("SK_BLACK", 4);
define ("SK_BLOCKED", -1);

define ("SK_ANNOUNCE_CONFIG", 1);
define ("SK_ANNOUNCE_YES", 2);
define ("SK_ANNOUNCE_NO", 3);

$db_version=get_option('sk_db_version');
$sk_user_agent = "WordPress/$wp_version | Schreikasten/0.1";

add_action('init', 'sk_text_domain');
add_action('init', 'sk_cookie_id');
add_action('wp_head', 'sk_header');
add_action('admin_menu', 'sk_menus');
add_action('activate_schreikasten/schreikasten.php', 'sk_activate');

/**
* Function to add the required data to the header in the site.
* This function should be called by an action.
*
* @access public
*/
function sk_header() {
	$css = get_theme_root()."/".get_template()."/schreikasten.css";
	if(file_exists($css)) {
		echo "<link rel='stylesheet' href='".get_bloginfo('template_directory')."/schreikasten.css' type='text/css' media='screen' />";
	} else {
		echo "<link rel='stylesheet' href='".sk_plugin_url("/css/schreikasten.css")."' type='text/css' media='screen' />";
	}
}
 
/**
* Returns the ID of the PC who is makeing the call. If there isn't an assigned
* cookie create one.
* This function is public but an action call it too.
*
* @access public
*/
function sk_cookie_id() {
	$answer=0;
	
	// If there isn't a cookie, create one
	if(!$_COOKIE['sk-id']) {
		//Positive numbers are for logged users, negative for cookies
		@setcookie("sk-id", (mt_rand())*-1, time()+3600*24*365*10,"/","");
	} else {
		$answer=$_COOKIE['sk-id'];
	}
	
	return $answer;
}

/**
* Function to do a post request. This function is used by the antispam system, akismet.
*
* @access public
* @param string request The post request
* @param string host The server for whom we call
* @param string path The URL to ask in the server
* @param int port The port number. Optional, it uses the HTTP port by default
*/
function sk_http_post($request, $host, $path, $port = 80) {
	global $sk_user_agent;
	$http_request  = "POST $path HTTP/1.0\r\n";
	$http_request .= "Host: $host\r\n";
	$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_settings('blog_charset') . "\r\n";
	$http_request .= "Content-Length: " . strlen($request) . "\r\n";
	$http_request .= "User-Agent: $sk_user_agent\r\n";
	$http_request .= "\r\n";
	$http_request .= $request;
	
	$response = '';
	if( false !== ( $fs = @fsockopen($host, $port, $errno, $errstr, 3) ) ) {
		fwrite($fs, $http_request);
		while ( !feof($fs) ) {
			$response .= fgets($fs, 1160); // One TCP-IP packet
		}
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);
	}
	return $response;
}

/**
* Verify Akismet Key
*
* @return bool
* @access public
*/
function sk_verify_key( ) {
	$answer=false;
	
	//Ask if the key is asigned to this host
	$key=get_option('sk_api_key');
	$blog = urlencode( get_option('home') );
	if(strlen(get_option('sk_api_key'))!=0) {
		$response = sk_http_post("key=$key&blog=$blog", 'rest.akismet.com', '/1.1/verify-key');
		if ( 'valid' == $response[1] ) {
			$answer=true;
		}
	}
	return $answer;
}

/**
* To declare where are the mo files (i18n).
* This function should be called by an action.
*
* @access public
*/
function sk_text_domain() {
	load_plugin_textdomain('sk', 'wp-content/plugins/schreikasten/lang');
}

/**
* Function to return the url of the plugin concatenated to a string. The idea is to
* use this function to get the entire URL for some file inside the plugin.
*
* @access public
* @param string str The string to concatenate
* @return The URL of the plugin concatenated with the string 
*/
function sk_plugin_url($str = '')
{

	$aux = '/wp-content/plugins/schreikasten/'.$str;
	$aux = str_replace('//', '/', $aux);
	$url = get_bloginfo('wpurl');
	return $url.$aux;
	
}

/**
* Indicates if require name and email.
*
* @return bool
* @access public
*/
function sk_require_name_and_email()
{
	$answer=false;
	$options = get_option('widget_sk');
	$require = $options['requiremail'];
	$general = get_option('require_name_email');
	
	if(is_bool($require)) {
		if($require) {
			$require = 2; //The old 'true' is the new '2'. 
		} else {
			$require = 3; //The old 'false' is the new '3'.
		}
	}
	
	//If we use the general configuration and the configuration say 'yes', or
	//if we say 'yes'	
	if($require == 2 || ($require == 1 && $general) )
		$answer=true;
	return $answer;
}

/**
* Indicates if only registered user can add comments.
*
* @return bool
* @access public
*/
function sk_only_registered_users()
{
	$answer=false;
	$options = get_option('widget_sk');
	$registered = $options['registered'];
	$general = get_option('comment_registration');
	
	if(is_bool($registered)) {
		if($registered) {
			$registered = 2; //The old 'true' is the new '2'. 
		} else {
			$registered = 3; //The old 'false' is the new '3'.
		}
	}
	
	//If we use the general configuration and the configuration say 'yes', or
	//if we say 'yes'	
	if($registered == 2 || ($registered == 1 && $general) )
		$answer=true;
	return $answer;
}

/**
* Returns the avatar using the comment id
*
* @access public
* @param int id Comment's id
* @param int size Size of the avatar image
* @return string The avatar image
*/
function sk_avatar($id, $size) {
	global $wpdb;
	$answer="";
	
	//Get the email, user id and alias for the comment
	$table_name = $wpdb->prefix . "schreikasten";
	$data = $wpdb->get_row("select user_id, alias, email from $table_name where id=$id");
	$alias=$data->alias;
	$email=$data->email;
	$user_id=$data->user_id;
	
	if($user_id>0) { //If user id is greater than 0, it means this is a registered user
		$answer=get_avatar($user_id,$size);
	} else {
		if($email=="") { //If we don't have an email, use the alias
			$answer=get_avatar($alias,$size);
		} else {
			$answer=get_avatar($email,$size); //else, use the email
		}
	}
	return $answer;
}

/**
* Returns HTML for 'page selector' footer
*
* @param int group Which group are we showing?
* @return string
* @access public
*/
function sk_page_selector($group=1) {
	global $wpdb;
	$uri_sk=sk_plugin_url('/content.php?page');
	$answer="";
	$total_groups=3; //We will show only 3 groups
	$style_actual_group="color : #000000;";
	$style_no_actual_group="";
	$first_item= "&#171;";
	$last_item= "&#187;";
	
	//Create nonce
	$nonce = wp_create_nonce('schreikasten');
	
	// Get the number of comments we have
	$table_name = $wpdb->prefix . "schreikasten";
	$sql="SELECT count(*) FROM $table_name WHERE status=".SK_HAM;
	$total = $wpdb->get_var($sql);
	$options = get_option('widget_sk');
	$size=$options['items'];
	
	//Get the number of groups we have
	$groups=ceil($total/$size);
	
	//By default we start with the first group and end with the number of groups
	//With this we define the interval to show
	$group_start=1;
	$group_end=$total_groups;
	
	//A number to determine thye groups to show
	$group_limit=ceil($total_groups/2)-1;

	//If the number of groups is lesser or equar than the number of groups to show
	if($groups<=$total_groups) {
		$group_end=$groups; //The start group is 1,and the end group is the number of groups
	} else {
		if($groups-$group<=$group_limit) {	// If the difference between the total groups 
														// to show and the group we are showing is 
														// lesser or equal to the group limit.
														// It means we are so close to the end so we have to 
														// show the total number of groups at the end and
														// calculate the begin 
			$group_start=$groups-$total_groups+1; //The start group is the groups minus the total groups to show pluss 1 
			$group_end=$groups; // The end group is the number of groups
		} else {
			if($group>$group_limit) { 	// If the group to show is greater than the group limit. 
												// It means we are far away from the begin so we can 
												// show calculate the list and set the group in the middle.
				$group_start=$group-$group_limit; //The start is the group to show minus the group limit
				$group_end=$group+$group_limit; //The end is the group to show plus the group limit
			}
		}
	}
	
	//The timer system (experimental)
	$timer="";
	if($options['refresh']>0) {		
		$timer="\nsk_timer();";
	}

	//If the list doesn't start from 1, create a link to go to the benginig
	if($group_start!=1) {
		$answer.="<a style='cursor : pointer;' onclick=\"
				document.getElementsByName('sk_page')[0].value=1;
				$timer
				mm_get.post('nonce=$nonce&amp;page=1');\">$first_item</a> &#183; ";
	}
	
	//Create the page list and the links
	for($group_id=$group_start; $group_id<=$group_end; $group_id++) {
		$style=$style_no_actual_group;
		if($group_id==$group) {
			$style=$style_actual_group;
		}
		$answer.="<a style='cursor : pointer; $style' onclick=\"
				document.getElementsByName('sk_page')[0].value=$group_id;
				$timer
				mm_get.post('nonce=$nonce&amp;page=$group_id');\">$group_id</a> &#183; ";
	}

	//If the list doesn't finish with the last group, create a link to the end
	if($group_end!=$groups) {
	$answer.="<a style='cursor : pointer;'
			 onclick=\"
			document.getElementsByName('sk_page')[0].value=$groups;
			$timer
			mm_get.post('nonce=$nonce&amp;page=$groups');\">$last_item</a> &#183; ";
	}

	//As every link ends with a line, delete the last one as we don't need it
	$answer = substr($answer,0,-8);
	return "<br/><div id='throbber-page' class='off'><small>$answer</small></div>";
}

/**
* Function to create the database and to add options into WordPress.
* This function should be called by an action.
*
* @access public
*/
function sk_activate()
{
	global $wpdb;
	global $db_version;
	$table_name = $wpdb->prefix . "schreikasten";
	$blacklist_name = $wpdb->prefix . "schreikasten_blacklist";
	$db_version=get_option('sk_db_version');
	switch($db_version) {
	case 1: //SQL code to update from SK1 to SK4
		$sql="ALTER TABLE $table_name ADD user_id int NOT NULL"; 
		$wpdb->query($sql);
		$sql="ALTER TABLE $table_name ADD email tinytext NOT NULL";
		$wpdb->query($sql);
		$sql="ALTER TABLE $table_name CONVERT TO CHARACTER SET utf8"; 
		$wpdb->query($sql);
		$sql = "CREATE TABLE $blacklist_name(
		id bigint(1) NOT NULL AUTO_INCREMENT,
		pc bigint(1) NOT NULL,
		date datetime NOT NULL,
		forever tinyint(4) NOT NULL,
		PRIMARY KEY (id)
		) CHARSET=utf8;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		update_option('sk_db_version', 4);
		break;
	case 2: //SQL code to update from SK2 to SK4
		$sql="ALTER TABLE $table_name ADD reply int NOT NULL"; 
		$wpdb->query($sql);
		$sql="ALTER TABLE $table_name CONVERT TO CHARACTER SET utf8"; 
		$wpdb->query($sql);
		$sql="ALTER TABLE $blacklist_name CONVERT TO CHARACTER SET utf8"; 
		$wpdb->query($sql);
		update_option('sk_db_version', 4);
		break;
	case 3: //SQL code to update from SK3 to SK4
		$sql="ALTER TABLE $table_name CONVERT TO CHARACTER SET utf8"; 
		$wpdb->query($sql);
		$sql="ALTER TABLE $blacklist_name CONVERT TO CHARACTER SET utf8"; 
		$wpdb->query($sql);
		update_option('sk_db_version', 4);
		break;
	case 4: //We are in SK3, so theres nothing we have to do
		break;
	default: //It's a fresh installation, create the table.
		if($wpdb->get_var("show tables like '$table_name'") != $table_name)
		{
			$sql = "CREATE TABLE $table_name(
				id bigint(1) NOT NULL AUTO_INCREMENT,
				alias tinytext NOT NULL,
				text text NOT NULL,
				date datetime NOT NULL,
				ip char(32) NOT NULL,
				status int NOT NULL,
				user_id int NOT NULL,
				email tinytext NOT NULL,
				reply int NOT NULL,
				PRIMARY KEY (id)
			) CHARSET=utf8;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		
		if($wpdb->get_var("show tables like '$blacklist_name'") != $blacklist_name)
		{
			$sql = "CREATE TABLE $blacklist_name(
				id bigint(1) NOT NULL AUTO_INCREMENT,
				pc bigint(1) NOT NULL,
				date datetime NOT NULL,
				forever tinyint(4) NOT NULL,
				PRIMARY KEY (id)
				) CHARSET=utf8;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		add_option('sk_db_version', 4);
		add_option('sk_api_key', '');
		add_option('sk_api_key_accepted', false);
		sk_verify_key( ); //if we have an old sk_api_key, verify it;
		break;
	}
}

/**
* Function to delete a Comment
*
* @param int id Comment's id
* @return bool
* @access public
*/
function sk_delete_comment($id) {
	global $wpdb;
	
	//Delete the comment
	$table_name = $wpdb->prefix . "schreikasten";
	$query = "DELETE FROM " . $table_name ." WHERE id=" . $id;
	$answer1=$wpdb->query( $query );
	return $answer1;
}

/**
* Sends an email to the author of a comment it has a reply.
* Return true if the email was send.
*
* @param int id Comment's id
* @return bool
* @access public
*/
function sk_reply($id) {
	global $wpdb;
	$answer=false;
	
	//Get the 'from' data
	$table_name = $wpdb->prefix . "schreikasten";
	$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
	$from = $wpdb->get_row($query);
	
	//If we have the 'from' data and it is a reply
	if($from->reply!=0) {
		//Get the 'for' data
		$query="SELECT * FROM " . $table_name ." WHERE id=".$from->reply;
		$for = $wpdb->get_row($query);
		
		$website=get_option('blogname');
		$url=get_option('home');
		
		//Create the mail and send it
		if($for->email!="" && $from->status!=SK_SPAM) {
			$email=$for->email;
			$notify_message = sprintf(__('There is a reply to your comment on %s from %s', 'sk'), $website, $from->alias) . "\r\n\r\n";
			$notify_message .= sprintf(__('Your comment : %s', 'sk'), $for->text ) . "\r\n\r\n";
			$notify_message .= sprintf(__('Reply comment: %s', 'sk'), $from->text ). "\r\n\r\n";
			
			$notify_message .= $url;
			
			@wp_mail($email, sprintf(__('An answer to your comment on %s', 'sk'), $website), $notify_message);
			//To not resend the reply, clear the reply column
			$query="UPDATE $table_name SET reply=0 WHERE id=".$id;
			$wpdb->query($query);
			
			$answer=true;
			
		}
	}
	return $answer;
}

/**
* Sends an email to the admin, indicating there is a new comment and if it needs to be moderated.
* Return true if the email was send.
*
* @param int id Comment's id
* @return bool
* @access public
*/
function sk_inform($id) {
	global $wpdb;
	
	//Get the comment data
	$table_name = $wpdb->prefix . "schreikasten";
	$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
	$comment = $wpdb->get_row($query);
	
	$options = get_option('widget_sk');
	$announce = $options['announce'];
	if(!$announce) $announce = SK_ANNOUNCE_CONFIG;
	
	//If it was accepted create the information email
	if($comment->status==SK_HAM) {
		if($announce == SK_ANNOUNCE_YES || ($announce == SK_ANNOUNCE_CONFIG && get_option('comments_notify'))) {
			$admin_email = get_option('admin_email');
			$notify_message=__('There is a new comment on Schreikasten', 'sk') . "\r\n";
			$notify_message.= sprintf( '%s', admin_url("edit-comments.php?page=skmanage&paged=1&mode=edit&id=$id") ) . "\r\n\r\n";
			$notify_message .= sprintf( __('Author : %1$s (IP: %2$s)', 'sk'), $comment->alias, $comment->ip ) . "\r\n";
			if($comment->email!="") $notify_message .= sprintf( __('E-mail : %s', 'sk'), $comment->email ) . "\r\n";
			$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->ip ) . "\r\n";
			$notify_message .= sprintf( __('Comment: %s', 'sk'), $comment->text ) . "\r\n\r\n";							
			$notify_message .= sprintf( __('Delete it: %s', 'sk'), admin_url("edit-comments.php?page=skmanage&paged=1&mode=delete&id=$id") ) . "\r\n";
			$notify_message .= sprintf( __('Reject it: %s', 'sk'), admin_url("edit-comments.php?page=skmanage&paged=1&mode=set_black&id=$id") ) . "\r\n";
			$notify_message .= sprintf( __('Spam it: %s', 'sk'), admin_url("edit-comments.php?page=skmanage&paged=1&mode=set_spam&id=$id") );
								
			@wp_mail($admin_email, __('New comment on Schreikasten', 'sk'), $notify_message);
		}
	}
	
	//If it waits for moderation, create the information email
	if($comment->status==SK_MOOT) {
		if($announce == SK_ANNOUNCE_YES || ($announce == SK_ANNOUNCE_CONFIG && get_option('moderation_notify'))) {
			$admin_email = get_option('admin_email');
			$notify_message=__('A new comment on Schreikasten is waiting for your approval', 'sk') . "\r\n";
			$notify_message.= sprintf( '%s', admin_url("edit-comments.php?page=skmanage&paged=1&mode=edit&id=$id") ) . "\r\n\r\n";
			$notify_message .= sprintf( __('Author : %1$s (IP: %2$s)', 'sk'), $comment->alias, $comment->ip ) . "\r\n";
			if($comment->email!="") $notify_message .= sprintf( __('E-mail : %s', 'sk'), $comment->email ) . "\r\n";
			$notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->ip ) . "\r\n";
			$notify_message .= sprintf( __('Comment: %s', 'sk'), $comment->text ) . "\r\n\r\n";
			$notify_message .= sprintf( __('Approve it: %s', 'sk'), admin_url("edit-comments.php?page=skmanage&paged=1&mode=set_ham&id=$id") ) . "\r\n";
			$notify_message .= sprintf( __('Delete it: %s', 'sk'), admin_url("edit-comments.php?page=skmanage&paged=1&mode=delete&id=$id") ) . "\r\n";
			$notify_message .= sprintf( __('Reject it: %s', 'sk'), admin_url("edit-comments.php?page=skmanage&paged=1&mode=set_black&id=$id") ) . "\r\n";
			$notify_message .= sprintf( __('Spam it: %s', 'sk'), admin_url("edit-comments.php?page=skmanage&paged=1&mode=set_spam&id=$id") );
								
			@wp_mail($admin_email, __('Schreikasten comment to moderate', 'sk'), $notify_message);
		}
	}
	
}

/**
* Function to add a Comment. If Akismet is working ask akismet, else mark as Ham.
*
* @param string alias Who comments?
* @param string text Comment's text
* @param string ip 
* @return bool
* @access public
*/
function sk_add_comment($alias, $email, $text, $ip, $for) {
	global $wpdb;
	global $current_user;
	$options = get_option('widget_sk');
	get_currentuserinfo();
	
	//If the sender is logged use it's internal id
	$user_id=0; //Positive for logged users, negative for cookies, 0 for anonymous
	if($current_user->ID>0) {
		$user_id=$current_user->ID;
	} else { //else, use the cookie
		$user_id=sk_cookie_id();
	}
	
	$answer=false;	
	
	//If we can only accept messages for registered user and this is a registered user
	//or we can accept for not registered users
	//and in general this user can send more messages, accept the comment
	if( ( ( sk_only_registered_users() && $user_id>0 ) || !sk_only_registered_users() ) && !sk_can_not_accept_more_messages($user_id) ) {
	
		$time=current_time('mysql');
		
		//if someone logged or with a cooki sends it
		if($user_id!=0) {
			if(strlen($alias)>0 && strlen($text)>0) { //If we have a name and a text
				$table_name = $wpdb->prefix . "schreikasten";
				$insert = "INSERT INTO " . $table_name .
					" (alias, text, date, ip, status, user_id, email, reply) " .
					"VALUES ('%s', '%s', '%s', '%s', %d, %d, '%s', %d)";
				//Add the comment, mark it to moderate
				$insert = $wpdb->prepare( $insert, $alias, $text, $time, $ip, SK_MOOT, $user_id, $email, $for );
				if($answer = $wpdb->query( $insert ) ) { //If the comment was accepted
					$id = $wpdb->get_var("select last_insert_id()");
					$answer=$id;
					$spam=false; 
					//if(sk_verify_key()) {
					if(get_option('sk_api_key_accepted')) { //If we have a verified key
						$spam=sk_is_spam($id); //Check if this comment is spam
					}
					
					if(!$spam) { //If it is not spam
						//If the owner is not in the blacklist 
						//and we do not require to moderate
						//and it is not an anonymous,
						//accept the message
						if(!sk_is_blacklisted() && 1 != get_option('comment_moderation') && $user_id != 0) {
							//sk_mark_as_ham($id); //accept the message
							$query="UPDATE " . $table_name ." SET status='".SK_HAM."' WHERE id=".$id;
							$wpdb->query( $query );
							sk_reply($id); //send the reply, if it has one
						}
						sk_inform($id); //Inform the administrator
					}
				}
			}
		}
	}
	return $answer;
}

/**
* Update blacklist. Release PC listed with more than the max days accepted.
*
* @access public
*/
function sk_blacklist_update() {
	global $wpdb;
	$options = get_option('widget_sk');
	$days=0;
	
	//get the max number of days to be blacklisted
	if(is_array($options)) {
		$days=$options['bl_days'];
	}
	if($days!=0) {
		$table_name = $wpdb->prefix . "schreikasten_blacklist";
		//Delete any pc blacklisted wich aren't marked to be blacklisted forever 
		//and with a mark older than the days accepted
		$query="DELETE FROM " . $table_name ." WHERE forever=0 AND date <= NOW() - INTERVAL ".$days." DAY";
		$wpdb->query( $query );
	}
}

/**
* Return the id of the block, or false if PC is not blacklisted. Without 
* parameter checks cookies
*
* @param int pc PC to check. Withoput parameter use cookie
* @return int
* @access public
*/
function sk_is_blacklisted($pc=false) {
	global $wpdb;
	global $current_user;
	$answer=false;
	
	//use the current pc id (or user id) if the function didn't get the variable
	if(!$pc) {
		get_currentuserinfo();
		$pc=0;
		if($current_user->ID>0) {
			$pc=$current_user->ID;
		} else {
			$pc=sk_cookie_id();
		}
	}

	//Check if the PC is in the blacklist
	$table_name = $wpdb->prefix . "schreikasten_blacklist";
	$query="SELECT COUNT(*) FROM " . $table_name ." WHERE pc = ".$pc;
	$total=$wpdb->get_var( $query );
	if($total>0) {
			$query="SELECT id FROM " . $table_name ." WHERE pc = ".$pc;
			$answer=$wpdb->get_var( $query );
	}
	return $answer;
}

/**
* Return true if this PC can't send more comments to be accepted
*
* @param int pc PC to check. Withoput parameter use cookie
* @return bool
* @access public
*/
function sk_can_not_accept_more_messages($pc=false) {
	global $wpdb;
	global $current_user;
	$answer=false;
	$options = get_option('widget_sk');
	
	//max number of messages a blacklisted pc can send
	$max=$options['bl_maxpending'];
	
	//use the current pc id (or user id) if the function didn't get the variable
	if(!$pc) {
	get_currentuserinfo();
	$pc=0;
	if($current_user->ID>0) {
				$pc=$current_user->ID;
			} else {
			$pc=sk_cookie_id();
			}
	}
	
	//get the numbr of messages a pc (or user) have pending from moderation
	$table_name = $wpdb->prefix . "schreikasten";
	$query="SELECT COUNT(*) FROM " . $table_name ." WHERE status = ".SK_MOOT." AND user_id = ".$pc;
	$total=$wpdb->get_var( $query );
	
	//if it has more or equal, inform this user can't send more comments
	if($total>=$max) {
		$answer=true;
	}
	return $answer;
}

/**
* Marks comment as spam and send message to Akismet
*
* @param int id Comment's id
* @return bool
* @access public
*/
function sk_mark_as_spam($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$query="UPDATE " . $table_name ." SET status='".SK_SPAM."' WHERE id=".$id;
	$answer1=$wpdb->query( $query );
	
	//if(sk_verify_key()) {
	if(get_option('sk_api_key_accepted')) { //if we have an accepted key
		global $sk_user_agent;
		//Get the data for akismet
		$key=get_option('sk_api_key');
		$blog = urlencode( get_option('home') );
		$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
		$comment = $wpdb->get_row($query);
		$user_agent=$comment->user_agent;
		$ip=$comment->ip;
		$comment_author=$comment->alias;
		$comment_content=$comment->text;
		
		//create the post request
		$path = "blog=$blog&user_ip=$ip&user_agent=$sk_user_agent&comment_author=$comment_author&comment_content=$comment_content";
		//do we have an email?
		if($comment->email!="") {
			$path.="&comment_author_email=".$comment->email;
		}
		//ask and wait for the answer
		$response = sk_http_post($path, $key.'.rest.akismet.com', '/1.1/submit-spam');
	}
	
	return $answer1;
}

/**
* Marks comment as blacklisted
*
* @param int id Comment's id
* @return bool
* @access public
*/
function sk_mark_as_black($id) {
	global $wpdb;
	$answer2=true;
	$table_name = $wpdb->prefix . "schreikasten";
	$blacklist_name = $wpdb->prefix . "schreikasten_blacklist";
	$query="UPDATE " . $table_name ." SET status='".SK_BLACK."' WHERE id=".$id;
	$answer1=$wpdb->query( $query );
	
	//Get PC
	$query="SELECT user_id FROM " . $table_name ." WHERE id=".$id;
	$pc=$wpdb->get_var($query);
	
	//If there is a reference about the PC, do something
	if($pc!=0) {
		$query="SELECT date FROM " . $table_name ." WHERE id=".$id;
		$date=$wpdb->get_var($query);
		//Is pc blacklisted?
		if(sk_is_blacklisted($pc)) {
			//Update the PC so the new blacklist date is the actual date if the blacklist date is older than
			//the one in the list.
			$sql="UPDATE $blacklist_name SET date='$date' WHERE pc=$pc AND date<'$date'";
			$wpdb->query( $sql );
		} else {
			//Add the PC to the blacklist
			$insert = "INSERT INTO " . $blacklist_name .
				" (pc, date, forever) " .
				"VALUES ('$pc', '$date', 0)";
			$answer2 = $wpdb->query( $insert );
		}
	}
	
	return $answer1&&$answer2;
}

/**
* Marks comment as ham and send message to Akismet
*
* @param int id Comment's id
* @return bool
* @access public
*/
function sk_mark_as_ham($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$status = sk_status($id);
	//Change the status
	$query="UPDATE " . $table_name ." SET status='".SK_HAM."' WHERE id=".$id;
	$answer1=$wpdb->query( $query );
	//Send the reply
	sk_reply($id);
	
	// Send HAM mark to Akismet if there is an API key and
	// the comment was marked as SPAM
	if(get_option('sk_api_key_accepted') && $status==SK_SPAM) {
		global $sk_user_agent;
		//Get the data to send to akismet
		$key=get_option('sk_api_key');
		$blog = urlencode( get_option('home') );
		$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
		$comment = $wpdb->get_row($query);
		$user_agent=$comment->user_agent;
		$ip=$comment->ip;
		$comment_author=$comment->alias;
		$comment_content=$comment->text;

		//Create the post request
		$path = "blog=$blog&user_ip=$ip&user_agent=$sk_user_agent&comment_author=$comment_author&comment_content=$comment_content";
		//do we have an email?
		if($comment->email!="") {
			$path.="&comment_author_email=".$comment->email;
		}
		
		//ask and wait for the anser
		$response = sk_http_post($path, $key.'.rest.akismet.com', '/1.1/submit-ham');
	}
	
	return $answer1;
}

/**
* Return comment's status
*
* @param int id Comment's id
* @return bool
* @access public
*/
function sk_status($id) {
	global $wpdb;
	global $sk_user_agent;
	$status=0;
	$table_name = $wpdb->prefix . "schreikasten";
	$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
	$comment = $wpdb->get_row($query);
	if($comment) {
		$status=$comment->status;
	}
	return $status;
}

/**
* Ask Akismet if the comment is spam
*
* @param int id Comment's id
* @return bool
* @access public
*/
function sk_is_spam($id) {
	global $wpdb;
	global $sk_user_agent;
	
	//Get the data for akismet
	$key=get_option('sk_api_key');
	$blog = urlencode( get_option('home') );
	$table_name = $wpdb->prefix . "schreikasten";
	$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
	$comment = $wpdb->get_row($query);
	$user_agent=$comment->user_agent;
	$ip=$comment->ip;
	$comment_author=$comment->alias;
	$comment_content=$comment->text;
	
	//Create the post request
	$path = "blog=$blog&user_ip=$ip&user_agent=$sk_user_agent&comment_author=$comment_author&comment_content=$comment_content";
	//do we have an email?
	if($comment->email!="") {
		$path.="&comment_author_email=".$comment->email;
	}
	
	//ask and wait for the answer
	$response = sk_http_post($path, $key.'.rest.akismet.com', '/1.1/comment-check');
	$answer=true;

	//If the comment isn't spam, all righty then
	if ( 'false' == $response[1] ) {
		$answer=false;
	} else { //SPAM!!!!! Mark the comment as spam now!!!!!!
		$query="UPDATE " . $table_name ." SET status=".SK_SPAM." WHERE id=".$id;
		$wpdb->query( $query );
	}
	return $answer;
}

/**
* Deletes all comments marked as spam.
*
* @access public
*/
function sk_delete_spam() {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$sql = "DELETE FROM " . $table_name ." WHERE status=" . SK_SPAM;
	$wpdb->query( $sql );
}

/**
* How many elements in list? Without argument return total number of comments. You can use
* SK_HAM for accpeted, SK_SPAM for spam, and SK_MOOT for comments to be moderated.
*
* @param int status
* @return int
* @access public
*/
function sk_count($status=false) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$sql="SELECT count(*) FROM " . $table_name; 
	if($status)
		$sql.=" WHERE status=$status";
	$total = $wpdb->get_var($sql);
	return $total;
}

/**
* Edit comment
*
* @param int id Comment's id
* @param string alias Who send the comment
* @param string text Comment's content
* @access public
*/
function sk_edit_comment($id, $alias, $email, $text) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$query="UPDATE " . $table_name ." SET alias='".$alias."', email= '".$email."', text='".$text."' WHERE id=".$id;
	$wpdb->query($query);
}

/**
* Lock a PC forever
*
* @param int id block's id
* @access public
*/
function sk_lock_forever($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten_blacklist";
	$query="UPDATE " . $table_name ." SET forever=1 WHERE id=".$id;
	return $wpdb->query($query);
}

/**
* Unlock a PC
*
* @param int id block's id
* @access public
*/
function sk_unlock($id) {	
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten_blacklist";
	$query="DELETE FROM " . $table_name ." WHERE id=".$id;
	return $wpdb->query($query);
}

/**
* Format a comment to be displayed in the shoutbox
*
* @access public
* @param objetc comment The comment
* @param sending Is this the comment we are sending?
*/
function sk_format_comment($comment,$sending=false) {
	global $current_user;
	
	$answer = "";
	$for="";
	$av_size=32;
	
	get_currentuserinfo();
	
	//check if this user can administrate
	$sk_canmannage=false;
	if($current_user) {
		$capabilities=$current_user->wp_capabilities;
		if($capabilities['administrator']) {
			$sk_canmannage=true;
		}
	}
	
	$options = get_option('widget_sk');
	
	//If we can reply a message, create the reply system 
	if($options['replies']) {
		$for=" ";
		if(!$sending) {
			if($comment->email!="") {
				$for.="<a href='#sk_top' onclick='javascript:for_set(".$comment->id.", \"".$comment->alias."\");'> ".__("[reply]","sk")."</a>";
			} else {
				$for.="<span class='sk-for'>".__("[no sender]", "sk")."</span>";
			}
		}
	}
	
	//The classes for coloring the comments (used only if we are sending this comment)
	$class="sk-userdata-user";
	$divClass='sk-comment';
	if($comment->status==SK_SPAM) {
		$divClass.='-spam';
		$class.="-spam";
	}
	
	if($comment->status==SK_MOOT) {
		$divClass.='-moot';
		$class.="-moot";
	}
		
	$mannage = "";
	//If the user can mannage, set the mannage system inside the comment
	if($sk_canmannage) {
		$class="sk-userdata-admin";
		if($comment->status==SK_SPAM) {
			$class.="-spam";
		}
		if($comment->status==SK_MOOT) {
			$class.="-moot";
		}
		$av_size=41;
		$id=$comment->id;
		$edit="<a href='".htmlspecialchars(admin_url("edit-comments.php?page=skmanage&paged=1&mode=edit&id=$id"))."'>" . __('[edit]' , 'sk') . "</a>";
		$mannage.="<br/><a href='".htmlspecialchars(admin_url("edit-comments.php?page=skmanage&paged=1&mode=delete&id=$id"))."'>" . __('Delete' , 'sk') . "</a> | ";
		if($comment->user_id!=0) {
			$mannage.="<a href='".htmlspecialchars(admin_url("edit-comments.php?page=skmanage&paged=1&mode=set_black&id=$id"))."'>". __('Reject', 'sk') . "</a> | ";
		}
		$mannage.="<a href='".htmlspecialchars(admin_url("edit-comments.php?page=skmanage&paged=1&mode=set_spam&id=$id"))."'>". __('Spam', 'sk') . "</a><br/>";
		if($sending) {
			$mannage="<br/>";
			$edit = "[ ".__('Sending', 'sk')." ]";
		}
	}
	
	$avatar="";
	$item="";
	
	//Get the avatar using the specific size
	if($options['avatar']) {
		$avatar=sk_avatar($comment->id,$av_size);
	}
	$comment_text=apply_filters('comment_text', $comment->text);
	$comment_text=str_replace("<p>", "", $comment_text);
	$comment_text=str_replace("</p>", "", $comment_text);
	
	//Create the comment text
	$item.="<div style='min-height: ".$av_size."px;' class='$class'>".
			$avatar.
			"<strong>".$comment->alias."</strong>
			<br/><div class='sk-little'>(".$comment->date.")$mannage</div>
		</div>
		<div class='sk-widgettext'>
			<div class='skwidget-comment'>".$comment_text."</div>
			<div class='skwidget-edit'>$for $edit</div>
			<div style='clear: both;'></div>
		</div>";
	
	//if we show avatars, use the images
	if($options['avatar']) {
		$answer.="\n<div class='$divClass'>
		$item
		</div>";
	} else { //else, it's a list item
		$answer.="\n<li class='$divClass'>
				$item
				</li>";
	}
	return $answer;
}

/**
* Returns HTML for contents to show
*
* @param int page Which group are we showing?
* @param int id If this comment is marked as SPAM show it 
* @return string
* @access public
*/

function sk_show_comments($page=1,$id=false)
{
	global $wpdb;
	
	$options = get_option('widget_sk');
	$size=$options['items'];
	$first=(($page-1)*$size);
	$table_name = $wpdb->prefix . "schreikasten";
	
	//Get the comments to show
	$sql="SELECT id, alias, text, date, user_id, email, status FROM $table_name WHERE status=".SK_HAM." ORDER BY id desc LIMIT $first, $size";
	$comments = $wpdb->get_results($sql);

	//if we don't show avatars, then it's a list	
	if(!$options['avatar']) $answer="<ul>";
	
	$av_size=32;
	
	//The throbber div
	
	//Create the throbber div
	$aux = "";
	$aux->alias = "<span id='th_sk_alias'></span>";
	$aux->text = "<span id='th_sk_text'></span>";
	$aux->date = "&nbsp;".__('Sending', 'sk')."...&nbsp;";
	
	$answer= "<div id='throbber-img' class='off' style='visibility: hidden;'>".sk_format_comment($aux,true)."</div>";

	//If there is and id, it means we have to show it, so, get the comment
	if($id) {
		$sql="SELECT id, alias, text, date, user_id, email, status FROM $table_name WHERE id=$id";
		$idComments = $wpdb->get_results($sql);
		foreach($idComments as $idComment) {
			//Set the data the page format
			$idComments->date = mysql2date(get_option('date_format'), $idComments->date)." ".mysql2date(get_option('date_time'), $idComments->date);
			//If it's spam add it at the begginning.
			if($idComment->status==SK_SPAM || $idComment->status==SK_MOOT) {
				array_unshift($comments, $idComment);
			}
		}
	}

	//The comments list
	foreach($comments as $comment) {
		//Set the data the page format
		$comment->date = mysql2date(get_option('date_format'), $comment->date)." ".mysql2date(get_option('time_format'), $comment->date);
		$answer.=sk_format_comment($comment);
	}
	
	//If we don't show avatars, it's a list 
	if(!$options['avatar']) $answer.="</ul>";
	$answer.="\n";
	
	return $answer;
}

/**
* Enable menus.
* This function should be called by an action.
*
* @access public
*/
function sk_menus()
{
	global $submenu;
	add_submenu_page('edit-comments.php', 'Schreikasten', 'Schreikasten', 10, 'skmanage', 'sk_manage' );
	add_submenu_page('plugins.php', __('Schreikasten Configuration', 'sk'), __('Schreikasten Configuration', 'sk'), 10, 'skconfig', 'sk_config');
}

/**
* Configuration page
* This function should be called by an action.
*
* @access public
*/
function sk_config() {
	$messages=array();
	$mode_x=$_POST['mode_x'];
	$mode=$_GET['mode'];
	switch($mode_x) {
		case 'api_x': //We have to update the api key
			$api_key=$_POST["sk_api_key"];
			update_option('sk_api_key', $api_key);
			$mode='done';
			break;
	}

	$sk_api_key=get_option('sk_api_key');
	// Now display the options 
	include('templates/sk_config.php');

}

/**
* Manage page
*
* @access private
*/
function sk_manage() {
	global $wpdb;
	$select=SK_NOT_FILTERED;
	if($_GET['filter']=='spam') $select=SK_SPAM;
	if($_GET['filter']=='ham') $select=SK_HAM;
	if($_GET['filter']=='moot') $select=SK_MOOT;
	if($_GET['filter']=='black') $select=SK_BLACK;
	if($_GET['filter']=='blocked') $select=SK_BLOCKED;
	$table_name = $wpdb->prefix . "schreikasten";
	$table_list = $wpdb->prefix . "schreikasten_blacklist";
	$messages=array();
	
	//If we don't have minimax, ask the user for it
	if(!function_exists('minimax_version') && minimax_version()<0.3) { 
		array_push($messages, sprintf(__('You have to install <a href="%s" target="_BLANK">minimax 0.3</a> in order for this plugin to work', 'lexi'), "http://wordpress.org/extend/plugins/minimax/" ));
	}

	$mode_x=$_POST['mode_x'];
	if(!$mode_x)
		$mode_x=$_GET['mode_x'];
	$mode=$_GET['mode'];
	
	//if pressed deletespam, delete all spam
	if($_POST['deletespam']) {
		$mode_x='deletespam';
	}
	
	// Assume we don't have to do any action, but ask if we have
	$doaction=false;
	if($_POST['doaction']!="") $doaction=$_POST['action'];
	if($_POST['doaction2']!="") $doaction=$_POST['action2'];
	
	//In case we have to do something previous
	if($doaction)
	{
		if(is_array($_POST['checked_comments'])) {
			switch($doaction)
			{
				case 'approve': //approve the list of checked comments
					foreach($_POST['checked_comments'] as $checked_id) {
						sk_mark_as_ham($checked_id);
					}
					break;
				case 'markspam': //mark as spam the list of checked comments
					foreach($_POST['checked_comments'] as $checked_id) {
						sk_mark_as_spam($checked_id);
					}
					break;
				case 'markblack': //mark as black the list of checked comments
					foreach($_POST['checked_comments'] as $checked_id) {
						sk_mark_as_black($checked_id);
					}
					break;
				case 'delete': //delete the list of checked comments
					foreach($_POST['checked_comments'] as $checked_id) {
						sk_delete_comment($checked_id);
					}
					break;
			}
		}
		
		if(is_array($_POST['checked_pcs'])) {
			switch($doaction)
			{
				case 'forever': //mark to be blocked forever the list of checked comments	
					foreach($_POST['checked_pcs'] as $checked_id) {
						sk_lock_forever($checked_id);
					}
					break;
				case 'unlock': //mark to be unblocked the list of checked comments
					foreach($_POST['checked_pcs'] as $checked_id) {
						sk_unlock($checked_id);
					}
					break;
			}
		}
	}
	
	$sk_api_key=get_option('sk_api_key');
	$sk_manage_page='skmanage';
	switch($select) {
	case SK_MOOT:
		$sk_manage_page='skmanagemoot';
		break;
	case SK_HAM:
		$sk_manage_page='skmanageham';
		break;
	case SK_SPAM:
		$sk_manage_page='skmanagespam';
		break;
	case SK_BLACK:
		$sk_manage_page='skmanageblack';
		break;
	case SK_BLOCKED:
		$sk_manage_page='skmanageblocked';
		break;
	}

	//if we are going to execute a command previous to show the form
	switch($mode_x) {
		case 'edit_x': //edit the comment
		case 'tedit_x': //edit a tracked comment
			$mode='done';
			if($mode_x=='tedit_x') {
					$mode="tracking";
			}
			if($_POST['submit']) {
				//get the data from the form
				$id=$_POST['sk_id'];
				$alias=$_POST['sk_alias'];
				$email=$_POST['sk_email'];
				$comment=$_POST['sk_comment'];
				
				//edit the comment
				sk_edit_comment($id, $alias, $email, $comment);
				
				$newstatus=$_POST['comment_status'];
				$actstatus=sk_status($id);
				
				//If we change the status
				if($newstatus!=$actstatus) {
					switch($newstatus) {
						case SK_HAM:
							sk_mark_as_ham($id);
							break;
						case SK_SPAM:
							sk_mark_as_spam($id);
							break;
						case SK_BLACK:
							sk_mark_as_black($id);
							break;
					}
					//Message to indicate we change the status
					array_push($messages, __("Status changed",'sk'));
				}
				//Message to indicate we edited a comment
				array_push($messages, __( 'Comment modified', 'sk' ));
			}
			break;
		case 'set_ham_x': //set as ham
			$id=$_GET['id'];
			if(sk_mark_as_ham($id)) array_push($messages, __("Status changed",'sk'));
			break;
		case 'set_black_x': //set as blacklisted
			$id=$_GET['id'];
			if(sk_mark_as_black($id)) array_push($messages, __("Status changed",'sk'));
			break;
		case 'set_spam_x': //set as spam
			$id=$_GET['id'];
			if(sk_mark_as_spam($id)) array_push($messages, __("Status changed",'sk'));
			break;
		case 'delete_x': //delete the comment
			$id=$_GET['id'];
			if(sk_delete_comment($id)) array_push($messages, __("Comment deleted",'sk'));
			break;
		case 'forever_lock_x': //block forever
			$id=$_GET['id'];
			if(sk_lock_forever($id)) array_push($messages, __("PC locked forever",'sk'));
			break;
		case 'unlock_x': //unlock the PC
			$id=$_GET['id'];
			if(sk_unlock($id)) array_push($messages, __("PC unlocked",'sk'));
			break;
		case 'lock_x': //lock the PC
			$id=$_GET['id'];
			sk_mark_as_black($id);
			array_push($messages, __("PC locked",'sk'));
			break;
		case 'deletespam_x': //delete all the spam
			sk_delete_spam();
			break;
	}
	
	//show the form
	switch($mode) {
		case 'tedit': //edit a comment or a tracking comment
		case 'edit':
			$id=$_GET['id'];
			$table_name = $wpdb->prefix . "schreikasten";
			$data = $wpdb->get_row("select alias, text, status, date, email from $table_name where id=$id");
			if($data) { //get data
				$alias=$data->alias;
				$email=$data->email;
				$comment=$data->text;
				$status=$data->status;
				$date=$data->date;
				//show form
				include('templates/sk_comment.php');
			} else {
				$mode='done';
			}
			break;
		case 'tracking':
			$tid=$_GET['tid'];
			$table_name = $wpdb->prefix . "schreikasten";
			$data = $wpdb->get_row("select * from $table_name where id=$tid");
			//get the tracking data
			if($data->id) {
				//show form
				include('templates/sk_tracking.php');
			} else {
				$mode='done';
			}
			break;
		case 'delete':
		case 'set_ham':
		case 'set_black':
		case 'set_spam': //show the form to manage the comments
			$id=$_GET['id'];
			$table_name = $wpdb->prefix . "schreikasten";
			$data = $wpdb->get_row("select alias, text, status, date, email from $table_name where id=$id");
			if($data) {
				$alias=$data->alias;
				$email=$data->email;
				$comment=$data->text;
				$status=$data->status;
				$date=$data->date;
				include('templates/sk_confirm.php');
			} else {
				$mode='done';
			}
			break;
		default:
			if(count($messages)>0) 
			{
				echo "<div class='updated'>";
				foreach($messages as $message) echo "<p><strong>$message</strong></p>";
				echo "</div>";
			}
			
			if($select==SK_BLOCKED)
				include('templates/sk_manageblack.php');
			else
				include('templates/sk_manage.php');
			
			break;
	}
	
}

/**
* Function to show the shoutbox. Can be used in the template files.
*
* @access private
*/
function sk_shoutbox() {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	include('templates/sk_widget.php');
}

// sk widget stuff
function sk_widget_init() {

	if ( !function_exists('register_sidebar_widget') )
		return;

	function sk_widget($args) {

		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);
		
		$options = get_option('widget_sk');
		$title = $options['title'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		sk_shoutbox();
		echo $after_widget;
	}

	function sk_widget_control() {
		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_sk');
		
		if ( !is_array($options) ) {
			$options = array('title'=>'', 'registered'=>false, 'avatar'=>true, 'replies'=>false, 'alert_about_emails'=>true, 'items'=>'5', 'refresh'=>0, 'bl_days'=>'7', 'bl_maxpending'=>'2', 'announce'=>'1', 'requiremail'=>'1', 'maxchars'=>'225');
			
		}
		
		//Max characters
		if(!isset($options['maxchars'])) $options['maxchars']=255;
		
		if(!function_exists('minimax_version') || minimax_version()<0.3) { ?>
		<p>
			<label>
				<?php printf(__('You have to install <a href="%s" target="_BLANK">minimax 0.3</a> in order for this plugin to work', 'sk'), "http://wordpress.org/extend/plugins/minimax/" ); ?>
			</label>
		</p><?
		} else {
		
			if ( $_POST['sk-submit'] ) {
				// Remember to sanitize and format use input appropriately.
				$options['title'] = strip_tags(stripslashes($_POST['sk_title']));
				
				$options['maxchars'] = $_POST['sk_maxchars'];
				if(!is_numeric($options['maxchars'])) $options['maxchars'] = 225;
				
				$options['items'] = $_POST['sk_items'];
				if(!is_numeric($options['items'])) $options['items'] = 5;
				
				$options['avatar'] = false;
				if($_POST['sk_avatar'])
					$options['avatar'] = true;
				
				$options['replies'] = false;
				if($_POST['sk_replies'])
					$options['replies'] = true;
				
				$options['alert_about_emails'] = false;
				if($_POST['sk_alert_about_emails'])
					$options['alert_about_emails'] = true;
					
				$options['refresh'] = $_POST['sk_refresh'];
				$options['bl_days'] = $_POST['sk_bl_days'];
				$options['bl_maxpending'] = $_POST['sk_bl_maxpending'];
				
				$options['registered'] = $_POST['sk_registered'];
				$options['announce'] = $_POST['sk_announce'];
				$options['requiremail'] = $_POST['sk_requiremail'];
				
				update_option('widget_sk', $options);
			}
			// Be sure you format your options to be valid HTML attributes.
			$title = htmlspecialchars($options['title'], ENT_QUOTES);

			// Here is our little form segment. Notice that we don't need a
			// complete form. This will be embedded into the existing form.
			$items=$options['items'];
			$maxchars=$options['maxchars'];
	
			$status=$options['avatar'];
			$registered=$options['registered'];
			$replies=$options['replies'];
			$alert_about_emails=$options['alert_about_emails'];
			
			$refresh="selectedrefresh".$options['refresh'];
			$$refresh=' selected="selected"';
			
			$days="selecteddays".$options['bl_days'];
			$$days=' selected="selected"';
			
			$maxpending="selectedmaxpending".$options['bl_maxpending'];
			$$maxpending=' selected="selected"';
			
			$registered="registered".$options['registered'];
			$$registered=' selected="selected"';
			
			$require="require".$options['requiremail'];
			$$require=' selected="selected"';
			
			$announce="announce".$options['announce'];
			$$announce=' selected="selected"';
			
			require("templates/sk_widgetconfig.php");
		}
	}

	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget(array('Schreikasten', 'widgets'), 'sk_widget');
	
	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	register_widget_control(array('schreikasten', 'widgets'), 'sk_widget_control', 250, 100);

}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'sk_widget_init');

?>
