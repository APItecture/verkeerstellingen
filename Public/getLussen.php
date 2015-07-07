<?php

$mongo = new MongoClient();
$db = $mongo->verkeerstellingen;
$lussen = $db->lussen->find();
$results = (object)[
    'lussen' => []
];

foreach($lussen as $lus) {
    $result = (object) $lus;
    unset($result->{'_id'});
    $results->lussen[] = $result;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode($results);