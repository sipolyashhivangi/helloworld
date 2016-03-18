define([
  'underscore',
  'backbone'
], function(_, Backbone) {

var Credentials = Backbone.Model.extend({

  initialize: function(){
    this.bind("change", this.attributesChanged);
  },

  attributesChanged: function(){
    var valid = false;
    if (this.get('username') && this.get('password'))
      valid = true;
    this.trigger("validated", valid);
  }
});
return Credentials;
});