<?php   
/*
 * Plugin Name:       Rayium Wordpress Post Like 
 * Plugin URI:        https://github.com/Rayiumir/WordPress-Post-Like
 * Description:       A Plugin Post Like for Wordpress
 * Version:           1.0.0
 * Requires at least: 6.0.0
 * Requires PHP:      7.2
 * Author:            Raymond Baghumian
 * Author URI:        https://github.com/Rayiumir/WordPress-Post-Like
 */

 defined('ABSPATH') || exit;

define("Rayium_URL", plugin_dir_url( __FILE__ ));
define("Rayium_PATH", plugin_dir_path( __FILE__ ));

define('RAYIUM_WP_LIKES_INC', Rayium_PATH . 'inc/');
define('RAYIUM_WP_LIKES_CSS', Rayium_URL . 'css/');
define('RAYIUM_WP_LIKES_JS', Rayium_URL . 'js/');

// Calling Files

require(RAYIUM_WP_LIKES_INC . 'function.php');