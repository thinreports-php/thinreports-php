<?php
require __DIR__ . '/../../vendor/autoload.php';

date_default_timezone_set('Asia/Tokyo');

use Thinreports\Report;

$report = new Report(__DIR__ . '/hello_world.tlf');

$page = $report->addPage();
$page->item('world')->setValue('World');
$page->item('sekai')->setValue('世界');

$page = $report->addPage();
$page('world')->setValue('World');
$page('sekai')->setValue('世界');

$page = $report->addPage();
$page->setItemValue('world', 'World');
$page->setItemValue('sekai', '世界');

$report->addPage(function ($new_page) {
    $new_page('world')->setValue('World')->setStyle('color', 'blue');
    $new_page('sekai')->setValue('世界');
});

$page = $report->addPage();
$page->setItemValues([
    'world' => 'World',
    'sekai' => '世界'
]);

$report->addPage()->setItemValues([
    'world' => 'World',
    'sekai' => '世界'
]);

$report->generate(__DIR__ . '/hello_world.pdf');
