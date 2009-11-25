<?php
/*
Plugin Name: Schreikasten
Plugin URI: http://www.sebaxtian.com/acerca-de/schreikasten
Description: A shoutbox using ajax and akismet.
Version: 0.10
Author: Juan Sebastián Echeverry
Author URI: http://www.sebaxtian.com
*/

/*  Copyright 2008-2009  Sebaxtian  (email : sebaxtian@gawab.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define ("SK_NOT_FILTERED", 0);
define ("SK_HAM", 1);
define ("SK_SPAM", 2);
define ("SK_MOOT", 3);
define ("SK_BLACK", 4);
define ("SK_BLOCKED", -1);

$db_version=get_option('sk_db_version');
$sk_user_agent = "WordPress/$wp_version | Schreikasten/0.1";

add_action('init', 'sk_textdomain');
add_action('init', 'sk_cookieID');
add_action('wp_head', 'sk_header');
add_action('admin_menu', 'sk_menus');
add_action('activate_schreikasten/schreikasten.php', 'sk_activate');

/**
  * Sends a POST request
  *
  * @param string request
  * @param string host
  * @param string path
  * @param int port
  * @return string
  * @access public
  */
	
function sk_header() {
	echo "<link rel='stylesheet' href='".sk_plugin_url('/css/schreikasten.css')."' type='text/css' media='screen' />";
}

function sk_cookieID() {
	$answer=0;
	if(!$_COOKIE['sk-id']) {
		@setcookie("sk-id", (mt_rand())*-1, time()+3600*24*365*10,"/","");
	} else {
		$answer=$_COOKIE['sk-id'];
	}
	
	return $answer;
}

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
  * Function to use i18n
  *  
  * @access public
  */

function sk_textdomain() {
	load_plugin_textdomain('sk', 'wp-content/plugins/schreikasten/lang');
}

/**
  * Returns plugin's path.
  *
  * @param string str Path to append
  * @return string
  * @access public
  */

function sk_plugin_url($str = '')
{
	$dir_name = '/wp-content/plugins/schreikasten';
	$url=get_bloginfo('wpurl');
	return $url . $dir_name . $str;
}

/**
	* Indicates if only registered user can add comments.
	*
	* @return bool
	* @access public
	*/

function sk_onlyRegistered()
{
	$answer=false;
	$options = get_option('widget_sk');	
	if($options['registered']==1)
		$answer=true;
	return $answer;
}

function sk_avatar($id, $size) {
	global $wpdb;
	$answer="";
	$table_name = $wpdb->prefix . "schreikasten";
	$data = $wpdb->get_row("select user_id, alias, email from $table_name where id=$id");
	$alias=$data->alias;
	$email=$data->email;
	$user_id=$data->user_id;
		
	if($user_id>0) {
		$answer=get_avatar($user_id,$size);
	} else {
		if($email=="") {
			$answer=get_avatar($alias,$size);
		} else {
			$answer=get_avatar($email,$size);
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
	$total_groups=3;
	$style_actual_group="color : #000000;";
	$style_no_actual_group="";
	$first_item= "&#171;";
	$last_item= "&#187;";
	
	$table_name = $wpdb->prefix . "schreikasten";
	$sql="SELECT count(*) FROM $table_name WHERE status=".SK_HAM;
	$total = $wpdb->get_var($sql);
	$options = get_option('widget_sk');
	$size=$options['items'];
	
	$groups=ceil($total/$size);
	
	$group_start=1;
	$group_end=$total_groups;
	
	$group_limit=ceil($total_groups/2)-1;

	if($groups<=$total_groups) {
		$group_end=$groups;
	} else {
		if($groups-$group<=$group_limit) {
			$group_start=$groups-$total_groups+1;
			$group_end=$groups;
		} else {
			if($group>$group_limit) {
				$group_start=$group-$group_limit;
				$group_end=$group+$group_limit;
			}
		}
	}
	
	$timer="";
	if($options['refresh']>0) {		
		$timer="\nsk_timer();";
	}

	if($group_start!=1) {
		$answer.="<a style='cursor : pointer;' onclick=\"
				document.getElementsByName('sk_page')[0].value=1;
				$timer
				mm_get.post('page=1');\">$first_item</a> &#183; ";
	}

	for($group_id=$group_start; $group_id<=$group_end; $group_id++) {
		$style=$style_no_actual_group;
		if($group_id==$group) {
			$style=$style_actual_group;
		}
		$answer.="<a style='cursor : pointer; $style' onclick=\"
				document.getElementsByName('sk_page')[0].value=$group_id;
				$timer
				mm_get.post('page=$group_id');\">$group_id</a> &#183; ";
	}

	if($group_end!=$groups) {
	$answer.="<a style='cursor : pointer;'
			 onclick=\"
			document.getElementsByName('sk_page')[0].value=$groups;
			$timer
			mm_get.post('page=$groups');\">$last_item</a> &#183; ";
	}

	$answer = substr($answer,0,-8);
	return "<br/><div id='trobbling-page' class='off'><small>$answer</small></div>";
}

/**
  * Function to create the database and to add options into WordPress
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
	case 1:
		$sql="ALTER TABLE $table_name ADD user_id int NOT NULL"; 
		$wpdb->query($sql);
		$sql="ALTER TABLE $table_name ADD email tinytext NOT NULL";
		$wpdb->query($sql);
		$sql = "CREATE TABLE $blacklist_name(
		id bigint(1) NOT NULL AUTO_INCREMENT,
		pc bigint(1) NOT NULL,
		date datetime NOT NULL,
		forever tinyint(4) NOT NULL,
		PRIMARY KEY (id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		update_option('sk_db_version', 3);
		break;
	case 2:
		$sql="ALTER TABLE $table_name ADD reply int NOT NULL"; 
		$wpdb->query($sql);
		update_option('sk_db_version', 3);
		break;
	case 3:
		break;
	default:
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
			);";
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
				);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		add_option('sk_db_version', 3);
		add_option('sk_api_key', '');
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

function sk_deleteComment($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$query = "DELETE FROM " . $table_name ." WHERE id=" . $id;
	$answer1=$wpdb->query( $query );
	return $answer1;
}

function sk_reply($id) {
	global $wpdb;
	$answer=false;
	$table_name = $wpdb->prefix . "schreikasten";
	$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
	$from = $wpdb->get_row($query);
	
	if($from->reply!=0) {
	
		$query="SELECT * FROM " . $table_name ." WHERE id=".$from->reply;
		$for = $wpdb->get_row($query);
		
		$website=get_option('blogname');
		$url=get_option('home');
		
		if($for->email!="" && $from->status!=SK_SPAM) {
			$email=$for->email;
			$notify_message  = sprintf(__('There is a reply to your comment on %s from %s', 'sk'), $website, $from->alias) . "\r\n\r\n";
			$notify_message .= sprintf(__('Your comment : %s', 'sk'), $for->text ) . "\r\n\r\n";
			$notify_message .= sprintf(__('Reply comment: %s', 'sk'), $from->text ). "\r\n\r\n";
			
			$notify_message .= $url;
			
			@wp_mail($email, sprintf(__('An answer to your comment on %s', 'sk'), $website), $notify_message);
			$query="UPDATE $table_name SET reply=0 WHERE id=".$id;
			$wpdb->query($query);
			
			$answer=true;
			
		}
	}
	return $answer;
}

function sk_inform($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
	$comment = $wpdb->get_row($query);
	
	if($comment->status==SK_HAM) {
		if(get_option('comments_notify')) {
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
	
	if($comment->status==SK_MOOT) {
		if(get_option('moderation_notify')) {
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

function sk_addComment($alias, $email, $text, $ip, $for)
{
	global $wpdb;
	global $current_user;
	$options = get_option('widget_sk');
	get_currentuserinfo();
	
	$user_id=0;
	if($current_user->ID>0) {
		$user_id=$current_user->ID;
	} else {
		$user_id=sk_cookieID();
	}
	
	$answer=false;
	
	$time=current_time('mysql');
	
	if($user_id!=0) {
		if(strlen($alias)>0 && strlen($text)>0) {
			$table_name = $wpdb->prefix . "schreikasten";
			$insert = "INSERT INTO " . $table_name .
				" (alias, text, date, ip, status, user_id, email, reply) " .
				"VALUES ('$alias', '$text', '$time', '$ip', ".SK_MOOT.", $user_id, '$email', $for)";
			if($answer = $wpdb->query( $insert )) {
				$id = $wpdb->get_var("select last_insert_id()");
				$answer=$id;
				$spam=false;
				if(sk_verify_key()) {
					$spam=sk_isSpam($id);
				}
				
				if(!$spam) {
					if(!sk_isBlacklisted() && 1 != get_option('comment_moderation') && $user_id != 0) {
						sk_markHam($id);
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

function sk_updateBlacklist() {
	global $wpdb;
	$options = get_option('widget_sk');
	$days=0;
	if(is_array($options)) {
		$days=$options['bl_days'];
	}
	if($days!=0) {
		$table_name = $wpdb->prefix . "schreikasten_blacklist";
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

function sk_isBlacklisted($pc=false) {
	global $wpdb;
	global $current_user;
	$answer=false;
	
	if(!$pc) {
		get_currentuserinfo();
		$pc=0;
		if($current_user->ID>0) {
			$pc=$current_user->ID;
		} else {
			$pc=sk_cookieID();
		}
	}

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
	* Return true if PC can's send  more comments to be accepted
	*
	* @param int pc PC to check. Withoput parameter use cookie
	* @return bool
	* @access public
	*/

function sk_noMoreMessages2Accept($pc=false) {
	global $wpdb;
	global $current_user;
	$answer=false;
	$options = get_option('widget_sk');
	$max=$options['bl_maxpending'];
	
	if(!$pc) {
	get_currentuserinfo();
	$pc=0;
	if($current_user->ID>0) {
				$pc=$current_user->ID;
			} else {
			$pc=sk_cookieID();
			}
	}
	
	$table_name = $wpdb->prefix . "schreikasten";
	$query="SELECT COUNT(*) FROM " . $table_name ." WHERE status = ".SK_MOOT." AND user_id = ".$pc;
	$total=$wpdb->get_var( $query );
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

function sk_markSpam($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$query="UPDATE " . $table_name ." SET status='".SK_SPAM."' WHERE id=".$id;
	$answer1=$wpdb->query( $query );
	
	if(sk_verify_key()) {
		global $sk_user_agent;
		$key=get_option('sk_api_key');
		$blog = urlencode( get_option('home') );
		$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
		$comment = $wpdb->get_row($query);
		$user_agent=$comment->user_agent;
		$ip=$comment->ip;
		$comment_author=$comment->alias;
		$comment_content=$comment->text;
		$path = "blog=$blog&user_ip=$ip&user_agent=$sk_user_agent&comment_author=$comment_author&comment_content=$comment_content";
		if($comment->email!="") {
			$path.="&comment_author_email=".$comment->email;
		}
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

function sk_markBlack($id) {
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
		if(sk_isBlacklisted($pc)) {
			$sql="UPDATE $blacklist_name SET date='$date' WHERE pc=$pc AND date<'$date'";
			$wpdb->query( $sql );
		} else {
			//Get date
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

function sk_markHam($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$status = sk_status($id);
	$query="UPDATE " . $table_name ." SET status='".SK_HAM."' WHERE id=".$id;
	$answer1=$wpdb->query( $query );
	sk_reply($id);
	
	// Send SPAM mark to Akismet if there is an API key and
	// the comment was marked as SPAM
	if(sk_verify_key() && $status==SK_SPAM) {
		global $sk_user_agent;
		$key=get_option('sk_api_key');
		$blog = urlencode( get_option('home') );
		$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
		$comment = $wpdb->get_row($query);
		$user_agent=$comment->user_agent;
		$ip=$comment->ip;
		$comment_author=$comment->alias;
		$comment_content=$comment->text;
		$path = "blog=$blog&user_ip=$ip&user_agent=$sk_user_agent&comment_author=$comment_author&comment_content=$comment_content";
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

function sk_isSpam($id) {
	global $wpdb;
	global $sk_user_agent;
	$key=get_option('sk_api_key');
	$blog = urlencode( get_option('home') );
	$table_name = $wpdb->prefix . "schreikasten";
	$query="SELECT * FROM " . $table_name ." WHERE id=".$id;
	$comment = $wpdb->get_row($query);
	$user_agent=$comment->user_agent;
	$ip=$comment->ip;
	$comment_author=$comment->alias;
	$comment_content=$comment->text;
	$path = "blog=$blog&user_ip=$ip&user_agent=$sk_user_agent&comment_author=$comment_author&comment_content=$comment_content";
	$response = sk_http_post($path, $key.'.rest.akismet.com', '/1.1/comment-check');
	$answer=true;
	if ( 'false' == $response[1] ) {
		$answer=false;
	} else {
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

function sk_deleteSpam() {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$sql = "DELETE FROM " . $table_name ." WHERE status=" . SK_SPAM;
	$wpdb->query( $sql );
}

/**
  * How many elements in list? With argument return the numbers in
	* with status, without argument return total number of comments.
  *
	* @param int status
  * @return int
  * @access public
  */

function sk_count($status=false) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$sql="SELECT  count(*) FROM " . $table_name; 
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

function sk_editComment($id, $alias, $email, $text) {
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

function sk_foreverLock($id) {
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

function sk_formatComment($comment,$throbbler=false) {
	global $current_user;
	
	$answer = "";
	$for="";
	$av_size=32;
	
	get_currentuserinfo();
	
	$sk_canmannage=false;
	
	if($current_user) {
		$capabilities=$current_user->wp_capabilities;
		if($capabilities['administrator']) {
			$sk_canmannage=true;
		}
	}
	
	$options = get_option('widget_sk');
	if($options['replies']) {
		$for=" ";
		if(!$throbbler) {
			if($comment->email!="") {
				$for.="<a href='#sk_top' onclick='javascript:for_set(".$comment->id.", \"".$comment->alias."\");'> ".__("[reply]","sk")."</a>";
			} else {
				$for.="<span class='sk-for'>".__("[no sender]", "sk")."</span>";
			}
		}
	}
	
	$class="sk-userdata-user";
	$mannage = "";
	if($sk_canmannage) {
		$class="sk-userdata-admin";
		$av_size=41;
		$id=$comment->id;
		$edit="<a href='".htmlspecialchars(admin_url("edit-comments.php?page=skmanage&paged=1&mode=edit&id=$id"))."'>" . __('[edit]' , 'sk') . "</a>";
		$mannage.="<br/><a href='".htmlspecialchars(admin_url("edit-comments.php?page=skmanage&paged=1&mode=delete&id=$id"))."'>" . __('Delete' , 'sk') . "</a> | ";
		if($comment->user_id!=0) {
			$mannage.="<a href='".htmlspecialchars(admin_url("edit-comments.php?page=skmanage&paged=1&mode=set_black&id=$id"))."'>". __('Reject', 'sk') . "</a> | ";
		}
		$mannage.="<a href='".htmlspecialchars(admin_url("edit-comments.php?page=skmanage&paged=1&mode=set_spam&id=$id"))."'>".  __('Spam', 'sk') . "</a><br/>";
		if($throbbler) {
			$mannage="<br/>";
			$edit = "[ ".__('Sending', 'sk')." ]";
		}
	}
	
	$avatar="";
	$item="";
	if($options['avatar']) {
		$avatar=sk_avatar($comment->id,$av_size);
	}
	$comment_text=apply_filters('comment_text', $comment->text);
	$comment_text=str_replace("<p>", "", $comment_text);
	$comment_text=str_replace("</p>", "", $comment_text);
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
	
	if($options['avatar']) {
		$answer.="\n<div class='sk-comment'>
		$item
		</div>";
	} else {
		$answer.="\n<li class='sk-comment'>
				$item
				</li>";
	}
	return $answer;
}

/**
  * Returns HTML for contents to show
  *
  * @param int group Which group are we showing?
  * @return string
  * @access public
  */

function sk_showComments($page=1)
{
	global $wpdb;
	
	$options = get_option('widget_sk');
	$size=$options['items'];
	$first=(($page-1)*$size);
	$table_name = $wpdb->prefix . "schreikasten";
	$sql="SELECT id, alias, text, DATE_FORMAT(date, '%d/%c/%Y %l:%i%p') as date, user_id, email FROM $table_name WHERE status=".SK_HAM." ORDER BY id desc LIMIT $first, $size";
	$comments = $wpdb->get_results($sql);
	
	if(!$options['avatar']) $answer="<ul>";
	
	$av_size=32;
	
	$aux = "";
	$aux->alias = "<span id='th_sk_alias'></span>";
	$aux->text = "<span id='th_sk_text'></span>";
	$aux->date = "&nbsp;".__('Sending', 'sk')."...&nbsp;";
	
	$answer.= "<div id='trobbling-img' class='off'>".sk_formatComment($aux,true)."</div>";

	foreach($comments as $comment) {
		$answer.=sk_formatComment($comment);
	}
	
	if(!$options['avatar']) $answer.="</ul>";
	$answer.="\n";
	
	return $answer;
}

/**
  * Enable menus
  *
  * @access public
  */

function sk_menus()
{
	global $submenu;
	add_submenu_page('edit-comments.php', 'Schreikasten', __('Schreikasten' ,'sk'), 10, 'skmanage', 'sk_manage' );
	add_submenu_page('plugins.php', __('Schreikasten Configuration', 'sk'), __('Schreikasten Configuration', 'sk'), 10,  'skconfig', 'sk_config');
}

/**
  * Configuration page
  *
  * @access public
  */

function sk_config()
{
	$messages=array();
	$mode_x=$_POST['mode_x'];
	$mode=$_GET['mode'];
	switch($mode_x) {
		case 'api_x':
			$api_key=$_POST["sk_api_key"];
			update_option('sk_api_key', $api_key);
			$mode='done';
			break;
	}

	$sk_api_key=get_option('sk_api_key');
	// Now display the options editing screen
	// options form
	include('templates/sk_config.php');

}

/**
* Manage page
*
* @access public
*/

function sk_manage() {
	$select=SK_NOT_FILTERED;
	if($_GET['filter']=='spam') $select=SK_SPAM;
	if($_GET['filter']=='ham') $select=SK_HAM;
	if($_GET['filter']=='moot') $select=SK_MOOT;
	if($_GET['filter']=='black') $select=SK_BLACK;
	if($_GET['filter']=='blocked') $select=SK_BLOCKED;
	sk_managePage($select);
}

/**
  * Manage page
  * @param int select Which comment type to show?
  *
  * @access public
  */

function sk_managePage($select=SK_NOT_FILTERED) {
	global $wpdb;
	$table_name = $wpdb->prefix . "schreikasten";
	$table_list = $wpdb->prefix . "schreikasten_blacklist";
	$messages=array();

	$mode_x=$_POST['mode_x'];
	if(!$mode_x)
		$mode_x=$_GET['mode_x'];
	$mode=$_GET['mode'];
	
	//if pressed deletespam, delete all spam
	if($_POST['deletespam']) {
		$mode_x='deletespam';
	}
	
	$doaction=false;
	if($_POST['doaction']!="") $doaction=$_POST['action'];
	if($_POST['doaction2']!="") $doaction=$_POST['action2'];
	if($doaction)
	{
		switch($doaction)
		{
			case 'approve':
				foreach($_POST['checked_comments'] as $checked_id) {
					sk_markHam($checked_id);
				}
				break;
			case 'markspam':
				foreach($_POST['checked_comments'] as $checked_id) {
					sk_markSpam($checked_id);
				}
				break;
			case 'markblack':
				foreach($_POST['checked_comments'] as $checked_id) {
					sk_markBlack($checked_id);
				}
				break;
			case 'delete':
				foreach($_POST['checked_comments'] as $checked_id) {
					sk_deleteComment($checked_id);
				}
				break;
			case 'forever':				
				foreach($_POST['checked_pcs'] as $checked_id) {
					sk_foreverLock($checked_id);
				}
				break;
			case 'unlock':
				foreach($_POST['checked_pcs'] as $checked_id) {
					sk_unlock($checked_id);
				}
				break;
		}
	}
	
	$sk_api_key=get_option('sk_api_key');
	$sk_managepage='skmanage';
	switch($select) {
	case SK_MOOT:
		$sk_managepage='skmanagemoot';
		break;
	case SK_HAM:
		$sk_managepage='skmanageham';
		break;
	case SK_SPAM:
		$sk_managepage='skmanagespam';
		break;
	case SK_BLACK:
		$sk_managepage='skmanageblack';
		break;
	case SK_BLOCKED:
		$sk_managepage='skmanageblocked';
		break;
	}

	switch($mode_x) {
		case 'edit_x':
		case 'tedit_x':		
			$mode='done';
			if($mode_x=='tedit_x') {
					$mode="tracking";
			}
			if($_POST['submit']) {
				$id=$_POST['sk_id'];
				$alias=$_POST['sk_alias'];
				$email=$_POST['sk_email'];
				$comment=$_POST['sk_comment'];   
				sk_editComment($id, $alias, $email, $comment);
				
				$newstatus=$_POST['comment_status'];
				$actstatus=sk_status($id);
				
				if($newstatus!=$actstatus) {
					switch($newstatus) {
						case SK_HAM:
							sk_markHam($id);
							break;
						case SK_SPAM:
							sk_markSpam($id);
							break;
						case SK_BLACK:
							sk_markBlack($id);
							break;
					}
					array_push($messages, __("Status changed",'sk'));
				}
				
				array_push($messages, __( 'Comment modified', 'sk' ));
			}
			break;
		case 'set_ham_x':
			$id=$_GET['id'];
			if(sk_markHam($id)) array_push($messages, __("Status changed",'sk'));
			break;
		case 'set_black_x':
			$id=$_GET['id'];
			if(sk_markBlack($id)) array_push($messages, __("Status changed",'sk'));
			break;
		case 'set_spam_x':
			$id=$_GET['id'];
			if(sk_markSpam($id)) array_push($messages, __("Status changed",'sk'));
			break;
		case 'delete_x':
			$id=$_GET['id'];
			if(sk_deleteComment($id)) array_push($messages, __("Comment deleted",'sk'));
			break;
		case 'forever_lock_x':
			$id=$_GET['id'];
			if(sk_foreverLock($id)) array_push($messages, __("PC locked forever",'sk'));
			break;
		case 'unlock_x':
			$id=$_GET['id'];
			if(sk_unlock($id)) array_push($messages, __("PC unlocked",'sk'));
			break;
		case 'lock_x':
			$id=$_GET['id'];
			sk_markBlack($id);
			array_push($messages, __("PC locked",'sk'));
			break;
		case 'deletespam_x':
			sk_deleteSpam();
			break;
	}
	
	switch($mode) {
		case 'tedit':
		case 'edit':
			$id=$_GET['id'];
			$table_name = $wpdb->prefix . "schreikasten";
			$data = $wpdb->get_row("select alias, text, status, date, email from $table_name where id=$id");
			if($data) {
				$alias=$data->alias;
				$email=$data->email;
				$comment=$data->text;
				$status=$data->status;
				$date=$data->date;
				include('templates/sk_comment.php');
			} else {
				$mode='done';
			}
			break;
		case 'tracking':
			$tid=$_GET['tid'];
			$table_name = $wpdb->prefix . "schreikasten";
			$data = $wpdb->get_row("select * from $table_name where id=$tid");
			if($data->id) {
				include('templates/sk_tracking.php');
			} else {
				$mode='done';
			}
			break;
		case 'delete':
		case 'set_ham':
		case 'set_black':
		case 'set_spam':
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

// sk widget stuff
function sk_widget_init() {

	if ( !function_exists('register_sidebar_widget') )
		return;

	function sk_widget($args) {

		global $wpdb;
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);
		
		$options = get_option('widget_sk');
		$title = $options['title'];

		$table_name = $wpdb->prefix . "schreikasten";
		
		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;
		include('templates/sk_widget.php');
		echo $after_widget;
	}

	function sk_widget_control() {
		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_sk');
		if ( !is_array($options) ) {
			$options = array('title'=>'', 'registered'=>false, 'avatar'=>true, 'replies'=>false, 'alert_about_emails'=>true, 'items'=>'5', 'refresh'=>0, 'bl_days'=>'7', 'bl_maxpending'=>'2');
			
		}
		if ( $_POST['sk-submit'] ) {
			// Remember to sanitize and format use input appropriately.
			$options['title'] = strip_tags(stripslashes($_POST['sk_title']));
			$options['items'] = $_POST['sk_items'];
			
			$options['avatar'] = false;
			if($_POST['sk_avatar'])
				$options['avatar'] = true;
			
			$options['registered'] = false;
			if($_POST['sk_registered'])
				$options['registered'] = true;
			
			$options['replies'] = false;
			if($_POST['sk_replies'])
				$options['replies'] = true;
			
			$options['alert_about_emails'] = false;
			if($_POST['sk_alert_about_emails'])
				$options['alert_about_emails'] = true;
				
			$options['refresh'] = $_POST['sk_refresh'];
			$options['bl_days'] = $_POST['sk_bl_days'];
			$options['bl_maxpending'] = $_POST['sk_bl_maxpending'];
				
			update_option('widget_sk', $options);
		}
		// Be sure you format your options to be valid HTML attributes.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);

		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		$items="selected".$options['items'];
		$$items=' selected="selected"';
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
		
		require("templates/sk_widgetconfig.php");
		
		if(!function_exists('minimax')) { ?>
		<p>
			<label>
				<?php _e('You have to install <a href="http://www.sebaxtian.com/acerca-de/minimax"  target="_BLANK">minimax</a> in order for this plugin to work', 'sk'); ?>
			</label>
		</p><?
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
