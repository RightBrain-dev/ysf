define([
  'underscore',
  'backbone',
  '../models/candidateModel'
], function(_, Backbone, candidateModel){

  var companyCollection = Backbone.Collection.extend({
      cID:null,
      model: candidateModel,
      initialize : function(){

      },
      url : function() {
        return APIPATH+'candidateMasterList';
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

  return companyCollection;

});