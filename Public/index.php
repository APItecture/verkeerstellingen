<!DOCTYPE html>
<html>
    <head>
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
        
        <script type="text/javascript">
            google.setOnLoadCallback(drawChart);
            
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Jaar', 'Ochtend', 'Avond'],
                    ['2004',  1000,      400],
                    ['2005',  1170,      460],
                    ['2006',  660,       1120],
                    ['2007',  1030,      540]
                ]);
                
                var options = {
                    title: 'Spitsdrukte meerjarig',
                    curveType: 'function',
                    legend: { position: 'bottom' }
                };
                
                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
                
                chart.draw(data, options);
            }
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
                    <input type="checkbox">PA<br>
                    <input type="checkbox">LV<br>
                    <input type="checkbox">MV<br>
                    <input type="checkbox">ZV<br>
                    van <input><br>
                    tot <input><br>
                    per
                    <select>
                        <option></option>
                        <option>uur</option>
                        <option>etmaal</option>
                        <option>week</option>
                        <option>maand</option>
                        <option>jaar</option>
                    </select>
                    <input type="submit" value="bereken">
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
            </main>
        </div>
        <script type="text/javascript">
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
                    $('#informatie').append('<div><span>' + key + '</span> : <span>' + this.lus[key] + '</span><br>');
                }
                
                // Piechart voor categorie aandeel.
                $.get('getTellingen.php?telpunt=' + this.lus['Locatie code'] + '&facet=cat', function (data) {
                    var dataArray = [['Categorie', 'Aandeel']];
                    for (var key in data.result[0]) {
                        if (key !== '_id') {
                            dataArray.push([key, data.result[0][key]]);
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
                $.get('getTellingen.php?telpunt=' + this.lus['Locatie code'] + '&facet=aantal', function (data) {
                });
            }
        </script>
    </body>
</html>