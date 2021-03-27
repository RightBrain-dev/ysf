define([
  'underscore',
  'backbone',
], function(_, Backbone) {

  var companyModel = Backbone.Model.extend({
    idAttribute: "ITIID",
     defaults: {
        ITIID:null,
        ITICode:null,
        instituteName:null,
        fullAddress:null,
        state:null,
        pinCode:null,
        principalName:null,
        mobileNumber:null,
        landLineNumber:null,
        emailId:null,
        coordinatorName:null,
        co_mobileNumber:null,
        co_emailId:null,
        createdBy:null,
        modifiedBy:null,
        createdDate:null,
        modifiedDate:null,
        status:'active',
    },
  	urlRoot:function(){
      return APIPATH+'reigtrationMaster/'
    },
    parse : function(response) {
        return response.data[0];
      }
  });
  return companyModel;
});
