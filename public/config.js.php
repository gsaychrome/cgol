<?php
define('APP_PATH', dirname(__DIR__));
require_once(dirname(__DIR__).'/vendor/autoload.php');

$config = include(dirname(__DIR__).'/config/clab2.php');
$environment = include(dirname(__DIR__).'/config/environment.php');

$config = array_merge_recursive($config, $environment);

$jsconfig = [
    "baseHost"          => isset($config['site_url']) ? $config['site_url'] : "http://".$_SERVER['SERVER_NAME'],
    "endPointBase"      => "/clab2/rest",
    "modulePath"        => $config['frontend']['module_path'],
    "modules"           => $config['frontend']['modules'],
    "language"          => $config['language'],
    "languageFromUrl"   => true,
    "device"            => "web"
];
//file_put_contents('config.js',"var config =".json_encode($jsconfig));
header('Content-Type: application/json');
echo "var config =".json_encode($jsconfig);