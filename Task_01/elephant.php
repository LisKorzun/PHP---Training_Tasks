#!/usr/bin/php5

<?php

($argc <= 2) || die ("Please, enter only one name as parameter!!!\n");

define("USER_NAME_DECOR", "\x1b[36m");  //blue text
define("DEL_DECOR", "\x1b[0m");
define("USER_MSG_DECOR", "\x1b[34;47m");  //blue text with white background
define("NEW_LINE", "\n");

$name = ($argc != 2) ? 'Anonymous': $argv[1];

printf('%1$s%4$s%2$s, buy an elephant!!!%3$s', USER_NAME_DECOR, DEL_DECOR, NEW_LINE, $name );
$userMsg = trim(fgets(STDIN));

$str = '%1$s%5$s%3$s, every can say %2$s"%6$s"%3$s and you take and buy an elephant!!!%4$s';
$message = sprintf($str, USER_NAME_DECOR, USER_MSG_DECOR, DEL_DECOR, NEW_LINE, '%1$s', '%2$s');
while (strcasecmp($userMsg, 'exit') !== 0){
    printf($message, $name, $userMsg);
    $userMsg = trim(fgets(STDIN));
}
?>
