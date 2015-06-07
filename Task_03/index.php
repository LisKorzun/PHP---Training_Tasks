#!/usr/bin/php5
<?php

define("EXIT_KEY", 27);
define("H_INFO_FIELD", 10);
define("W_INFO_FIELD", 48);

spl_autoload_register(function ($class) {
    require_once 'src/' . strtolower($class) . '.php';
});

$gamer = new Gamer();
$gamer->playGame();
