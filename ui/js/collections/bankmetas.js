// Filename: collections/bankmetas
define([
    'jquery',
    'underscore',
    'handlebars',
    'backbone',
    'models/bankmeta'
    ], function($,_,Handlebar, Backbone, BankmetaModel){
        var BankmetaCollection = Backbone.Collection.extend({
            model: BankmetaModel,
            url:"../service/api/searchaccountsbytimezone"
        });
        // You don't usually return a collection instantiated
        return new BankmetaCollection;
    });

