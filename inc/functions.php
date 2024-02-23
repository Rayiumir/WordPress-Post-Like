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

    $like_count     = rayium_get_post_like( $post_id );
    $liked_class    = rayium_is_like_post( $post_id, get_current_user_id() ) ? 'post-liked' : '';
    $nonce = wp_create_nonce( 'post-like' . $post_id );
    
    $button = "<button type='button' class='post-like $liked_class' data-id='$post_id' data-nonce='$nonce'>
        <svg width='16' height='16' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'><style>.spinner_ajPY{transform-origin:center;animation:spinner_AtaB .75s infinite linear}@keyframes spinner_AtaB{100%{transform:rotate(360deg)}}</style><path d='M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z' opacity='.25'/><path d='M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z' class='spinner_ajPY'/></svg> 
     $text 
     <span class='like-count'>$like_count</span>
    </button>
    <span class='like-message'></span>
    ";

    return $content . $button;
}
add_filter( 'the_content', 'rayium_button_post_like' );

// Callback Ajax Post Like

function rayium_callback_post_like() {

    $result = [];

    $post_id = absint( $_POST['post_id'] );
    $like  = $_POST['like'] == 'true' ? true : false;

    if( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'post-like' . $post_id ) ){
        wp_send_json_error( [
            'message'   => 'Forbidden, nonce is invalid',
            'code'      => '403',
        ], 401 );
    }

    $liked  = rayium_do_like( $post_id, get_current_user_id(), $like );

    if( is_wp_error( $liked ) ){
        wp_send_json_error( [
            'message'   => $liked->get_error_message(),
            'code'      => $liked->get_error_code(),
        ], 401 );
    }else{
        wp_send_json_success( $liked );
    }

}
add_action( 'wp_ajax_rayium_like', 'rayium_callback_post_like' );

// Function Get Data Like Conut 

function rayium_get_post_like( $post_id ){
    global $wpdb;
    $like_count = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->wp_likes} WHERE post_id = %d"
            , $post_id
        )
    ); 
    return absint( $like_count );
}

// Function Like and Dislike

function rayium_do_like( $post_id, $user_id, $like ){

    global $wpdb;
    
    if( ! get_post_type( $post_id ) ){
        return new WP_Error( 'invalid_post_id', __( 'Post is invalid', 'rayium-post-like' ) );
    }

    $exists_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT ID FROM {$wpdb->wp_likes} WHERE post_id = %d AND user_id = %d"
            , $post_id
            , get_current_user_id()
        )
    );

    if( $exists_id && $like ){
        //You like previously
        return new WP_Error( 'liked_prev', __( 'You liked previously', 'rayium-post-like' ) );
    }

    if( ! $exists_id && ! $like ){
        //You did not like this post
        return new WP_Error( 'did_not_liked', __( 'You did not like previously', 'rayium-post-like' ) );
    }

    if( $like ){

        $like_data = [
            'post_id'       => $post_id,
            'user_id'       => $user_id,
            'ip'            => $_SERVER['REMOTE_ADDR'],
            'liked'         => 1,
            'created_at'    => current_time('mysql')
        ];

        $liked = $wpdb->insert(
            $wpdb->wp_likes,
            $like_data,
            ['%d', '%d', '%s', '%d', '%s']
        );

        if( $liked ){
            return [
                'message'   =>  __( 'Liked successfuly', 'rayium-post-like' ),
                'liked'     => true,
                'count'     => rayium_get_post_like( $post_id ),
            ];
        }else{
            return new WP_Error( 'like_error', __( 'Error in like', 'rayium-post-like' ) );
        }

    }else{

        $disliked = $wpdb->delete(
            $wpdb->wp_likes,
            [
                'ID'    => $exists_id
            ]
        );

        if( $disliked ){
            return [
                'message'   =>  __( 'Disliked successfuly', 'rayium-post-like' ),
                'liked'     => false,
                'count'     => rayium_get_post_like( $post_id ),
            ];
        }else{
            return new WP_Error( 'dislike_error', __( 'Error in dislike', 'rayium-post-like' ) );
        }

    }

}

// Function Registration User ID and IP in  Likes Table

function rayium_is_like_post( $post_id, $user_id ){

    global $wpdb;

    if( $user_id ){
        $where = $wpdb->prepare( " AND user_id = %d ", $user_id );
    }else{
        $where = $wpdb->prepare( " AND ip = %s ", $_SERVER['REMOTE_ADDR'] );
    }

    $liked = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->wp_likes} WHERE post_id = %d $where"
            , $post_id
        )
    );
    return $liked;
}

