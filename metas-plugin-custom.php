<?php
/**
 * Plugin Name: Create metaboxes Custom
 * Plugin URI: ""
 * Description: Create metaboxes Custom
 * Version: 1.0
 * Author: Aramis
 * Author URI: ""
 * License: A "Slug" license name e.g. GPL12
 */

define('DIR__PLUGIN_URL', plugin_dir_url(__FILE__));
define('DIR__PLUGIN_DIR', plugin_dir_path(__FILE__));

if ( is_admin() ) {
	require_once DIR__PLUGIN_DIR . 'class.metas-admin.php';
	$metas = new MetasAdmin();
} else {
	require_once DIR__PLUGIN_DIR . 'class.metas.php';
	$metas = new Metas();
}