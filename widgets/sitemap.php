<?php
/**
 * Better Sitemap Elementor Widget
 * 
 * This widget creates a customizable sitemap for WordPress sites using Elementor.
 * It allows users to select specific posts and pages to display in a hierarchical structure,
 * with customizable styling options for different levels.
 *
 * Features:
 * - Select individual posts and pages to include
 * - Customizable icons for each item
 * - Hierarchical display of pages with child pages
 * - Extensive styling options for each level
 * - Responsive design controls
 * - Custom typography and colors
 * 
 * @package BetterSitemap
 * @subpackage Elementor
 * @since 1.0.0
 * @version 1.0.0
 * 
 * @author Md Abul Bahar <hmbashar@gmail.com>
 * @link @link github.com/hmbashar/better-sitemap-elementor
 */

/**
 * Dependencies:
 * - Elementor Plugin
 * - WordPress Core
 *
 * Usage:
 * 1. Install and activate the plugin
 * 2. Add the widget through Elementor editor
 * 3. Configure content and styling options
 * 
 * @see Elementor\Widget_Base
 * @see get_posts()
 * @see get_pages()
 */

namespace BETTER_SITEMAP;

if (!defined('ABSPATH'))
    exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
/**
 * Sitemap_Widget Class
 * 
 * Creates a customizable sitemap widget for Elementor that displays posts and pages
 * in a hierarchical structure with extensive styling options.
 * 
 * Features:
 * - Displays selected posts and pages in a customizable list format
 * - Supports hierarchical page structures up to 2 levels deep
 * - Custom icons for list items with hover effects
 * - Extensive styling controls for list items, spacing, and typography
 * - Responsive design with customizable layouts
 * - Different styling options for each hierarchical level
 * 
 * @since 1.0.0
 * @package BetterSitemap
 * @subpackage Elementor
 */

class Sitemap_Widget extends Widget_Base
{
    public function get_name()
    {
        return 'better-sitemap-widget';
    }
    public function get_title()
    {
        return __('Better Sitemap', 'better-sitemap-for-elementor');
    }
    public function get_icon()
    {
        return 'eicon-sitemap';
    }
    public function get_categories()
    {
        return ['basic'];
    }

    /**
     * Retrieves a list of published posts for use in dropdown/select controls
     * 
     * This method queries all published posts and creates an associative array
     * where post IDs are keys and post titles are values. Used to populate
     * select controls in the widget settings.
     *
     * @since 1.0.0
     * @access private
     *
     * @return array Associative array of post IDs and titles
     */
    private function get_posts_list()
    {
        $posts = get_posts([
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ]);

        $options = [];
        foreach ($posts as $post) {
            $options[$post->ID] = $post->post_title;
        }

        return $options;
    }

    /**
     * Retrieves a list of top-level published pages for use in dropdown/select controls
     * 
     * This method queries published pages with no parent (top-level pages) and creates 
     * an associative array where page IDs are keys and page titles are values. Used to
     * populate select controls in the widget settings.
     *
     * @since 1.0.0
     * @access private
     * 
     * @return array Associative array of page IDs and titles
     */
    private function get_pages_list()
    {
        $pages = get_pages([
            'post_status' => 'publish',
            'parent' => 0,
        ]);

        $options = [];
        foreach ($pages as $page) {
            $options[$page->ID] = $page->post_title;
        }

        return $options;
    }

    /**
     * Register Elementor widget controls
     * 
     * This method sets up all the controls/settings for the sitemap widget including:
     * - Content controls for selecting posts/pages and icons
     * - Style controls for the overall list container
     * - Individual item styling controls
     * - Child level 1 & 2 specific styling
     * 
     * Controls are organized into sections:
     * - Content section: Post/page selection and icons
     * - Style section: List container styling
     * - Item Style section: Individual item appearance
     * - Child Level sections: Styling for nested items
     *
     * @since 1.0.0
     * @access protected
     * @return void
     */
    protected function register_controls()
    {

        /*--------------------------------
        Content
        --------------------------------*/

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'better-sitemap-for-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'content_type',
            [
                'label' => __('Content Type', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'Post',
                'options' => [
                    'Post' => __('Posts', 'better-sitemap-for-elementor'),
                    'Page' => __('Pages', 'better-sitemap-for-elementor'),
                    'column_break' => __('Column Break', 'better-sitemap-for-elementor'),
                ],
            ]
        );

        $repeater->add_control(
            'posts_list',
            [
                'label' => __('Select Posts', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_posts_list(),
                'multiple' => false,
                'condition' => [
                    'content_type' => 'Post',
                ],
            ]
        );

        $repeater->add_control(
            'pages_list',
            [
                'label' => __('Select Pages', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_pages_list(),
                'multiple' => false,
                'condition' => [
                    'content_type' => 'Page',
                ],
            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => __('Icon', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-circle',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'content_type!' => 'column_break',
                ],
            ]
        );

        $this->add_control(
            'sitemap_items',
            [
                'label' => __('Sitemap Items', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'content_type' => 'Post',
                        'icon' => [
                            'value' => 'fas fa-circle',
                            'library' => 'fa-solid',
                        ],
                    ],
                ],
                'title_field' => '{{{ content_type === "Post" ? "Post" : content_type === "Page" ? "Page" : "Column Break" }}}',
            ]
        );

        $this->end_controls_section();


        /*--------------------------------
        Style
        --------------------------------*/
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'better-sitemap-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'wrapper_width',
            [
                'label' => __('Width', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-wrapper' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'list_spacing',
            [
                'label' => __('List Gap', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'level_gap',
            [
                'label' => __('Level Gap', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list ul' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'list_padding',
            [
                'label' => __('Padding', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'list_margin',
            [
                'label' => __('Margin', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'list_border',
                'label' => __('Border', 'better-sitemap-for-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-wrapper',
            ]
        );

        $this->add_responsive_control(
            'list_border_radius',
            [
                'label' => __('Border Radius', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'list_box_shadow',
                'label' => __('Box Shadow', 'better-sitemap-for-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-wrapper',
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'list_background',
                'label' => __('Background', 'better-sitemap-for-elementor'),
                'types' => ['classic', 'gradient'],
                'exclude' => [
                    'image',
                ], 
                'selector' => '{{WRAPPER}} .better-sitemap-wrapper',
            ]
        ); // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
        $this->end_controls_section();


        /*------------------------------------
        Item Style
        ------------------------------------*/
        $this->start_controls_section(
            'item_style_section',
            [
                'label' => __('Level 1', 'better-sitemap-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'show_icon',
            [
                'label' => __('Show Icon', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'better-sitemap-for-elementor'),
                'label_off' => __('Hide', 'better-sitemap-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .better-sitemap-list svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __('Icon Size', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .better-sitemap-list svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_gap',
            [
                'label' => __('Icon Spacing', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list span' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => __('Typography', 'better-sitemap-for-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-list a',
            ]
        );

        $this->start_controls_tabs('text_style_tabs');

        $this->start_controls_tab(
            'text_normal_tab',
            [
                'label' => __('Normal', 'better-sitemap-for-elementor'),
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'text_hover_tab',
            [
                'label' => __('Hover', 'better-sitemap-for-elementor'),
            ]
        );

        $this->add_control(
            'text_hover_color',
            [
                'label' => __('Text Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0066CC',
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'icon_hover_color',
            [
                'label' => __('Icon Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0066CC',
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list .better-sitemap-single-item span:hover > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .better-sitemap-list .better-sitemap-single-item span:hover  > svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /*--------------------------
        Child Level 1
        --------------------------*/

        $this->start_controls_section(
            'child_level_1_style_section',
            [
                'label' => __('Level 2', 'better-sitemap-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'child_level_1_indent',
            [
                'label' => __('Indent', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 100, 'step' => 1],
                    'em' => ['min' => 0, 'max' => 10, 'step' => 0.1],
                    'rem' => ['min' => 0, 'max' => 10, 'step' => 0.1],
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-1' => 'padding-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'child_level_1_typography',
                'label' => __('Typography', 'better-sitemap-for-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-child-level-1 a',
            ]
        );

        $this->add_control(
            'child_level_1_show_icon',
            [
                'label' => __('Show Icon', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'better-sitemap-for-elementor'),
                'label_off' => __('Hide', 'better-sitemap-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'child_level_1_icon_color',
            [
                'label' => __('Icon Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-1 i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .better-sitemap-child-level-1 svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'child_level_1_show_icon' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'child_level_1_icon_size',
            [
                'label' => __('Icon Size', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 100, 'step' => 1],
                    'em' => ['min' => 0, 'max' => 10, 'step' => 0.1],
                    'rem' => ['min' => 0, 'max' => 10, 'step' => 0.1],
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-1 i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .better-sitemap-child-level-1 svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'child_level_1_show_icon' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('child_level_1_style_tabs');

        $this->start_controls_tab(
            'child_level_1_normal_tab',
            [
                'label' => __('Normal', 'better-sitemap-for-elementor'),
            ]
        );

        $this->add_control(
            'child_level_1_text_color',
            [
                'label' => __('Text Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-1 a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'child_level_1_hover_tab',
            [
                'label' => __('Hover', 'better-sitemap-for-elementor'),
            ]
        );

        $this->add_control(
            'child_level_1_hover_color',
            [
                'label' => __('Text Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-1 a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'child_level_1_icon_hover_color',
            [
                'label' => __('Icon Hover Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0066CC',
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-1 .better-sitemap-single-item span:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .better-sitemap-child-level-1 .better-sitemap-single-item span:hover svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'child_level_1_show_icon' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        /*----------------------------
         * Child Level 2
         *----------------------------*/
        $this->start_controls_section(
            'child_level_2_style_section',
            [
                'label' => __('Level 3', 'better-sitemap-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'child_level_2_indent',
            [
                'label' => __('Indent', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-2' => 'padding-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'child_level_2_typography',
                'label' => __('Typography', 'better-sitemap-for-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-child-level-2 a',
            ]
        );

        $this->add_control(
            'child_level_2_show_icon',
            [
                'label' => __('Show Icon', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'better-sitemap-for-elementor'),
                'label_off' => __('Hide', 'better-sitemap-for-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'child_level_2_icon_color',
            [
                'label' => __('Icon Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-2 i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .better-sitemap-child-level-2 svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'child_level_2_show_icon' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'child_level_2_icon_size',
            [
                'label' => __('Icon Size', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-2 i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .better-sitemap-child-level-2 svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'child_level_2_show_icon' => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs('child_level_2_style_tabs');

        $this->start_controls_tab(
            'child_level_2_normal_tab',
            [
                'label' => __('Normal', 'better-sitemap-for-elementor'),
            ]
        );

        $this->add_control(
            'child_level_2_text_color',
            [
                'label' => __('Text Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-2 a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'child_level_2_hover_tab',
            [
                'label' => __('Hover', 'better-sitemap-for-elementor'),
            ]
        );

        $this->add_control(
            'child_level_2_hover_color',
            [
                'label' => __('Text Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-2 a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'child_level_2_icon_hover_color',
            [
                'label' => __('Icon Hover Color', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-2 .better-sitemap-single-item span:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .better-sitemap-child-level-2 .better-sitemap-single-item span:hover svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'child_level_2_show_icon' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /*----------------------------
         * Column Break Style
         *----------------------------*/
        $this->start_controls_section(
            'column_break_style_section',
            [
                'label' => __('Column Break', 'better-sitemap-for-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'column_width',
            [
                'label' => __('Column Width', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-column' => 'width: {{SIZE}}{{UNIT}};flex-basis: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label' => __('Column Gap', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'column_padding',
            [
                'label' => __('Padding', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-column' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'column_margin',
            [
                'label' => __('Margin', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-column' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'column_border',
                'label' => __('Border', 'better-sitemap-for-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-column',
            ]
        );

        $this->add_responsive_control(
            'column_border_radius',
            [
                'label' => __('Border Radius', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-column' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'column_background',
                'label' => __('Background', 'better-sitemap-for-elementor'),
                'types' => ['classic', 'gradient'],
                'exclude' => [
                    'image',
                ],
                'selector' => '{{WRAPPER}} .better-sitemap-column',
            ]
        );

        $this->add_responsive_control(
            'column_vertical_justify',
            [
                'label' => __('Columns Vertically Justify', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'default' => 'flex-start',
                'options' => [
                    'flex-start' => [
                        'title' => __('Start', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-start-v',
                    ],
                    'center' => [
                        'title' => __('Center', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-center-v',
                    ],
                    'flex-end' => [
                        'title' => __('End', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-end-v',
                    ],
                    'space-between' => [
                        'title' => __('Space Between', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-space-between-v',
                    ],
                    'space-around' => [
                        'title' => __('Space Around', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-space-around-v',
                    ],
                    'space-evenly' => [
                        'title' => __('Space Evenly', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-space-evenly-v',
                    ],
                ],
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-column' => 'justify-content: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'column_justify',
            [
                'label' => __('Justify Columns', 'better-sitemap-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'default' => 'flex-start',
                'options' => [
                    'flex-start' => [
                        'title' => __('Start', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-start-h',
                    ],
                    'center' => [
                        'title' => __('Center', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-center-h',
                    ],
                    'flex-end' => [
                        'title' => __('End', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-end-h',
                    ],
                    'space-between' => [
                        'title' => __('Space Between', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-space-between-h',
                    ],
                    'space-around' => [
                        'title' => __('Space Around', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-space-around-h',
                    ],
                    'space-evenly' => [
                        'title' => __('Space Evenly', 'better-sitemap-for-elementor'),
                        'icon' => 'eicon-justify-space-evenly-h',
                    ],
                ],
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-wrapper' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


    }

    /**
     * Recursively renders child pages in a hierarchical structure
     *
     * This method takes a parent page ID and recursively renders all its child pages
     * in a nested unordered list structure. It supports up to 2 levels of nesting.
     *
     * @since 1.0.0
     * @access private
     *
     * @param int $parent_id The ID of the parent page
     * @param int $level Current nesting level (1 or 2)
     * @param array|null $icon Icon settings from parent item
     * @param array $settings Widget settings containing styling options
     * 
     * @return void Outputs the HTML for child pages
     */
    private function render_child_pages($parent_id, $level = 1, $icon = null, $settings = [])
    {
        $child_pages = get_pages([
            'post_type' => 'page',
            'post_status' => 'publish',
            'parent' => $parent_id,
            'sort_column' => 'menu_order',
            'sort_order' => 'ASC',
        ]);

        if (!empty($child_pages)) {
            echo '<ul class="better-sitemap-list better-sitemap-child-level-' . intval($level) . '">';
            foreach ($child_pages as $child) {
                echo '<li  class="better-sitemap-single-item">';
                echo '<span>';

                // 💡 Determine if the icon should be shown based on level
                $show_icon = false;

                if ($level === 1 && !empty($settings['child_level_1_show_icon']) && $settings['child_level_1_show_icon'] === 'yes') {
                    $show_icon = true;
                }

                if ($level === 2 && !empty($settings['child_level_2_show_icon']) && $settings['child_level_2_show_icon'] === 'yes') {
                    $show_icon = true;
                }
                // Inherit icon from parent repeater item
                if ($show_icon && !empty($icon)) {
                    \Elementor\Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']);
                }


                echo '<a href="' . esc_url(get_permalink($child->ID)) . '">' . esc_html($child->post_title) . '</a>';
                echo '</span>';

                // Recursively continue with next level
                $this->render_child_pages($child->ID, $level + 1, $icon, $settings);

                echo '</li>';
            }
            echo '</ul>';
        }
    }


    /**
     * Renders the sitemap widget output on the frontend
     * 
     * This method:
     * 1. Gets the widget settings
     * 2. Checks if there are sitemap items configured
     * 3. Creates the main wrapper and list structure
     * 4. Iterates through each sitemap item to:
     *    - Get the post/page ID
     *    - Render the icon if enabled
     *    - Output the item link
     *    - Recursively render child pages for Page items
     * 5. Handles both Post and Page content types
     *
     * @since 1.0.0
     * @access protected
     * @return void Outputs the sitemap HTML structure
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (empty($settings['sitemap_items'])) {
            return;
        }

        echo '<div class="better-sitemap-wrapper">';
        echo '<ul class="better-sitemap-list better-sitemap-column">';

        foreach ($settings['sitemap_items'] as $item) {
            $post_id = null;
            if ($item['content_type'] === 'column_break') {
                echo '</ul><ul class="better-sitemap-list better-sitemap-column">';
                continue;
            }

            if ($item['content_type'] === 'Post' && !empty($item['posts_list'])) {
                $post_id = $item['posts_list'];
            } elseif ($item['content_type'] === 'Page' && !empty($item['pages_list'])) {
                $post_id = $item['pages_list'];
            }

            if ($post_id) {
                echo '<li class="better-sitemap-single-item">';
                echo '<span>';

                // Render icon from Elementor Icons control
                if (!empty($item['icon']) && 'yes' == $settings['show_icon']) {
                    \Elementor\Icons_Manager::render_icon($item['icon'], ['aria-hidden' => 'true']);
                }

                echo '<a href="' . esc_url(get_permalink($post_id)) . '">' . esc_html(get_the_title($post_id)) . '</a>';
                echo '</span>';

                // Start recursive children
                if ($item['content_type'] === 'Page') {
                    $this->render_child_pages($post_id, 1, $item['icon'], $settings);
                }

                echo '</li>';
            }
        }

        echo '</ul>';
        echo '</div>';
    }


}