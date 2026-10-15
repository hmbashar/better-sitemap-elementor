<?php
/**
 * PHPUnit bootstrap: loads Composer, defines minimal Elementor stubs and the widget.
 */

define('ABSPATH', __DIR__ . '/');
define('DAY_IN_SECONDS', 86400);

require_once dirname(__DIR__) . '/vendor/autoload.php';

require_once __DIR__ . '/stubs/elementor.php';
require_once dirname(__DIR__) . '/widgets/sitemap.php';
