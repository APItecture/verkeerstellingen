define(function () {
    return Backbone.Collection.extend({
        url: "/lussen.json",
        parse: function (data) {
            return data.lussen;
        }
    });
});
