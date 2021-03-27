define([
  'underscore',
  'backbone',
], function(_, Backbone) {

  var companyModel = Backbone.Model.extend({
    idAttribute: "cID",
     defaults: {
        cID :null,
        regiNoYSF:null,
        regNoPortal:null,
        ITICode:null,
        instituteName:null,
        candidateName:null,
        middleName:null,
        relationship:null,
        dateOfBirth:null,
        gender:null,
        disability:null,
        aadharNo:null,
        panNo:null,
        category:null,
        secondaryIDName:null,
        secondryID:null,
        aboutMe:null,
        fullAddress :null,
        state:null,
        pinCode:null,
        mobile1:null,
        mobile2:null,
        pMobile1:null,
        pMobile2:null,
        email:null,
        eudQualification:null,
        trade:null,
        aggregateMarks:null,
        percentage:null,
        PassingYear:null,
        createdBy:null,
        modifiedBy:null,
        createdDate:null,
        modifiedDate:null,
        status:'active',
    },
  	urlRoot:function(){
      return APIPATH+'candidateMaster/'
    },
    parse : function(response) {
        return response.data[0];
      }
  });
  return companyModel;
});
