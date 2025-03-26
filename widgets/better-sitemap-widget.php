<?php
namespace Elementor;

if (!defined('ABSPATH'))
    exit;

class Better_Sitemap_Widget extends Widget_Base
{

    public function get_name()
    {
        return 'better_sitemap';
    }

    public function get_title()
    {
        return __('Better Sitemap', 'plugin-name');
    }

    public function get_icon()
    {
        return 'eicon-sitemap';
    }

    public function get_categories()
    {
        return ['general'];
    }

    protected function register_controls()
    {

        // ========================
        // CONTENT: Sitemap Items
        // ========================
        $this->start_controls_section('content_section', [
            'label' => __('Better Sitemap Items', 'plugin-name'),
        ]);

        // Páginas e posts
        $pages = get_pages(['sort_column' => 'menu_order, post_title', 'parent' => 0]);
        $page_options = [];
        foreach ($pages as $page) {
            $page_options[$page->ID] = $page->post_title;
        }

        $posts = get_posts(['numberposts' => -1, 'post_type' => 'post']);
        $post_options = [];
        foreach ($posts as $post) {
            $post_options[$post->ID] = $post->post_title;
        }

        $repeater = new Repeater();

        $repeater->add_control('item_type', [
            'label' => __('Item Type', 'plugin-name'),
            'type' => Controls_Manager::SELECT,
            'default' => 'page',
            'options' => [
                'page' => __('Page', 'plugin-name'),
                'post' => __('Post', 'plugin-name'),
                'column_break' => __('Column Break', 'plugin-name'),
            ],
        ]);

        $repeater->add_control('page_id', [
            'label' => __('Page', 'plugin-name'),
            'type' => Controls_Manager::SELECT,
            'options' => $page_options,
            'condition' => ['item_type' => 'page'],
        ]);

        $repeater->add_control('page_title', [
            'type' => Controls_Manager::HIDDEN,
            'default' => '',
        ]);

        $repeater->add_control('post_id', [
            'label' => __('Post', 'plugin-name'),
            'type' => Controls_Manager::SELECT,
            'options' => $post_options,
            'condition' => ['item_type' => 'post'],
        ]);

        $repeater->add_control('post_title', [
            'type' => Controls_Manager::HIDDEN,
            'default' => '',
        ]);

        $this->add_control('sitemap_items', [
            'label' => __('Sitemap Items', 'plugin-name'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '{{{ item_type === "column_break" ? "— Column Break —" : ( item_type === "page" ? ( page_title ? page_title : "Page" ) : ( item_type === "post" ? ( post_title ? post_title : "Post" ) : "" ) ) }}}',
        ]);

        $this->end_controls_section();

        // ========================
        // STYLE: Levels 1, 2, 3
        // ========================

        for ($i = 1; $i <= 3; $i++) {
            $this->start_controls_section("style_level{$i}_section", [
                'label' => __("Level $i", 'plugin-name'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]);

            $this->add_control("show_icon_level{$i}", [
                'label' => __('Show Icon', 'plugin-name'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]);

            $this->add_control("icon_level{$i}", [
                'label' => __('Icon', 'plugin-name'),
                'type' => Controls_Manager::ICONS,
                'default' => ['value' => 'fas fa-circle', 'library' => 'fa-solid'],
                'condition' => ["show_icon_level{$i}" => 'yes'],
            ]);

            $this->add_responsive_control("icon_size_level{$i}", [
                'label' => __('Icon Size', 'plugin-name'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 10, 'max' => 100],
                ],
                'selectors' => [
                    "{{WRAPPER}} .sitemap-item.level-{$i} .item-icon" => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => ["show_icon_level{$i}" => 'yes'],
            ]);

            $this->add_control("icon_color_level{$i}", [
                'label' => __('Icon Color', 'plugin-name'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'condition' => ["show_icon_level{$i}" => 'yes'],
                'selectors' => [
                    "{{WRAPPER}} .sitemap-item.level-{$i} .item-icon" => 'color: {{VALUE}};',
                ],
            ]);

            $this->start_controls_tabs("tabs_typography_level{$i}");

            $this->start_controls_tab("tab_typography_level{$i}_normal", [
                'label' => __('Normal', 'plugin-name'),
            ]);
            $this->add_group_control(Group_Control_Typography::get_type(), [
                'name' => "typography_level{$i}_normal",
                'selector' => "{{WRAPPER}} .sitemap-item.level-{$i} a",
            ]);
            $this->end_controls_tab();

            $this->start_controls_tab("tab_typography_level{$i}_hover", [
                'label' => __('Hover', 'plugin-name'),
            ]);
            $this->add_group_control(Group_Control_Typography::get_type(), [
                'name' => "typography_level{$i}_hover",
                'selector' => "{{WRAPPER}} .sitemap-item.level-{$i} a:hover",
            ]);
            $this->end_controls_tab();

            $this->start_controls_tab("tab_typography_level{$i}_active", [
                'label' => __('Active', 'plugin-name'),
            ]);
            $this->add_group_control(Group_Control_Typography::get_type(), [
                'name' => "typography_level{$i}_active",
                'selector' => "{{WRAPPER}} .sitemap-item.level-{$i} a:active",
            ]);
            $this->end_controls_tab();

            $this->end_controls_tabs();


            $this->add_control("color_level{$i}", [
                'label' => __('Text Color', 'plugin-name'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    "{{WRAPPER}} .sitemap-item.level-{$i} a" => 'color: {{VALUE}};',
                ],
            ]);

            $this->add_responsive_control("margin_left_level{$i}", [
                'label' => __('Margin Left', 'plugin-name'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 100],
                ],
                'selectors' => [
                    "{{WRAPPER}} .sitemap-item.level-{$i}" => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->end_controls_section();
        }

        // ========================
        // STYLE: Columns
        // ========================
        $this->start_controls_section('style_columns_section', [
            'label' => __('Column Settings', 'plugin-name'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('column_width', [
            'label' => __('Column Width', 'plugin-name'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => ['min' => 100, 'max' => 500],
            ],
            'selectors' => [
                '{{WRAPPER}} .sitemap-column' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('columns_alignment', [
            'label' => __('Alignment', 'plugin-name'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => __('Left', 'plugin-name'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'plugin-name'),
                    'icon' => 'eicon-text-align-center',
                ],
                'flex-end' => [
                    'title' => __('Right', 'plugin-name'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'flex-start',
            'selectors' => [
                '{{WRAPPER}} .better-sitemap' => 'justify-content: {{VALUE}};',
            ],
        ]);

        $this->add_control('content_alignment', [
            'label' => __('Content Alignment', 'plugin-name'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => __('Left', 'plugin-name'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'plugin-name'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => __('Right', 'plugin-name'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .better-sitemap' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    /**
     * Renders subpages recursively.
     *
     * @param int $parent_id The parent page ID.
     * @param int $level The current level (starts at 2).
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $items = $settings['sitemap_items'];

        echo '<div class="better-sitemap" style="display: flex; flex-wrap: wrap; gap: 30px;">';
        echo '<div class="sitemap-column">';

        foreach ($items as $item) {
            if ($item['item_type'] === 'column_break') {
                echo '</div><div class="sitemap-column">';
                continue;
            }

            $type = $item['item_type'];
            $id = $type === 'page' ? ($item['page_id'] ?? null) : ($item['post_id'] ?? null);
            if (!$id)
                continue;

            echo '<div class="sitemap-item level-1">';

            if ($settings['show_icon_level1'] === 'yes' && !empty($settings['icon_level1'])) {
                Icons_Manager::render_icon($settings['icon_level1'], [
                    'aria-hidden' => 'true',
                    'class' => 'item-icon',
                ]);
            }

            echo '<a href="' . esc_url(get_permalink($id)) . '">';
            echo esc_html(get_the_title($id));
            echo '</a>';

            if ($type === 'page') {
                $this->render_subpages($id, 2, $settings);
            }

            echo '</div>';
        }

        echo '</div>'; // .sitemap-column
        echo '</div>'; // .better-sitemap
    }

    protected function render_subpages($parent_id, $level = 2, $settings)
    {
        $children = get_pages([
            'child_of' => $parent_id,
            'sort_column' => 'menu_order',
        ]);

        if (empty($children))
            return;

        foreach ($children as $child) {
            echo '<div class="sitemap-item level-' . esc_attr($level) . '">';

            // Render ícone conforme o nível
            if ($settings["show_icon_level{$level}"] === 'yes' && !empty($settings["icon_level{$level}"])) {
                Icons_Manager::render_icon($settings["icon_level{$level}"], [
                    'aria-hidden' => 'true',
                    'class' => 'item-icon',
                ]);
            }

            echo '<a href="' . esc_url(get_permalink($child->ID)) . '">';
            echo esc_html(get_the_title($child->ID));
            echo '</a>';

            // Recursão limitada até o nível 3
            if ($level < 3) {
                $this->render_subpages($child->ID, $level + 1, $settings);
            }

            echo '</div>';
        }
    }

} // end class Better_Sitemap_Widget

