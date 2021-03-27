define([
  'underscore',
  'backbone',
  '../models/iti_registrationModel'
], function(_, Backbone, iti_registrationModel){

  var iti_registrationCollection = Backbone.Collection.extend({
      companyID:null,
      model: iti_registrationModel,
      initialize : function(){

      },
      url : function() {
        return APIPATH+'iti_registrationMasterList';
      },
      parse : function(response){
        this.pageinfo = response.paginginfo;
        this.totalRecords = response.totalRecords;
        this.endRecords = response.end;
        this.flag = response.flag;
        this.msg = response.msg;
        this.loadstate = response.loadstate;
        return response.data;
      }
  });

  return iti_registrationCollection;

});