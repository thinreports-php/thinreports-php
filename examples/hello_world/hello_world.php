<?php
require __DIR__ . '/../../vendor/autoload.php';

date_default_timezone_set('Asia/Tokyo');

use Thinreports\Report;

$report = new Report(__DIR__ . '/hello_world.tlf');

$page = $report->addPage();
$page->item('world')->setValue('World');
$page('sekai')->setValue('世界');

$report->addPage(null, function ($new_page) {
    $new_page->item('world')->setValue('PHP')
                            ->setStyle('color', 'blue');
    $new_page->item('sekai')->setValue('帳票');
});

$page = $report->addPage();
$page->setItemValues([
    'world' => 'Thinreports',
    'sekai' => 'PDF'
]);

$report->generate(__DIR__ . '/hello_world.pdf');
