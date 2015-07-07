<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

$mongo = new MongoClient();
$db = $mongo->verkeerstellingen;
$collection = $db->tellingen;

$codes = [
    'AMSTD001' => 'T01',
    'AMSTD002' => 'T02',
    'AMSTD003' => 'T03',
    'AMSTD004' => 'T04',
    'AMSTD005' => 'T05',
    'AMSTD006' => 'T06',
    'AMSTD007' => 'T07',
    'AMSTD008' => 'T08',
    'AMSTD009' => 'T09',
    'AMSTD010' => 'T10',
    'AMSTD011' => 'T11',
    'AMSTD012' => 'T12+13',
    'AMSTD013' => 'T12+13',
    'AMSTD014' => 'T14',
    'AMSTD015' => 'T15',
    'AMSTD016' => 'T16',
    'AMSTD017' => 'T17+18',
    'AMSTD018' => 'T17+18',
    'AMSTD019' => 'T19',
    'AMSTD020' => 'T20',
    'AMSTD021' => 'T21',
    'AMSTD022' => 'T22+23',
    'AMSTD023' => 'T22+23',
    'AMSTD024' => 'T24',
    'AMSTD025' => 'T25',
    'AMSTD026' => 'T26',
    'AMSTD027' => 'T27',
    'AMSTD028' => 'T28'
];

$ops = [];

$match = [];
if (isset($_GET['telpunt']) && isset($codes[$_GET['telpunt']])) {
    $match = [
        '$match' => [
            'Telpunt' => $codes[$_GET['telpunt']]
        ]
    ];
}

if (!empty($match)) {
    $ops[] = $match;
}

if ($_GET['facet']) {
    switch($_GET['facet']) {
        case 'cat':
            $group = [
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
            ];
            break;
        case 'aantal':
            $group = [
                '$group' => [
                    '_id' => [
                        'Telpunt' => '$Telpunt',
                        'Richting' => '$Richting'
                    ],
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
            ];
            break;
        default:
            break;
    }
}

if (!empty($group)) {
    $ops[] = $group;
}

$tellingen = $collection->aggregate($ops);

//$tellingen = $collection->find();
//$tellingen->limit(5);

//$results = (object)[
//    'tellingen' => []
//];

//foreach($tellingen as $telling) {
//    $result = (object) $telling;
//    unset($result->{'_id'});
//    $results->tellingen[] = $result;
//}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode($tellingen);