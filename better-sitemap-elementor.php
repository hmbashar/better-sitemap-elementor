<?php
/**
 * Plugin Name: Better Sitemap for Elementor
 * Description: A powerful Elementor widget that creates customizable sitemaps for your footer or any widget area. Features include custom post type support, hierarchical page display, category organization, and flexible styling options. It's open source by (Pedro de Barros)
 * Version: 1.0
 * Author URI: https://profiles.wordpress.org/hmbashar/
 * Author: Md Abul Bashar
 * License: GPLv2 or later
 * Text Domain: better-sitemap-for-elementor
 * Domain Path: /languages
 * Requires Plugins: elementor
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Check if Elementor is installed and activated
function better_sitemap_elementor_is_elementor_active() {
    return did_action('elementor/loaded');
}

// Display admin notice if Elementor is not active
function better_sitemap_elementor_admin_notice() {
    if (!better_sitemap_elementor_is_elementor_active()) {
        $message = sprintf(
            esc_html__('Better Sitemap for Elementor requires %1$sElementor%2$s plugin to be active. Please install and activate Elementor first.', 'better-sitemap-for-elementor'),
            '<strong>',
            '</strong>'
        );
        echo '<div class="notice notice-warning is-dismissible"><p>' . $message . '</p></div>';
    }
}
add_action('admin_notices', 'better_sitemap_elementor_admin_notice');

// Load plugin textdomain
function better_sitemap_elementor_load_textdomain() {
    load_plugin_textdomain('better-sitemap-for-elementor', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'better_sitemap_elementor_load_textdomain');

// Register the widget only if Elementor is active
function better_sitemap_elementor_register_widget($widgets_manager) {
    if (better_sitemap_elementor_is_elementor_active()) {
        require_once(__DIR__ . '/widgets/sitemap.php');
        $widgets_manager->register(new \BETTER_SITEMAP\Sitemap_Widget());
    }
}
add_action('elementor/widgets/register', 'better_sitemap_elementor_register_widget');

// Enqueue frontend styles
function better_sitemap_enqueue_styles() {
    wp_enqueue_style(
        'better-sitemap-style',
        plugin_dir_url(__FILE__) . 'assets/css/style.css', 
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'better_sitemap_enqueue_styles');