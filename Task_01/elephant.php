#!/usr/bin/php5

<?php
if ($argc != 2) {
    $name = 'Anonymous';
} else {
    $name = $argv[1];
}
echo "\x1b[36m".$name."\x1b[0m".', buy elephant!!!'."\n";
$line = trim(fgets(STDIN));
while (strcasecmp($line, 'exit') !== 0){
    echo "\x1b[36m".$name."\x1b[0m". ', every can say '."\x1b[34;47m".'"'.$line.'"'."\x1b[0m".' and you take it and buy an elephant!!!'."\n";
    $line = trim(fgets(STDIN));
}
?>
