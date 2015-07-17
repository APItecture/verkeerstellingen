<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

$mongo = new MongoClient();
$db = $mongo->verkeerstellingen;

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

$groepering = isset($_GET['groeperen_per']) ? $_GET['groeperen_per'] : "jaar";

switch ($groepering) {
    case "uur":
        $groepering = "{
            richting: this.Richting,
            bucket: this.date.getFullYear() + '-' +
                    (this.date.getMonth() + 1) + '-' + 
                    this.date.getDate() + ' ' +
                    this.date.getHours() + ':00'
        }";
        break;
    case "etmaal":
        $groepering = "{
            richting: this.Richting,
            bucket: this.date.getFullYear() + '-' +
                    (this.date.getMonth() + 1) + '-' +
                    this.date.getDate()
        }";
        break;
    case "week":
        $groepering = "{
            richting: this.Richting,
            bucket: this.date.getFullYear()
        }";
        break;
    case "maand":
        $groepering = "{
            richting: this.Richting,
            bucket: this.date.getFullYear() + '-' +
                    (this.date.getMonth() + 1)
        }";
        break;
    case "jaar":
    default:
        $groepering = "{
            richting: this.Richting,
            bucket: this.date.getFullYear()
        }";
}

$map = new MongoCode("
    function () {
        emit({$groepering}, {
            totaal: this.Totaal,
            cat1: this.Cat1,
            cat2: this.Cat2,
            cat3: this.Cat3,
            cat4: this.Cat4,
            cat5: this.Cat5,
            cat6: this.Cat6
        });
    }
");

$reduce = new MongoCode("
    function (key, values) {
        var r = {
            totaal: 0,
            cat1: 0,
            cat2: 0,
            cat3: 0,
            cat4: 0,
            cat5: 0,
            cat6: 0
        };
        values.forEach(function (value) {
            r.totaal += value.totaal;
            r.cat1 += value.cat1;
            r.cat2 += value.cat2;
            r.cat3 += value.cat3;
            r.cat4 += value.cat4;
            r.cat5 += value.cat5;
            r.cat6 += value.cat6;
        });
        return r;
    }
");

$ops = [
    'mapreduce' => 'tellingen',
    'map' => $map,
    'reduce' => $reduce,
    'query' => ['Telpunt' => $codes[$_GET['telpunt']]],
    'out' => 'counts'
];

$mapReduce = $db->command($ops);

$tellingen = $db->selectCollection($mapReduce['result'])->find();

$results = [
    'tellingen' => []
];

foreach ($tellingen as $telling) {
    $results['tellingen'][] = $telling;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
echo json_encode($results);