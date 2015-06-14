#!/usr/bin/php5
<?php

define("H_INFO_FIELD", 10);
define("W_INFO_FIELD", 48);

define("EXIT_KEY", 27);
define("NULL_KEY", 48);
define("ONE_KEY", 49);
define("TWO_KEY", 50);
define("THREE_KEY", 51);
define("ENTER_KEY", 13);
define("SPACE_KEY", 32);
define("LEFT_KEY", 37);
define("UP_KEY", 38);
define("RIGHT_KEY", 39);
define("DOWN_KEY", 40);

define("XRIGHT", 'xRight');
define("XLEFT", 'xLeft');
define("YUP", 'yUp');
define("YDOWN", 'yDown');

spl_autoload_register(function ($class) {
    require_once 'src/' . strtolower($class) . '.php';
});

$painter = new DrawingNcurses();
$listener = new ListenerNcurses();

$game = new Game();
$gamer = new Gamer($game->getH(), $game->getW());

$game->setPainter($painter);
$game->setListener($listener);
$game->setGamer($gamer);
$game->playGame();
