<?php
// Minimal stand-ins for the Elementor classes the widget depends on.

namespace Elementor {
    class Widget_Base
    {
        public $settings = [];

        public function get_settings_for_display()
        {
            return $this->settings;
        }

        public function __call($name, $args)
        {
            return null;
        }
    }

    class Controls_Manager
    {
    }

    class Repeater
    {
        public function __call($name, $args)
        {
            return null;
        }
    }

    class Group_Control_Border { public static function get_type() { return 'border'; } }
    class Group_Control_Box_Shadow { public static function get_type() { return 'box-shadow'; } }
    class Group_Control_Background { public static function get_type() { return 'background'; } }
    class Group_Control_Typography { public static function get_type() { return 'typography'; } }

    class Icons_Manager
    {
        public static $rendered = 0;

        public static function render_icon($icon, $attrs = [])
        {
            self::$rendered++;
            echo '<i class="icon"></i>';
        }
    }

    class Preview
    {
        public $is_preview = false;

        public function is_preview_mode()
        {
            return $this->is_preview;
        }
    }

    class Plugin
    {
        public static $instance;
        public $preview;
    }

    Plugin::$instance = new Plugin();
    Plugin::$instance->preview = new Preview();
}
