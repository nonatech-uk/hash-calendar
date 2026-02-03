<?php
/**
 * Plugin Name: GH3 Hash Runs
 * Plugin URI: https://guildfordh3.org.uk
 * Description: Custom Hash House Harriers run management for Guildford H3
 * Version: 1.0.0
 * Author: Guildford Hash House Harriers
 * License: Non-Commercial Use License
 * GitHub Plugin URI: nonatech-uk/hash-calendar
 * Text Domain: gh3-hash-runs
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GH3_HASH_RUNS_VERSION', '1.0.0');
define('GH3_HASH_RUNS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GH3_HASH_RUNS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once GH3_HASH_RUNS_PLUGIN_DIR . 'includes/class-gh3-post-type.php';
require_once GH3_HASH_RUNS_PLUGIN_DIR . 'includes/class-gh3-admin.php';
require_once GH3_HASH_RUNS_PLUGIN_DIR . 'includes/class-gh3-shortcode.php';
require_once GH3_HASH_RUNS_PLUGIN_DIR . 'includes/class-gh3-updater.php';

/**
 * Initialize the plugin
 */
function gh3_hash_runs_init() {
    // Initialize post type
    $post_type = new GH3_Post_Type();
    $post_type->init();

    // Initialize admin
    if (is_admin()) {
        $admin = new GH3_Admin();
        $admin->init();
    }

    // Initialize shortcode
    $shortcode = new GH3_Shortcode();
    $shortcode->init();

    // Initialize GitHub updater
    if (is_admin()) {
        $updater = new GH3_Updater(__FILE__);
        $updater->init();
    }
}
add_action('plugins_loaded', 'gh3_hash_runs_init');

/**
 * Activation hook
 */
function gh3_hash_runs_activate() {
    // Register post type on activation
    $post_type = new GH3_Post_Type();
    $post_type->register_post_type();

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'gh3_hash_runs_activate');

/**
 * Deactivation hook
 */
function gh3_hash_runs_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'gh3_hash_runs_deactivate');
