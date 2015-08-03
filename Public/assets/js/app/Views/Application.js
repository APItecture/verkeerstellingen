define([
    'settings',
    'Collections/Lussen',
    'Views/InfoPanel',
    'text!templates/application.html'
], function (settings, LussenCollection, InfoPanelView, template) {
    return Backbone.View.extend({
        template: _.template(template),
        
        events: {
        },

        initialize: function () {
            this.lussenCollection = new LussenCollection();
            this.listenToOnce(this.lussenCollection, "sync", this.placeMarkers);
            this.lussenCollection.fetch();
            this.infoPanelView = new InfoPanelView();
            this.render();
        },
        
        placeMarkers: function () {
            var applicationView = this;
            this.lussenCollection.each(function (lus) {
                google.maps.event.addListener(
                    new google.maps.Marker({
                        position: new google.maps.LatLng(lus.get("N.breedte"), lus.get("O.lengte")),
                        map: this.mapCanvas,
                        title: lus.get("Locatie naam"),
                        lus: lus
                    }),
                    "click",
                    function () {
                        applicationView.clickMarker(this);
                    }
                );
            }, this);
        },
        
        clickMarker: function (marker) {
            this.infoPanelView.selectMarker(marker);
        },
        
        render: function () {
            this.$el.html(this.template(this));
            this.$(".wrapper").append(this.infoPanelView.el);
            this.mapCanvas = new google.maps.Map(
                document.getElementById("map-canvas"),
                {
                    center: {
                        lat: 52.3747158,
                        lng: 4.8986142
                    },
                    zoom: 12
                }
            );
            return this;
        }
    });
});
