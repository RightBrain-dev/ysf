define([
  'underscore',
  'backbone',
], function(_, Backbone) {

  var companyModel = Backbone.Model.extend({
    idAttribute: "cID"
  });
  return companyModel;
});

