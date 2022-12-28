<?php

namespace App {
    
    use Singleton\FrontController;

    //Dla czasu generowania strony
    define('TIME', microtime(true));
    
    //Stałe dla ścieżek
    //define('PATH',getcwd().'\\..\\');
    define('APP', __DIR__.'\\..\\src\\');
    define('MAIN', getcwd());
    define('VENDOR', '../vendor/');
    define('TEMPLATES', APP.'Templates/');
    define('TEMPLATES_C', APP.'Templates/templates_c/');

    //Ścieżki dla frontendu, problem w windowsie, metoda dirname zwraca \ zamiast /
    $path = (dirname($_SERVER["SCRIPT_NAME"]) == "\\") ? null : dirname($_SERVER["SCRIPT_NAME"]);
    define('ROOT', $path.'/');

    define('RESOURCE',ROOT.'resource/');
    define('STYLES', RESOURCE.'styles/');
    define('STYLE', 'resource/styles/');
    define('IMG', RESOURCE.'img/');
    define('JS', RESOURCE.'js/');
    define('BOOTSTRAP', RESOURCE.'bootstrap/5.2.3/');
    define('FONTAWESOME', RESOURCE.'fontawesome/6.2.1/');
    define('JQUERY', RESOURCE.'jquery/3.6.1/');

    //Implementacja autoloader z composera
    require_once VENDOR.'/autoload.php';
    
    //Uruchomienie sesji
    session_start();

    $f = new FrontController();
    $f->run();
}

?>