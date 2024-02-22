<?php 

/*
 * Plugin Name:       Rayium Wordpress Post Like 
 * Plugin URI:        https://github.com/Rayiumir/WordPress-Post-Like
 * Description:       A Plugin Post Like for Wordpress
 * Version:           1.0.0
 * Requires at least: 6.0.0
 * Requires PHP:      7.1
 * Author:            Raymond Baghumian
 * Author URI:        https://github.com/Rayiumir/WordPress-Post-Like
 * Text Domain: rayium-post-like
 * Domain Path: /languages
 */

 defined('ABSPATH') || exit;

 define("RAYIUM_URL", plugin_dir_url( __FILE__ ));
 define("RAYIUM_PATH", plugin_dir_path( __FILE__ ));
 
 define('RAYIUM_WP_LIKES_INC', RAYIUM_PATH . 'inc/');
 define('RAYIUM_WP_LIKES_CSS', RAYIUM_URL . 'css/');
 define('RAYIUM_WP_LIKES_JS', RAYIUM_URL . 'js/');
 
 define( 'RAYIUM_WP_LIKES_VERSION', '1.0.0' );

 define(
    'RAYIUM_LIKES_VERSION',
    defined('WP_DEBUG') &&
    WP_DEBUG ? time() : RAYIUM_WP_LIKES_VERSION
);
 
 // Calling Files
 
 require_once(RAYIUM_WP_LIKES_INC . 'functions.php');
 
 // Database Post Likes
 
 global $wpdb;
 $wpdb->likes = $wpdb->prefix  . 'likes';
 
 // Languages
 
 add_action( 'plugins_loaded', function(){
     load_plugin_textdomain( 'rayium-post-like', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
 });
 
 // Activation Install
 register_activation_hook( __FILE__, 'rayium_post_like_install' );
 