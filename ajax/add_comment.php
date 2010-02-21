<?php
require_once( '../../../../wp-config.php' );

if ($_SERVER['HTTP_X_FORWARD_FOR']) {
	$ip = $_SERVER['HTTP_X_FORWARD_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}

$alias=$_POST['alias'];
$email=$_POST['email'];
$text=$_POST['text'];
$for=$_POST['for'];

setcookie('comment_author_' . COOKIEHASH, $alias, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
setcookie('comment_author_email_' . COOKIEHASH, $email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);

$id=sk_add_comment($alias, $email, $text, $ip, $for);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title></title>
</head>
<body>
<?php

if(wp_verify_nonce($_POST['nonce'], 'schreikasten')) {

	if(sk_cookie_id()==0) {
		echo "<p id='skwarning'>".__('We cannot accept messages<br>from this PC', 'sk').".</p>";
	}
	
	$rand=false;
	if($_POST['rand']) $rand=$_POST['rand'];
	
	echo sk_show_comments(1,$id,$rand);
	echo sk_page_selector(1,$rand);

} else {
	_e('Only Schreikasten can use this link.', 'sk');
}
?>
</body>
</html>
