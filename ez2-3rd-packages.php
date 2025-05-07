<?php

/**
 * Plugin Name: Ez2 資源包
 * Version: 1.3.1
 * Author: Ez2.SHOP
 * Requires at least: 6.7
 * Requires PHP: 8.1
 *
 * License: GPL v3 or later
 **/

define('EZ2_3RD_VERSION', '1.3.1');
define('EZ2_3RD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EZ2_3RD_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('EZ2_DEV')) {
    define('EZ2_DEV', false);
}

require_once EZ2_3RD_PLUGIN_DIR . 'main.php';

function Ez2_3rd(): Ez2_3rd
{
    return Ez2_3rd::instance();
}

Ez2_3rd();
