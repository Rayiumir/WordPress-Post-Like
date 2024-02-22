<?php

defined( 'ABSPATH' ) || exit;

// Create Database Likes Table 

function rayium_post_like_install() {
    
    global $wpdb;
    
    $table_wp_likes = $wpdb->prefix . 'likes';
    
    $sql = "
    CREATE TABLE `{$table_wp_likes}` (
        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `post_id` bigint(20) unsigned NOT NULL,
        `user_id` bigint(20) unsigned NOT NULL,
        `ip` varchar(15) NOT NULL,
        `liked` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`ID`),
        KEY `post_id` (`post_id`),
        KEY `user_id` (`user_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta($sql);
}

// Calling Files CSS and JS

function rayium_post_like_script(){

    $JS = ['jquery'];

    wp_enqueue_script(
        'rayium_script',
        RAYIUM_WP_LIKES_JS . 'likes.js',
        $JS,
        RAYIUM_LIKES_VERSION,
        true
    );

    wp_enqueue_style(
        'rayium_style',
        RAYIUM_WP_LIKES_CSS . 'likes.css',
        [],
        RAYIUM_LIKES_VERSION
    );

    wp_localize_script( 'rayium_script', 'rayium', [
        'ajax_url'  => admin_url( 'admin-ajax.php' )
    ] );

}
add_action( 'wp_enqueue_scripts', 'rayium_post_like_script' );

// Add Buuton Like in Content

function rayium_button_post_like($content){

    $text = __('Like', 'rayium-post-like');

    $post_id = get_the_ID();
    
    $button = "<button type='button' class='LikePost' data-id='$post_id'> $text <span class='like-count'>20</span></button>";

    return $content . $button;
}
add_filter( 'the_content', 'rayium_button_post_like' );
