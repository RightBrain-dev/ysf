define([
  'underscore',
  'backbone',
], function(_, Backbone) {

  var companyFilterOptionModel = Backbone.Model.extend({
  	idAttribute: "cID",
  	 defaults:{
        textSearch:'candidateName',
        textval: null,
        status:'active',
		    orderBy:'candidateName',
        order:'ASC',
    }
  });
  return companyFilterOptionModel;
});

