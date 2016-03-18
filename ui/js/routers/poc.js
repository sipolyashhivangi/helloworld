define([
    'jquery',
    'underscore',
    'backbone'
], function($, _, Backbone) {
    var Router = Backbone.Router.extend({
        initialize: function() {

            //go to financial snapshot
            require(
                    ['views/user/overview'],
                    function(overV) {
                        userData = '{status":"OK","uid":"62","email":"subramanyahs@gmail.com","firstname":"Subramanya ","lastname":"HS","urole":"888","sess":"itl9rvjnes6acigj45l6ita146"}';
                        overV.render();
                    }
            );
        }

    });
    return Router;
});