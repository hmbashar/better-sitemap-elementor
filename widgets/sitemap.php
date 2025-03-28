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
    }

    private function render_child_pages($parent_id, $level = 1, $icon = null) {
        $child_pages = get_pages([
            'post_type'   => 'page',
            'post_status' => 'publish',
            'parent'      => $parent_id,
            'sort_column' => 'menu_order',
            'sort_order'  => 'ASC',
        ]);
    
        if (!empty($child_pages)) {
            echo '<ul class="better-sitemap-child-level-' . intval($level) . '">';
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
    
    
    protected function render() {
        $settings = $this->get_settings_for_display();
    
        if (empty($settings['sitemap_items'])) {
            return;
        }
    
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
                if (!empty($item['icon'])) {
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
    }
    

}