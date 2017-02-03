<?php
// Teszt jeleggel korlátoztuk a memoria limitet, hogy kiszűrjük a túl nagyra dagadó REST API-kat. Speciális esetben
// (pl képfeltöltés) a konkrét service-ben át kell állítani!!!
ini_set('memory_limit','64M');

require_once(dirname(__DIR__).'/vendor/autoload.php');
define('APP_PATH', dirname(__DIR__));

$config = include(dirname(__DIR__).'/config/clab2.php');
$environment = include(dirname(__DIR__).'/config/environment.php');
$config = array_merge_recursive($config, $environment);
// itt már teljesen mindegy melyik adaptert használjuk, mert azokat már szétteszteltük, ezért az egyszerűség
// kedvéért a doctrine mongót fogjuk használni.

try {
    ob_start();
    \Clab2\Application\Toolkit::initilize($config);
    $toolkit = \Clab2\Application\Toolkit::getInstance();

    // Összeszedjük a configból azokat a modulokat, akiknek van regisztrálni valójuk a rest api-ba
    $modules = [];
    foreach($config as $module=>$mconfig) {
        if(!empty($mconfig['rest']['register'])) {
            $modules[] = $module;
        }
    }

    // Regisztráljuk őket
    foreach($modules as $module) {
        $toolkit->rest->register($module);
    }

    // Kérés kiszolgálása
    $toolkit->rest->handle();

    ob_end_flush();

} catch (Exception $e) {
    ob_get_clean();
    header("HTTP/1.0 500 Internal server error");
    if (getenv('APP_ENV') === 'dev') {
        $message = 'Error: ' . $e->getMessage() . '<br />File: ' . $e->getFile() . '<br />Line: ' . $e->getLine() . '<br /><br />';
        $message .= 'Trace:<br />' . $e->getTraceAsString();
        echo $message;
    }
    else {
        echo "The BFTK backend server encountered an unexpected condition which prevented it from fulfilling the request.";
    }
}