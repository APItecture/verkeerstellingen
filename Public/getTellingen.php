<?php

$mongo = new MongoClient();
$db = $mongo->verkeerstellingen;
$collection = $db->tellingen;

$ops = [
    [
        '$match' => [
            'Telpunt' => 'T12+13'
        ]
    ],
    [
        '$group' => [
            '_id' => '$Telpunt',
            'Cat1' => [
                '$sum' => '$Cat1'
            ],
            'Cat2' => [
                '$sum' => '$Cat2'
            ],
            'Cat3' => [
                '$sum' => '$Cat3'
            ],
            'Cat4' => [
                '$sum' => '$Cat4'
            ],
            'Cat5' => [
                '$sum' => '$Cat5'
            ],
            'Cat6' => [
                '$sum' => '$Cat6'
            ]
        ]
    ]
];

$tellingen = $collection->aggregate($ops);
echo json_encode($tellingen);
die;

//$tellingen = $collection->find();
//$tellingen->limit(5);

$results = (object)[
    'tellingen' => []
];

foreach($tellingen as $telling) {
    $result = (object) $telling;
    unset($result->{'_id'});
    $results->tellingen[] = $result;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode($results);