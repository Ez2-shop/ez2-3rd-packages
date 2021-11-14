<?php
final class EZ2_3P
{
    private static $initiated = false;

    public static function init()
    {
        if (!self::$initiated) {
            self::$initiated = true;

            add_filter('ez2/update_plugin_list', [__CLASS__, 'add_plugin'], 1);
        }
    }

    public static function add_plugin($plugin_list)
    {
        $plugin_list[] = [
            'name' => 'Ez2 資源包',
            'slug' => 'ez2-3rd-packages',
            'now_version' => EZ2_3P_VERSION
        ];

        return $plugin_list;
    }
}
