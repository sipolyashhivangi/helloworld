// Filename: models/bankmeta
define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    ], function($,_, Handlebar,Backbone){
        var BankmetaModel = Backbone.Model.extend({
            defaults: {
                serviceId:"0",
                displayName: "None", 
                loginURL: "",
                type:"bank",
                mfa:"0"
            }
        });
        // Return the model for the module
        return new BankmetaModel;
    });

