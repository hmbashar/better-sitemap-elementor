<?php

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;
use BETTER_SITEMAP\Sitemap_Widget;

class SitemapWidgetTest extends TestCase
{
    private Sitemap_Widget $widget;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();

        Functions\stubs([
            'esc_url' => fn($v) => $v,
            'esc_html' => fn($v) => htmlspecialchars((string) $v),
            'is_admin' => false,
        ]);

        \Elementor\Icons_Manager::$rendered = 0;
        \Elementor\Plugin::$instance->preview->is_preview = false;
        $this->widget = new Sitemap_Widget();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    private function call(string $method, array $args = [])
    {
        $m = new ReflectionMethod($this->widget, $method);
        $m->setAccessible(true);
        return $m->invokeArgs($this->widget, $args);
    }

    private function render(array $settings): string
    {
        $this->widget->settings = $settings;
        ob_start();
        $this->call('render');
        return ob_get_clean();
    }

    public function test_metadata(): void
    {
        $this->assertSame('better-sitemap-widget', $this->widget->get_name());
        $this->assertSame('eicon-sitemap', $this->widget->get_icon());
        $this->assertSame(['basic'], $this->widget->get_categories());
        $this->assertSame(['better-sitemap-style'], $this->widget->get_style_depends());
    }

    public function test_option_lists_are_empty_outside_editor(): void
    {
        Functions\expect('get_posts')->never();
        Functions\expect('get_pages')->never();

        $this->assertSame([], $this->call('get_posts_list'));
        $this->assertSame([], $this->call('get_pages_list'));
    }

    public function test_posts_list_is_built_and_cached_in_editor(): void
    {
        Functions\when('is_admin')->justReturn(true);
        Functions\when('get_transient')->justReturn(false);
        Functions\expect('get_posts')->once()->andReturn([
            (object) ['ID' => 5, 'post_title' => 'Hello'],
        ]);
        Functions\expect('set_transient')->once()->with('better_sitemap_posts_list', [5 => 'Hello'], DAY_IN_SECONDS);

        $this->assertSame([5 => 'Hello'], $this->call('get_posts_list'));
    }

    public function test_posts_list_uses_cache(): void
    {
        Functions\when('is_admin')->justReturn(true);
        Functions\when('get_transient')->justReturn([1 => 'Cached']);
        Functions\expect('get_posts')->never();

        $this->assertSame([1 => 'Cached'], $this->call('get_posts_list'));
    }

    public function test_pages_list_available_in_preview_mode(): void
    {
        \Elementor\Plugin::$instance->preview->is_preview = true;
        Functions\when('get_transient')->justReturn(false);
        Functions\when('set_transient')->justReturn(true);
        Functions\expect('get_pages')->once()->andReturn([
            (object) ['ID' => 2, 'post_title' => 'About'],
        ]);

        $this->assertSame([2 => 'About'], $this->call('get_pages_list'));
    }

    public function test_render_outputs_nothing_without_items(): void
    {
        $this->assertSame('', $this->render(['sitemap_items' => []]));
    }

    public function test_render_published_post_is_escaped_and_linked(): void
    {
        Functions\when('get_post_status')->justReturn('publish');
        Functions\when('post_password_required')->justReturn(false);
        Functions\when('get_permalink')->justReturn('https://example.test/p/');
        Functions\when('get_the_title')->justReturn('A <b>post</b>');

        $html = $this->render([
            'show_icon' => 'yes',
            'sitemap_items' => [
                ['content_type' => 'Post', 'posts_list' => 7, 'icon' => ['value' => 'x']],
            ],
        ]);

        $this->assertStringContainsString('<a href="https://example.test/p/">A &lt;b&gt;post&lt;/b&gt;</a>', $html);
        $this->assertSame(1, \Elementor\Icons_Manager::$rendered);
    }

    public function test_render_skips_unpublished_deleted_and_protected_content(): void
    {
        Functions\when('get_post_status')->alias(fn($id) => [1 => 'draft', 2 => false, 3 => 'publish'][$id]);
        Functions\when('post_password_required')->alias(fn($id) => 3 === $id);
        Functions\expect('get_permalink')->never();

        $html = $this->render([
            'show_icon' => 'yes',
            'sitemap_items' => [
                ['content_type' => 'Post', 'posts_list' => 1, 'icon' => []],
                ['content_type' => 'Post', 'posts_list' => 2, 'icon' => []],
                ['content_type' => 'Post', 'posts_list' => 3, 'icon' => []],
            ],
        ]);

        $this->assertStringNotContainsString('<li', $html);
    }

    public function test_column_break_closes_and_opens_list(): void
    {
        $html = $this->render([
            'show_icon' => 'yes',
            'sitemap_items' => [['content_type' => 'column_break']],
        ]);

        $this->assertSame(
            '<div class="better-sitemap-wrapper"><ul class="better-sitemap-list better-sitemap-column"></ul><ul class="better-sitemap-list better-sitemap-column"></ul></div>',
            $html
        );
    }

    public function test_icon_hidden_when_show_icon_off(): void
    {
        Functions\when('get_post_status')->justReturn('publish');
        Functions\when('post_password_required')->justReturn(false);
        Functions\when('get_permalink')->justReturn('u');
        Functions\when('get_the_title')->justReturn('t');

        $this->render([
            'show_icon' => '',
            'sitemap_items' => [['content_type' => 'Post', 'posts_list' => 1, 'icon' => ['value' => 'x']]],
        ]);

        $this->assertSame(0, \Elementor\Icons_Manager::$rendered);
    }

    public function test_child_pages_render_nested_and_capped_at_two_levels(): void
    {
        // Parent 10 -> 11 -> 12 -> 13 (13 must not render: depth cap is 2).
        Functions\when('get_transient')->justReturn([
            10 => [['id' => 11, 'title' => 'L1', 'url' => 'u11']],
            11 => [['id' => 12, 'title' => 'L2', 'url' => 'u12']],
            12 => [['id' => 13, 'title' => 'L3', 'url' => 'u13']],
        ]);
        Functions\when('get_post_status')->justReturn('publish');
        Functions\when('post_password_required')->justReturn(false);
        Functions\when('get_permalink')->justReturn('top');
        Functions\when('get_the_title')->justReturn('Top');

        $html = $this->render([
            'show_icon' => 'yes',
            'child_level_1_show_icon' => 'yes',
            'child_level_2_show_icon' => '',
            'sitemap_items' => [['content_type' => 'Page', 'pages_list' => 10, 'icon' => ['value' => 'x']]],
        ]);

        $this->assertStringContainsString('better-sitemap-child-level-1', $html);
        $this->assertStringContainsString('better-sitemap-child-level-2', $html);
        $this->assertStringNotContainsString('better-sitemap-child-level-3', $html);
        $this->assertStringContainsString('>L1</a>', $html);
        $this->assertStringContainsString('>L2</a>', $html);
        $this->assertStringNotContainsString('L3', $html);
        // Top icon + level-1 icon only (level 2 icon disabled).
        $this->assertSame(2, \Elementor\Icons_Manager::$rendered);
    }
}
