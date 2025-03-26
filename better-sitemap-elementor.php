<?php
/**
 * Plugin Name: Better Sitemap for Elementor
 * Description: A customizable footer sitemap widget for Elementor.
 * Version: 1.03
 * Author: Pedro de Barros
 * Text Domain: better-sitemap-elementor
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Load plugin textdomain
function better_sitemap_elementor_load_textdomain() {
    load_plugin_textdomain('better-sitemap-elementor', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'better_sitemap_elementor_load_textdomain');

// Register the widget
add_action('elementor/widgets/register', function($widgets_manager) {
    require_once(__DIR__ . '/widgets/better-sitemap-widget.php');
    $widgets_manager->register(new \Elementor\Better_Sitemap_Widget());
});

// Load CSS/JS if needed
add_action('elementor/frontend/after_enqueue_styles', function() {
    wp_enqueue_style('better-sitemap-style', plugin_dir_url(__FILE__) . 'assets/style.css');
});

// Load admin.js script
function enqueue_better_sitemap_admin_scripts() {
    wp_enqueue_script(
        'better-sitemap-admin',
        plugin_dir_url( __FILE__ ) . 'assets/js/admin.js',
        array( 'jquery' ),
        '1.0.2',
        true
    );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'enqueue_better_sitemap_admin_scripts' );
