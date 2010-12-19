<?php if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
	exit();
	
	define ("SK_CAP", 'moderate_schreikasten');

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
	
	//Delete cappabilitie
	$role1 = get_role( 'administrator' );
	$role2 = get_role( 'editor' );
	$role3 = get_role( 'author' );
	$role1->remove_cap( SK_CAP );
	$role2->remove_cap( SK_CAP );
	$role3->remove_cap( SK_CAP );

?>
