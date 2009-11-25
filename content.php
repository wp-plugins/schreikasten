<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title></title>
</head>
<body><?php
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
// $trans['\n'] = '<br>';
// ksort($trans);

$page=$_POST['page'];
if(!$page)
	$page=1;
	
echo sk_showComments($page);
echo sk_page_selector($page);

// echo strtr(sk_showComments($page), $trans)."\n".sk_page_selector($page);

?>
</body>
</html>
