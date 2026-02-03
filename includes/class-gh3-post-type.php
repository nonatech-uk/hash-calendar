<?php
/**
 * Custom Post Type Registration for Hash Runs
 */

if (!defined('ABSPATH')) {
    exit;
}

class GH3_Post_Type {

    /**
     * Initialize hooks
     */
    public function init() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_meta_fields'));
    }

    /**
     * Register the hash_run custom post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Hash Runs', 'Post type general name', 'gh3-hash-runs'),
            'singular_name'         => _x('Hash Run', 'Post type singular name', 'gh3-hash-runs'),
            'menu_name'             => _x('Hash Runs', 'Admin Menu text', 'gh3-hash-runs'),
            'name_admin_bar'        => _x('Hash Run', 'Add New on Toolbar', 'gh3-hash-runs'),
            'add_new'               => __('Add New', 'gh3-hash-runs'),
            'add_new_item'          => __('Add New Hash Run', 'gh3-hash-runs'),
            'new_item'              => __('New Hash Run', 'gh3-hash-runs'),
            'edit_item'             => __('Edit Hash Run', 'gh3-hash-runs'),
            'view_item'             => __('View Hash Run', 'gh3-hash-runs'),
            'all_items'             => __('All Hash Runs', 'gh3-hash-runs'),
            'search_items'          => __('Search Hash Runs', 'gh3-hash-runs'),
            'parent_item_colon'     => __('Parent Hash Runs:', 'gh3-hash-runs'),
            'not_found'             => __('No hash runs found.', 'gh3-hash-runs'),
            'not_found_in_trash'    => __('No hash runs found in Trash.', 'gh3-hash-runs'),
            'featured_image'        => _x('Hash Run Image', 'Overrides the "Featured Image" phrase', 'gh3-hash-runs'),
            'set_featured_image'    => _x('Set hash run image', 'Overrides the "Set featured image" phrase', 'gh3-hash-runs'),
            'remove_featured_image' => _x('Remove hash run image', 'Overrides the "Remove featured image" phrase', 'gh3-hash-runs'),
            'use_featured_image'    => _x('Use as hash run image', 'Overrides the "Use as featured image" phrase', 'gh3-hash-runs'),
            'archives'              => _x('Hash Run archives', 'The post type archive label', 'gh3-hash-runs'),
            'insert_into_item'      => _x('Insert into hash run', 'Overrides the "Insert into post" phrase', 'gh3-hash-runs'),
            'uploaded_to_this_item' => _x('Uploaded to this hash run', 'Overrides the "Uploaded to this post" phrase', 'gh3-hash-runs'),
            'filter_items_list'     => _x('Filter hash runs list', 'Screen reader text', 'gh3-hash-runs'),
            'items_list_navigation' => _x('Hash runs list navigation', 'Screen reader text', 'gh3-hash-runs'),
            'items_list'            => _x('Hash runs list', 'Screen reader text', 'gh3-hash-runs'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'hash-run'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-location-alt',
            'supports'           => array('title', 'editor', 'thumbnail'),
            'show_in_rest'       => true,
        );

        register_post_type('hash_run', $args);
    }

    /**
     * Register meta fields for REST API
     */
    public function register_meta_fields() {
        $meta_fields = array(
            '_gh3_run_number' => 'integer',
            '_gh3_run_date'   => 'string',
            '_gh3_start_time' => 'string',
            '_gh3_hares'      => 'string',
            '_gh3_location'   => 'string',
            '_gh3_what3words' => 'string',
            '_gh3_maps_url'   => 'string',
            '_gh3_oninn'      => 'string',
            '_gh3_notes'      => 'string',
        );

        foreach ($meta_fields as $meta_key => $type) {
            register_post_meta('hash_run', $meta_key, array(
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => $type,
                'auth_callback' => function() {
                    return current_user_can('edit_posts');
                }
            ));
        }
    }
}
