<?php
$root = dirname(__DIR__);
$loader = require $root.'/vendor/autoload.php';
$loader->add('', $root.'/classes/');

$pixie = new \App\Pixie();
$pixie->bootstrap($root)->http_request()->execute()->send_headers()->send_body();
?>
