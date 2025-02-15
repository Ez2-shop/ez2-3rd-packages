<?php

/**
 * Plugin Name: Ez2 資源包
 * Version: 1.2.8
 * Author: Ez2.SHOP
 * Requires at least: 6.7
 * Requires PHP: 8.1
 *
 * License: GPL v2 or later
 **/

define('EZ2_3P_VERSION', '1.2.8');
define('EZ2_3P_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EZ2_3P_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('EZ2_DEV')) {
    define('EZ2_DEV', false);
}

require_once EZ2_3P_PLUGIN_DIR . 'main.php';

function Ez2_3P(): Ez2_3P
{
    return Ez2_3P::instance();
}

Ez2_3P();
