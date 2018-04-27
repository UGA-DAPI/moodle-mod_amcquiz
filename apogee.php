<?php
/**
 * This pasge only handle apogee file generation and download.
 */
require_once __DIR__.'/locallib.php';
$service = new \mod_amcquiz\shared_service();
$curlmanager = new \mod_amcquiz\local\managers\curlmanager();

$service->parse_request();
$cm = $service->cm;
$course = $service->course;
$amcquiz = $service->amcquiz;

$context = context_module::instance($cm->id);
require_capability('mod/amcquiz:update', $context);
require_login($course, true, $cm);

global $CFG;
$tempdir = $CFG->dataroot.'/temp/amcquiz/';
if (!is_dir($tempdir)) {
    mkdir($tempdir);
}
srand(microtime() * 1000000);
$unique = str_replace('.', '', microtime(true).'_'.rand(0, 100000));
$filename = $unique.'grades_apogee.csv';
$filepath = $tempdir.$filename;
$output = fopen($filepath, 'w');
if (!$output) {
    return false;
}

fputcsv($output, array('id', 'name', 'surname', 'groups', 'mark'), ';');

$result = $curlmanager->get_grade_json($amcquiz);
$data = 200 === $result['status'] ? $result['data'] : [];
foreach ($data as $grade) {
    $idnumber = $grade['key'];
    fputcsv($output, array($grade['key'], $grade['name'], $grade['surname'], $grade['groups'], $grade['total_score']), ';');
}
fclose($output);

$content = file_get_contents($filepath);
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
echo $content;
unlink($filepath);
