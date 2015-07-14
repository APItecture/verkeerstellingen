<!DOCTYPE html>
<html>
    <head>
        
        <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.0/material.indigo-pink.min.css">
        <script src="https://storage.googleapis.com/code.getmdl.io/1.0.0/material.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        
        <style type="text/css">
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
                font-family: Helvetica, Tahoma, Arial, Sans-serif;
                line-height: 1.4;
            }
            h1 {
                line-height: 1.5;
                margin: 0;
            }
            .wrapper {
                width: 100%;
                height: 100%;
            }
            #map-canvas {
                height: 100%;
                width: 50%;
                float: left;
            }
            main {
                height: 100%;
                width: 50%;
                float: right;
                overflow: auto;
            }
            section {
                margin: 40px;
            }
            label {
                display: block;
                margin-bottom: 10px;
            }
            label.checkbox {
                display: inline-block;
                margin-right: 20px;
            }
            label span {
                display: block;
                margin-bottom: 10px;
            }
            .key {
                display: inline-block;
                width: 140px;
            }
            .value {
                font-weight: 700;
            }
        </style>
        <script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDH3cHtFfjEEIraQ8dSaloUm5dybciHZ3A"></script>
        
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
        </script>
        
        <script type="text/javascript"
            src="https://www.google.com/jsapi?autoload={
                'modules':[{
                    'name':'visualization',
                    'version':'1',
                    'packages':['corechart']
                }]
            }">
        </script>
    </head>
    <body>
        <div class="wrapper">
            <div id="map-canvas"></div>
            <main>
                <section id="informatie">
                    <h3>Informatie</h3>
                    <h1></h1>
                </section>
                <section id="facetten">
                    <form>
                        <label class="checkbox">
                            <input name="type[]" type="checkbox" value="PA"> PA
                        </label>
                        <label class="checkbox">
                            <input name="type[]" type="checkbox" value="LV"> LV
                        </label>
                        <label class="checkbox">
                            <input name="type[]" type="checkbox" value="MV"> MV
                        </label>
                        <label class="checkbox">
                            <input name="type[]" type="checkbox" value="ZV"> ZV
                        </label>
                        <label>
                            <span>datum vanaf</span>
                            <input name="datum_vanaf">
                        </label>
                        <label>
                            <span>datum tot</span>
                            <input name="datum_tot">
                        </label>
                        <label>
                            <span>groeperen per</span>
                            <select name="groeperen_per">
                                <option></option>
                                <option>uur</option>
                                <option>etmaal</option>
                                <!--<option>week</option>-->
                                <option>maand</option>
                                <option>jaar</option>
                            </select>
                        </label>
                        <input type="submit" value="bereken">
                    </form>
                </section>
                <section id="grafieken">
                    <div id="curve_chart" style="width: 100%;"></div>
                    <div id="piechart" style="width: 100%;"></div>
                </section>
                <section id="specificatie">
                    <h1>Typische vragen</h1>
                    <ul>
                        <li>Hoeveel motovoertuigen rijden er gemiddeld per jaar over weg x per richting? (dergelijke vragen worden gesteld om een schatting te maken van wanneer het wegdek vervangen moet worden)</li>
                        <li>Hoeveel zwaar verkeer rijdt er gemiddeld over weg x per etmaal? (dergelijke vragen worden gesteld t.b.v. roetuitstoot)</li>
                        <li>Hoeveel verkeer rijdt er gemiddeld tussen 0:00 en 6:00 over weg x? (geluidshinder gerelateerde vraagstuk. Burgers klagen over het vele verkeer ’s nachts maar klopt dat wel?)</li>
                        <li>Wat is het drukste moment van de dag op weg x? (uur verloop) Of: Hoeveel verkeerd rijdt er tijdens de spitsen over weg x? (vraag die we om verschillende redenen krijgen maar meestal gerelateerd aan een (tijdelijke)ingreep om in te schatten wat de inpakt is)</li>
                    </ul>
                </section>
                <section id="workinprogress">
                    <p>This site is work in progress</p>
                    <p><a href="http://www.getmdl.io/">Get MDL</a></p>
                </section>
            </main>
        </div>
        <script type="text/javascript">
            var marker;
        
            var map = new google.maps.Map(
                document.getElementById('map-canvas'),
                {
                    center: {
                        lat: 52.3747158,
                        lng: 4.8986142
                    },
                    zoom: 12
                }
            );
            
            $.get("lussen.json", function (data) {
                data.lussen.forEach(function (lus) {
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(lus['N.breedte'], lus['O.lengte']),
                        map: map,
                        title: lus['Locatie naam'],
                        lus: lus
                    });
                    google.maps.event.addListener(marker, 'click', clickMarker);
                });
            });
            
            function clickMarker() {
                $('#informatie').html('<h1>' + this.lus['Locatie naam'] + '</h1>');
                for (var key in this.lus) {
                    $('#informatie').append('<div><span class="key">' + key + ':</span><span class="value">' + this.lus[key] + '</span><br>');
                }
                marker = this;
            }
            
            $("form").submit(function (e) {
                e.preventDefault();
                bereken($(this).serialize());
            });
            
            function bereken(query) {
                // Piechart voor categorie aandeel.
                $.get('getTellingenPerCategorie.php?telpunt=' + marker.lus['Locatie code'] + '&' + query, function (data) {
                    var dataArray = [['Categorie', 'Aandeel']];
                    for (var key in data.tellingen[0]) {
                        if (key !== '_id') {
                            dataArray.push([key, data.tellingen[0][key]]);
                        }
                    }
                    var dataTable = google.visualization.arrayToDataTable(dataArray);
                
                    var options = {
                        title: 'Aandeel categorien'
                    };
                    
                    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                    
                    chart.draw(dataTable, options);
                });
                
                // Piechart voor aantal per jaar per richting.
                $.get('getTellingen.php?telpunt=' + marker.lus['Locatie code'] + '&' + query, function (data) {
                    var buckets = {};
                    data.tellingen.forEach(function (telling) {
                        if (!buckets[telling['_id'].bucket]) {
                            buckets[telling['_id'].bucket] = {};
                        }
                        buckets[telling['_id'].bucket][telling['_id'].richting] = telling.value.totaal;
                    });
                    var dataArray = [['Bucket', 'Richting 1', 'Richting 2', 'Totaal']];
                    for (var key in buckets) {
                        dataArray.push([key, buckets[key]['1'], buckets[key]['2'], buckets[key]['1'] + buckets[key]['2']]);
                    }
                    var data = google.visualization.arrayToDataTable(dataArray);
                    
                    var options = {
                        title: 'Telling',
                        curveType: 'function',
                        legend: { position: 'bottom' }
                    };
                            
                    var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
                            
                    chart.draw(data, options);
                });
            }
        </script>
    </body>
</html>