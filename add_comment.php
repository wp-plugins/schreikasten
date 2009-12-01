<?php
require_once( '../../../wp-config.php' );

// $trans['á'] = '&aacute;';
// $trans['é'] = '&eacute;';
// $trans['í'] = '&iacute;';
// $trans['ó'] = '&oacute;';
// $trans['ú'] = '&uacute;';
// $trans['ñ'] = '&ntilde;';
// $trans['Á'] = '&Aacute;';
// $trans['É'] = '&Eacute;';
// $trans['Í'] = '&Iacute;';
// $trans['Ó'] = '&Oacute;';
// $trans['Ú'] = '&Uacute;';
// $trans['Ñ'] = '&Ntilde;';
// // $trans['&'] = '&#038;';
// $trans['Ü'] = '&#220;';
// $trans['ü'] = '&#252;';
// $trans['¡'] = '&#161;';
// $trans['¿'] = '&#191;';
// $trans['–'] = '&#8211;';
// ksort($trans);

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

// setcookie("sk-alias", $alias, time()+60*30,"/","");
// setcookie("sk-email", $email, time()+60*30,"/","");

$id=sk_addComment($alias, $email, $text, $ip, $for);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title></title>
</head>
<body><?php

if(sk_cookieID()==0) {
	echo "<p id='skwarning'>".__('We cannot accept messages<br>from this PC', 'sk').".</p>";
}
echo sk_showComments(1,$id);
echo sk_page_selector($page);

if($id>0)
	sk_inform($id);

?>
</body>
</html>
