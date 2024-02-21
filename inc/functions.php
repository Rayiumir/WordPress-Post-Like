<?php

defined( 'ABSPATH' ) || exit;

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