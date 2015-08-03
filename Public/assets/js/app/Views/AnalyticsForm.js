define([
    'settings',
    'text!templates/analyticsForm.html'
], function (settings, template) {
    return Backbone.View.extend({
        template: _.template(template),
        
        tagName: "form",

        initialize: function () {
            this.render();
        },
        
        render: function () {
            this.$el.html(this.template(this)); //FIXME Tight coupling
            return this;
        }
    });
});
