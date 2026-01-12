<?php
/**
 * Return site statistics as JSON
 */
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/json; charset=utf-8');

$data = [
    'stat_students' => (int) get_setting('stat_students', 0),
    'stat_teachers' => (int) get_setting('stat_teachers', 0),
    'stat_experience' => (int) get_setting('stat_experience', 0),
    'stat_programs' => (int) get_setting('stat_programs', 0),
];

echo json_encode($data);
exit;
