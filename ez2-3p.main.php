<?php

final class Ez2_3P
{
    protected static $_instance = null;

    public static function instance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
            self::$_instance->do_init();
        }

        return self::$_instance;
    }

    protected function do_init()
    {
        add_filter('ez2/update_plugin_list', [$this, 'add_plugin'], 1);
    }

    public function add_plugin($plugin_list)
    {
        $plugin_list[] = [
            'name' => 'Ez2 資源包',
            'slug' => 'ez2-3rd-packages',
            'now_version' => EZ2_3P_VERSION
        ];

        return $plugin_list;
    }
}
