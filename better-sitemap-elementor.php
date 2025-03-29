<?php
/**
 * Plugin Name: Better Sitemap for Elementor
 * Description: A powerful Elementor widget that creates customizable sitemaps for your footer or any widget area. Features include custom post type support, hierarchical page display, category organization, and flexible styling options. It's open source by (Pedro de Barros)
 * Version: 1.0.0
 * Author URI: https://profiles.wordpress.org/hmbashar/
 * Author: Md Abul Bashar
 * License: GPLv2 or later
 * Text Domain: better-sitemap-elementor
 * Domain Path: /languages
 * Requires Plugins: elementor
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Load plugin textdomain
function better_sitemap_elementor_load_textdomain() {
    load_plugin_textdomain('better-sitemap-elementor', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'better_sitemap_elementor_load_textdomain');

// Register the widget
add_action('elementor/widgets/register', function($widgets_manager) {
    require_once(__DIR__ . '/widgets/sitemap.php');
    $widgets_manager->register(new \Elementor\Sitemap_Widget());
});

// Enqueue frontend styles
function enqueue_better_sitemap_styles() {
    wp_enqueue_style(
        'better-sitemap-style',
        plugin_dir_url(__FILE__) . 'assets/css/style.css'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_better_sitemap_styles');

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
