<?php
namespace Elementor;

if (!defined('ABSPATH'))
    exit;

class Sitemap_Widget extends Widget_Base
{
    public function get_name()
    {
        return 'better-sitemap-widget';
    }
    public function get_title()
    {
        return __('Better Sitemap', 'better-sitemap-elementor');
    }
    public function get_icon()
    {
        return 'eicon-posts-grid';
    }
    public function get_categories()
    {
        return ['basic'];
    }
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

    protected function register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'better-sitemap-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'content_type',
            [
                'label' => __('Content Type', 'better-sitemap-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'Post',
                'options' => [
                    'Post' => __('Posts', 'better-sitemap-elementor'),
                    'Page' => __('Pages', 'better-sitemap-elementor'),
                ],
            ]
        );

        $repeater->add_control(
            'posts_list',
            [
                'label' => __('Select Posts', 'better-sitemap-elementor'),
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
                'label' => __('Select Pages', 'better-sitemap-elementor'),
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
                'label' => __('Icon', 'better-sitemap-elementor'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-circle',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'sitemap_items',
            [
                'label' => __('Sitemap Items', 'better-sitemap-elementor'),
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
                'title_field' => '{{{ content_type }}}',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'better-sitemap-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'list_width',
            [
                'label' => __('Width', 'better-sitemap-elementor'),
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
                    '{{WRAPPER}} .better-sitemap-list' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'list_spacing',
            [
                'label' => __('List Gap', 'better-sitemap-elementor'),
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
                'label' => __('Level Gap', 'better-sitemap-elementor'),
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
            'list_direction',
            [
                'label' => __('Direction', 'better-sitemap-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'column',
                'options' => [
                    'column' => [
                        'title' => __('Column', 'better-sitemap-elementor'),
                        'icon' => 'eicon-arrow-down',
                    ],
                    'row' => [
                        'title' => __('Row', 'better-sitemap-elementor'),
                        'icon' => 'eicon-arrow-right',
                    ],
                    'row-reverse' => [
                        'title' => __('Row Reverse', 'better-sitemap-elementor'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'column-reverse' => [
                        'title' => __('Column Reverse', 'better-sitemap-elementor'),
                        'icon' => 'eicon-arrow-up',
                    ],
                ],
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list' => 'flex-direction: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'list_alignment',
            [
                'label' => __('Alignment', 'better-sitemap-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'better-sitemap-elementor'),
                        'icon' => 'eicon-align-start-h',
                    ],
                    'center' => [
                        'title' => __('Center', 'better-sitemap-elementor'),
                        'icon' => 'eicon-align-center-h',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'better-sitemap-elementor'),
                        'icon' => 'eicon-align-end-h',
                    ],
                ],
                'default' => 'flex-start',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'list_padding',
            [
                'label' => __('Padding', 'better-sitemap-elementor'),
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
                'label' => __('Margin', 'better-sitemap-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'list_background',
                'label' => __('Background', 'better-sitemap-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .better-sitemap-wrapper',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'list_border',
                'label' => __('Border', 'better-sitemap-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-wrapper',
            ]
        );

        $this->add_responsive_control(
            'list_border_radius',
            [
                'label' => __('Border Radius', 'better-sitemap-elementor'),
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
                'label' => __('Box Shadow', 'better-sitemap-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-wrapper',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'item_style_section',
            [
                'label' => __('Item Style', 'better-sitemap-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'show_icon',
            [
                'label' => __('Show Icon', 'better-sitemap-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'better-sitemap-elementor'),
                'label_off' => __('Hide', 'better-sitemap-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'better-sitemap-elementor'),
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
                'label' => __('Icon Size', 'better-sitemap-elementor'),
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
                'label' => __('Icon Spacing', 'better-sitemap-elementor'),
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
                'label' => __('Typography', 'better-sitemap-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-list a',
            ]
        );

        $this->start_controls_tabs('text_style_tabs');

        $this->start_controls_tab(
            'text_normal_tab',
            [
                'label' => __('Normal', 'better-sitemap-elementor'),
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'better-sitemap-elementor'),
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
                'label' => __('Hover', 'better-sitemap-elementor'),
            ]
        );

        $this->add_control(
            'text_hover_color',
            [
                'label' => __('Text Color', 'better-sitemap-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0066CC',
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-list a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        $this->start_controls_section(
            'child_level_2_style_section',
            [
                'label' => __('Child Level 2 Style', 'better-sitemap-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'child_level_2_indent',
            [
                'label' => __('Indent', 'better-sitemap-elementor'),
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
                'label' => __('Typography', 'better-sitemap-elementor'),
                'selector' => '{{WRAPPER}} .better-sitemap-child-level-2 a',
            ]
        );

        $this->add_control(
            'child_level_2_show_icon',
            [
                'label' => __('Show Icon', 'better-sitemap-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'better-sitemap-elementor'),
                'label_off' => __('Hide', 'better-sitemap-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'child_level_2_icon_color',
            [
                'label' => __('Icon Color', 'better-sitemap-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
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
                'label' => __('Icon Size', 'better-sitemap-elementor'),
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
                    'size' => 14,
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
                'label' => __('Normal', 'better-sitemap-elementor'),
            ]
        );

        $this->add_control(
            'child_level_2_text_color',
            [
                'label' => __('Text Color', 'better-sitemap-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-2 a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'child_level_2_hover_tab',
            [
                'label' => __('Hover', 'better-sitemap-elementor'),
            ]
        );

        $this->add_control(
            'child_level_2_hover_color',
            [
                'label' => __('Text Color', 'better-sitemap-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0066CC',
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-2 a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'child_level_2_icon_hover_color',
            [
                'label' => __('Icon Hover Color', 'better-sitemap-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#0066CC',
                'selectors' => [
                    '{{WRAPPER}} .better-sitemap-child-level-2 .better-sitemap-single-item:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .better-sitemap-child-level-2 .better-sitemap-single-item:hover svg' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'child_level_2_show_icon' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


    }

    private function render_child_pages($parent_id, $level = 1, $icon = null)
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

                // Inherit icon from parent repeater item
                if (!empty($icon)) {
                    \Elementor\Icons_Manager::render_icon($icon, ['aria-hidden' => 'true']);
                }

                echo '<a href="' . esc_url(get_permalink($child->ID)) . '">' . esc_html($child->post_title) . '</a>';
                echo '</span>';

                // Recursively continue with next level
                $this->render_child_pages($child->ID, $level + 1, $icon);

                echo '</li>';
            }
            echo '</ul>';
        }
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (empty($settings['sitemap_items'])) {
            return;
        }

        echo '<div class="better-sitemap-wrapper">';
        echo '<ul class="better-sitemap-list">';

        foreach ($settings['sitemap_items'] as $item) {
            $post_id = null;

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
                    $this->render_child_pages($post_id, 1, $item['icon']);
                }

                echo '</li>';
            }
        }

        echo '</ul>';
        echo '</div>';
    }


}