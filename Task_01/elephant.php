#!/usr/bin/php5

<?php

define("BLUE_COLOR", "\x1b[36m");
define("DEL_COLOR", "\x1b[0m");
define("BLUE_COLOR_WITH_BACKGROUND", "\x1b[34;47m");
define("NEW_LINE", "\n");

if ($argc >2) {
    die ('Please, enter only one name as parameter!!!'."\n");
}
elseif ($argc != 2) {
    $name = 'Anonymous';
} else {
    $name = $argv[1];
}
printf('%1$s%4$s%2$s, buy an elephant!!!%3$s', BLUE_COLOR, DEL_COLOR, NEW_LINE, $name );
$line = trim(fgets(STDIN));
while (strcasecmp($line, 'exit') !== 0){
    printf('%1$s%5$s%3$s, every can say %2$s"%6$s"%3$s and you take and buy an elephant!!!%4$s', BLUE_COLOR, BLUE_COLOR_WITH_BACKGROUND, DEL_COLOR, NEW_LINE, $name, $line );
    $line = trim(fgets(STDIN));
}
?>
