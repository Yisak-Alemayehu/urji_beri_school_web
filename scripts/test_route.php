<?php
$_SERVER['REQUEST_URI'] = $argv[1] ?? '/about';
$_SERVER['HTTPS'] = 'on';
$_SERVER['HTTP_HOST'] = 'urjiberischool.test';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

ob_start();
try {
    require __DIR__ . '/../router.php';
    $out = ob_get_clean();
    echo 'URI=' . $_SERVER['REQUEST_URI'] . "\n";
    echo 'LEN=' . strlen($out) . "\n";
    echo 'HAS_TITLE=' . (str_contains($out, '<title>') ? 'yes' : 'no') . "\n";
    if (preg_match('/<title>(.*?)<\/title>/s', $out, $m)) {
        echo 'TITLE=' . trim(html_entity_decode(strip_tags($m[1]))) . "\n";
    }
    if (stripos($out, 'fatal') !== false || stripos($out, 'error') !== false && stripos($out, 'Parse') !== false) {
        echo "POSSIBLE_ERROR_IN_OUTPUT\n";
        echo substr($out, 0, 500) . "\n";
    }
} catch (Throwable $e) {
    ob_end_clean();
    echo 'EXCEPTION: ' . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}
