<?php if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
	exit();

	global $wpdb;
	global $db_version;
	$table_name = $wpdb->prefix . "schreikasten";
	$wpdb->query("DROP TABLE $table_name;");
	$blacklist_name = $wpdb->prefix . "schreikasten_blacklist";
	$wpdb->query("DROP TABLE $blacklist_name;");
	delete_option('sk_db_version');
	delete_option('sk_api_key');
	delete_option('sk_api_key_accepted');
	delete_option('sk_options');
	delete_option('widget_sk');

?>
