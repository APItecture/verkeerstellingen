<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

date_default_timezone_set('Europe/Amsterdam'); //FIXME

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

$match = [
    '$match' => [
        'Telpunt' => $codes[$_GET['telpunt']]
    ]
];

if (!empty($_GET['datum_vanaf'])) {
    $match['$match']['date']['$gte'] = new MongoDate(strtotime($_GET["datum_vanaf"]));
}
if (!empty($_GET['datum_tot'])) {
    $match['$match']['date']['$lt'] = new MongoDate(strtotime($_GET["datum_tot"]));
}
if (!empty($_GET['uren_filter'])) {
    $match['$match']['uur'] = [
        '$in' => $_GET['uren_filter']
    ];
}
if (!empty($_GET['weekdagen_filter'])) {
    $match['$match']['weekdag'] = [
        '$in' => $_GET['weekdagen_filter']
    ];
}

switch (isset($_GET['groeperen_per']) ? $_GET['groeperen_per'] : "jaar") {
    case "uur":
        $groepering = [
            'richting' => '$Richting',
            'datum' => ['$concat' => ['$jaar', '-', '$maand', '-', '$dag', ' ',  '$Uur']]
        ];
        break;
    case "dag":
    case "etmaal":
        $groepering = [
            'richting' => '$Richting',
            'datum' => ['$concat' => ['$jaar', '-', '$maand', '-', '$dag', ' ', '0:00']]
        ];
        break;
    //case "week": // FIXME
    //    $groepering = [
    //        'richting' => '$Richting',
    //        //'jaar' => ['$year' => '$date'],
    //        //'week' => ['$week' => '$date']
    //    ];
    //    break;
    case "maand":
        $groepering = [
            'richting' => '$Richting',
            'datum' => ['$concat' => ['$jaar', '-', '$maand', '-', '1', ' ', '0:00']]
        ];
        break;
    case "jaar":
    default:
        $groepering = [
            'richting' => '$Richting',
            'datum' => ['$concat' => ['$jaar', '-', '1', '-', '1', ' ', '0:00']]
        ];
}

$group = [
    '$group' => [
        '_id' => $groepering,
        'totaal' => ['$sum' => '$Totaal'],
        'PA' => ['$sum' => '$PA'],
        'LV' => ['$sum' => '$LV'],
        'MV' => ['$sum' => '$MV'],
        'ZV' => ['$sum' => '$ZV']
    ]
];

$ops = [$match, $group];

$tellingen = $collection->aggregate($ops);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode([
    'tellingen' => $tellingen['result'],
    'ops' => $ops
]);