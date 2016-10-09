<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/16
 * Time: 17:44
 */

namespace Common\Util;


class ConfigManager
{
    public $config;
    public function __construct()
    {
        $configfile = BASE_DIR . 'config/config';
        $content = file_get_contents($configfile);
        $content = preg_replace('/\/\*(.*)\*\//', '', $content);
        $content = json_decode($content);
        $content = is_null($content) ? new \stdClass() : $content;
        $this->config = $content;
    }
}