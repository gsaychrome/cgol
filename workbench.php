<?php
/**
 * Created by PhpStorm.
 * User: Gergely Say
 * Date: 2016.07.08.
 * Time: 17:02
 */

// Op rendszer detektálás
$ISWIN = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$firsttime = !is_dir(__DIR__.'/workbench');

$nocomposer = !in_array('--clab',$argv);

// Felolvassuk az eredeti composer file-t
$config = json_decode(file_get_contents('composer.json'),true);

// Szétválogatjuk a clab2-es és nem clab2-es csomagokat, külön kezeljüka golapi-t
$wbpackages = [];
$wbpackages_a = [];
$require = [];
$packages = !empty($config['require-dev']) ? array_merge($config['require'],$config['require-dev']) : $config['require'];
foreach($packages as $package=>$ver) {
    if(substr($package,0,10)=='gsaychrome') {
        if(!is_dir(__DIR__.'/workbench/clab2/golapi')) {
            echo("git clone -b develop https://github.com/gsaychrome/golapi.git workbench/clab2/golapi\n");
            exec("git clone -b develop https://github.com/gsaychrome/golapi.git workbench/clab2/golapi");
        }
        else {
            echo("git pull workbench/clab2/golapi\n");
            exec("cd ".__DIR__."/workbench/clab2/golapi && git pull");
        }
        if(is_dir("workbench/clab2/golapi") && !is_dir("workbench//clab2/golapi/vendor")) {
            mkdir("workbench/clab2/golapi/vendor");
        }
        file_put_contents("workbench/clab2/golapi/vendor/autoload.php","<?php return require(dirname(dirname(dirname(dirname(__DIR__)))).'/vendor/autoload.php');");
    }
    else if(substr($package,0,5)=='clab2') {
        $wbpackages[] = $package;
    }
    else {
        $require[$package] = $ver;
    }
}

// környezet létrehozása ha még nincs
if($firsttime) {
    mkdir(__DIR__.'/workbench');
    mkdir(__DIR__.'/workbench/clab2');
}

// git password cache beállítása
if($ISWIN) {
    exec('git config --global credential.helper wincred');
}
else {
    exec('git config --global credential.helper store');
}

// clab2 csomagok kezelése git-en keresztül
for($i=0;$i<count($wbpackages);$i++) {
    $wbp = $wbpackages[$i];
    // Ha még nem volt
    if(!is_dir(__DIR__.'/workbench/'.$wbp)) {
        // Klónozzuk a megfelelő branchet
        switch ($wbp) {
            case 'clab2/module-tools':
            case 'clab2/client-generator':
                $branch = '';
                break;
            default:
                $branch = '-b develop';
        }
        echo("git clone $branch http://gitlab.chrome.hu/$wbp.git workbench/$wbp\n");
        exec("git clone $branch http://gitlab.chrome.hu/$wbp.git workbench/$wbp");
    }
    else {
        // Egyébként csak frissítünk
        echo("git pull workbench/$wbp\n");
        exec("cd ".__DIR__."/workbench/$wbp && git pull");
    }
    // megnézzük, hogy a letöltött clab2 modulban van-e olyan clab2 modul, amit még nem kezelünk
    if(is_file("workbench/$wbp/composer.json")) {
        $wbpconfig = json_decode(file_get_contents("workbench/$wbp/composer.json"), true);
        $wbppackages = !empty($wbpconfig['require-dev']) ? array_merge($wbpconfig['require'],
            $wbpconfig['require-dev']) : $wbpconfig['require'];
        foreach ($wbppackages as $package => $ver) {
            if (substr($package, 0, 5) == 'clab2') {
                if (!in_array($package, $wbpackages)) {
                    // A forciklus kiegészítése
                    $wbpackages[] = $package;
                }
            } else {
                if (empty($require[$package])) {
                    $require[$package] = $ver;
                }
            }
        }
    }
}

// Átalakítjuk a composer file-t
$config['require'] = $require;
unset($config['require-dev']);
unset($config['repositories']);

$config['autoload']['psr-4']["Clab2\\Golapi\\"] = "workbench/clab2/golapi/src/";
$config['autoload-dev']['psr-4']["Clab2\\Golapi\\Tests\\"] = "workbench/clab2/golapi/tests/";

// Clab2-es csomagok beállítása
foreach($wbpackages as $wbp) {
    // Speciális modulokhoz nem kell namespace
    if(in_array($wbp,['clab2/module-tools'])) {
        continue;
    }
    $nse = explode('/',$wbp);
    $ns = "Clab2\\".ucfirst($nse[1])."\\";
    // Hack a client-generator miatt, ami nem követi a névkonvenciót (grr*xx***!?)
    if($nse[1]=='client-generator') {
        $ns = "Clab2\\ClientGenerator\\";
    }
    $config['autoload']['psr-4'][$ns] = "workbench/".$wbp."/src/";
    $config['autoload-dev']['psr-4'][$ns."Tests\\"] = "workbench/".$wbp."/tests/";
    // autoload közvetlen a csomagokból
    if(is_dir("workbench/".$wbp) && !is_dir("workbench/".$wbp."/vendor")) {
        mkdir("workbench/" . $wbp . "/vendor");
    }
    // A csomag autoload-ja a központi helyről
    file_put_contents("workbench/".$wbp."/vendor/autoload.php","<?php return require(dirname(dirname(dirname(dirname(__DIR__)))).'/vendor/autoload.php');");
}

// Csak akkor futtatjuk a composert, ha nincs a clab kapcsoló beállítva
file_put_contents(__DIR__ . '/composer.workbench.json', str_replace('\/', '/', json_encode($config)));
putenv("COMPOSER=composer.workbench.json");
if($nocomposer) {
    if ($firsttime) {
        exec("composer install --prefer-source");
    }
    exec("composer update --prefer-source");
    unlink(__DIR__.'/composer.workbench.lock');
}
else {
    exec("composer dump-autoload");
}
unlink(__DIR__.'/composer.workbench.json');



