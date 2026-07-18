<?php
require_once dirname(__DIR__) . '/config/config.php';

$m = get_setting('director_message', '');
$m = str_replace(["\u{2013}", "\u{2014}", '???'], ['-', '-', '-'], $m);
$m = preg_replace('/UBS\s*-+\s*a great place/', 'UBS - a great place', $m);
update_setting('director_message', $m);
echo "Fixed. Tail: " . substr($m, -90) . "\n";
