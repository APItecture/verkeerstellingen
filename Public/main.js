var marker;
var google = google;

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
    marker = this;
    $('#item_info').fadeOut("fast", function () {
        $('#informatie').html(
            '<h1>' + marker.lus['Locatie naam'] + '</h1>' +
            '<img class="mapdetail" src="https://maps.googleapis.com/maps/api/staticmap?size=300x300&markers=color:red%7C%7C' + marker.lus['N.breedte'] + ',' + marker.lus['O.lengte'] + '">'
        );
        
        for (var key in marker.lus) {
            $('#informatie').append('<div class="field"><span class="key">' + key + ':</span><br><span class="value">' + marker.lus[key] + '</span><br>');
        }
        $('#item_info').fadeIn("fast");
    });
}

$("form").submit(function (e) {
    e.preventDefault();
    bereken($(this).serialize(), $(this).serializeArray());
});

function bereken(query, values) {
    
    $.get('getTellingen.php?telpunt=' + marker.lus['Locatie code'] + '&' + query, function (data) {
        var buckets = {};
        data.tellingen.forEach(function (telling) {
            buckets[telling._id.datum] = buckets[telling._id.datum] || {};
            buckets[telling._id.datum][telling._id.richting] = 0;
            var typeFilter = false;
            values.forEach(function (field) {
                if (field.name === 'type[]') {
                    buckets[telling._id.datum][telling._id.richting] += telling[field.value]; // FIXME No request needed when changing types!
                    typeFilter = true;
                }
            });
            if (!typeFilter) {
                buckets[telling._id.datum][telling._id.richting] += telling['totaal'];
            }
        });
        var dataArray = [['Bucket', 'Richting 1', 'Richting 2', 'Totaal']];
        for (var datum in buckets) {
            dataArray.push([new Date(datum), buckets[datum]['1'], buckets[datum]['2'], buckets[datum]['1'] + buckets[datum]['2']]);
        }
        
        if (dataArray.length <= 1) {
            alert("geen resultaten");
            return;
        }
        
        var data = google.visualization.arrayToDataTable(dataArray);
        data.sort([{column: 0}]);
        
        var options = {
            title: 'Telling',
            legend: { position: 'bottom' }
        };
        
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
                
        chart.draw(data, options);
        
        // // Categorie verhoudingen
        
        // var dataArray = [['Categorie', 'Aandeel']];
        // for (var key in data.tellingen[0]) {
        //     if (key !== '_id') {
        //         dataArray.push([key, data.tellingen[0][key]]);
        //     }
        // }
        // var dataTable = google.visualization.arrayToDataTable(dataArray);
    
        // var options = {
        //     title: 'Aandeel categorien'
        // };
        
        // var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        
        // chart.draw(dataTable, options);
    });
    
}