<?php
/**
 * Return site statistics as JSON
 */
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/json; charset=utf-8');

$data = [
    'stat_students' => (int) get_setting('stat_students', 550),
    'stat_teachers' => (int) get_setting('stat_teachers', 30),
    'stat_experience' => (int) get_setting('stat_experience', 18),
    'stat_programs' => (int) get_setting('stat_programs', 98),
];

echo json_encode($data);
exit;
