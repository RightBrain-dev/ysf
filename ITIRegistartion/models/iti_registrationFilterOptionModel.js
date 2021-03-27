define([
  'underscore',
  'backbone',
], function(_, Backbone) {

  var iti_registrationFilterOptionModel = Backbone.Model.extend({
  	idAttribute: "ITIID",
  	 defaults:{
        textSearch:'instituteName',
        textval: null,
        status:'active',
		    orderBy:'instituteName',
        order:'ASC',  
    }
  });
  return iti_registrationFilterOptionModel;
});

