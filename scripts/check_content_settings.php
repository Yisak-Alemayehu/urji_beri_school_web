<?php
require_once dirname(__DIR__) . '/config/config.php';

$keys = [
    'about_mission', 'about_vision', 'about_values', 'about_overview',
    'director_name', 'director_title', 'director_message', 'director_image', 'director_quote'
];

foreach ($keys as $k) {
    $v = get_setting($k, '');
    $len = strlen((string) $v);
    $preview = $len ? substr(preg_replace('/\s+/', ' ', $v), 0, 80) : 'EMPTY';
    echo "{$k} [{$len}] {$preview}\n";
}
