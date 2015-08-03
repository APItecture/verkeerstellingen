define([
    'settings',
    'Views/AnalyticsForm',
    'text!templates/infoPanel.html'
], function (settings, analyticsFormView, template) {
    return Backbone.View.extend({
        template: _.template(template),
        
        id: "side-panel",
        
        events: {
            "submit form": "formSubmit",
            "click nav > ul > li": "selectPanel"
        },

        initialize: function () {
            this.selectedMarker = {
                lus: new Backbone.Model // Empty model
            };
            this.analyticsFormView = new analyticsFormView();
            this.render();
        },
        
        selectMarker: function (marker) {
            this.selectedMarker = marker;
            this.render();
        },
        
        formSubmit: function (e) {
            e.preventDefault();
            this.getAnalytics($(e.target).serialize(), $(e.target).serializeArray());
        },
        
        /**
         * Load analytics from data service and plot. 
         */
        getAnalytics: function (query, values) {
            var infoPanelView = this;
            $.get('getTellingen.php?telpunt=' + this.selectedMarker.lus.get('Locatie code') + '&' + query, function (data) {
                infoPanelView.showChart(data, values);
                //infoPanelView.showCircle(data);
                infoPanelView.showTable(data, values);
            });
        },
      
        /**
         * Plot the chart (data from data service)
         */
        showChart: function (data, values) { //FIXME Tight coupling
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
        },
        
        /**
         * Broken function, fix later.
         */
        showCircle: function (data, values) { //FIXME Tight coupling
            return; //FIXME Broken
            
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
        },
        
        showTable: function (data, values) {
            console.log(data);
        },
        
        selectPanel: function (e) {
            this.$("nav > ul > li").removeClass("active");
            this.$("main > section").removeClass("active");
            $(e.target).addClass("active");
            this.$("#" + $(e.target).html()).addClass("active");
        },
        
        render: function () {
            this.$el.html(this.template(this));
            this.$("#facetten").append(this.analyticsFormView.$el);
            return this;
        }
    });
});
