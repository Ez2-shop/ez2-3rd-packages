<?php
/**
 * Plugin Name: Ez2 資源包
 * Version: 1.1.4
 * Author: Ez2.SHOP
 * Requires at least: 5.6
 * Requires PHP: 8.0.0
 *
 * License: GPL v2 or later
 **/

define('EZ2_3P_VERSION', '1.1.4');
define('EZ2_3P_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EZ2_3P_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once EZ2_3P_PLUGIN_DIR . 'ez2-3p.main.php';

function Ez2_3P(): Ez2_3P
{
    return Ez2_3P::instance();
}

Ez2_3P();
