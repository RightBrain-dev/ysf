define([
  'jquery',
  'underscore',
  'backbone',
  'validate',
  'inputmask',
  'datepicker',
  'select2',
  '../../state/collections/stateCollection',
  '../../businessSector/collections/businessCollection',
  '../models/iti_registrationSingleModel',
  'text!../templates/iti_registrationSingle_temp.html',
], function($,_, Backbone,validate,inputmask,datepicker,select2,stateCollection,businessCollection,iti_registrationSingleModel,iti_registrationSingle_temp){

var companySingleView = Backbone.View.extend({
    model:iti_registrationSingleModel,
    initialize: function(options){
        var selfobj = this;
        $(".modelbox").hide();
        scanDetails = options.searchITI;
        $('#companyData').remove();
        $(".popupLoader").show();
        
        this.stateList = new stateCollection();
        

        this.model = new iti_registrationSingleModel();
      
          this.stateList.fetch({headers: {
            'contentType':'application/x-www-form-urlencoded','SadminID':$.cookie('authid'),'token':$.cookie('_bb_key'),'Accept':'application/json'
          },error: selfobj.onErrorHandler,data:{getAll:'Y'}}).done(function(res){
            if(res.statusCode == 994){app_router.navigate("logout",{trigger:true});}
            $(".popupLoader").hide();
            selfobj.render();
          });
        if(options.ITIID != ""){
          this.model.set({ITIID:options.ITIID});
          this.model.fetch({headers: {
            'contentType':'application/x-www-form-urlencoded','SadminID':$.cookie('authid'),'token':$.cookie('_bb_key'),'Accept':'application/json'
          },error: selfobj.onErrorHandler}).done(function(res){
            
            if(res.statusCode == 994){app_router.navigate("logout",{trigger:true});}
            $(".popupLoader").hide();
           selfobj.render();
            
          });
        }else
        {
           selfobj.render();

           $(".popupLoader").hide();
        }
    },
    events:
    {
      "click #saveCompanyDetails":"saveCompanyDetails",
      "click .item-container li":"setValues",
      "blur .txtchange":"updateOtherDetails",
      "change .multiSel":"setValues",
      "change .bDate":"updateOtherDetails",
      "change .dropval":"updateOtherDetails",
    },
    onErrorHandler: function(collection, response, options){
        alert("Something was wrong ! Try to refresh the page or contact administer. :(");
        $(".profile-loader").hide();
    },
    updateOtherDetails: function(e){

      var valuetxt = $(e.currentTarget).val();  
      var toID = $(e.currentTarget).attr("id");
      var newdetails=[];
      newdetails[""+toID]= valuetxt;
      this.model.set(newdetails);
    },
    setValues:function(e){
        setvalues = ["status","comTypeNEEM","comTypeNAPS"];
        var selfobj = this;
        $.each(setvalues,function(key,value){
          var modval = selfobj.model.get(value);
          if(modval != null){
            var modeVal = modval.split(",");
          }else{ var modeVal = {};}

          $(".item-container li."+value).each(function(){
            var currentval = $(this).attr("data-value");
            var selecterobj = $(this);
            $.each(modeVal,function(key,dbvalue){
              if(dbvalue.trim().toLowerCase() == currentval.toLowerCase()){
                $(selecterobj).addClass("active");
              }
            });
          });
          
        });
        setTimeout(function(){
        if(e != undefined && e.type == "click")
        {
          var newsetval = [];
          var objectDetails = [];
          var classname = $(e.currentTarget).attr("class").split(" ");
          $(".item-container li."+classname[0]).each(function(){
            var isclass = $(this).hasClass("active");
            if(isclass){
              var vv = $(this).attr("data-value");
              newsetval.push(vv);
            }
         
          });

          if (0 < newsetval.length) {
            var newsetvalue = newsetval.toString();
          }
          else{var newsetvalue = "";}

          objectDetails[""+classname[0]] = newsetvalue;
          $("#valset__"+classname[0]).html(newsetvalue);
          selfobj.model.set(objectDetails);
        }
      }, 500);
    },
    saveCompanyDetails: function(e){
      e.preventDefault();
      var cid = this.model.get("ITIID");
      if(permission.edit != "yes"){
        alert("You dont have permission to edit");
        return false;
      }
      if(cid == "" || cid == null){
        var methodt = "PUT";
      }else{
        var methodt = "POST";
      }
      console.log(methodt)
      if($("#companyDetails").valid()){
        var selfobj = this;
        $(e.currentTarget).html("<span>Saving..</span>");
        $(e.currentTarget).attr("disabled", "disabled");
        this.model.save({},{headers:{
          'Content-Type':'application/x-www-form-urlencoded','SadminID':$.cookie('authid'),'token':$.cookie('_bb_key'),'Accept':'application/json'
        },error: selfobj.onErrorHandler,type:methodt}).done(function(res){
          if(res.statusCode == 994){app_router.navigate("logout",{trigger:true});}
          if(res.flag == "F"){
            alert(res.msg);
            $(e.currentTarget).html("<span>Error</span>");
          }else{
            $(e.currentTarget).html("<span>Saved</span>");
            scanDetails.filterSearch();
          }
          
          setTimeout(function(){
            $(e.currentTarget).html("<span>Save</span>");
            $(e.currentTarget).removeAttr("disabled");
            }, 3000);
          
        });
      }
    },
    initializeValidate:function(){
      var selfobj = this;
      $('#companyGstNo').inputmask('Regex',{regex: "^([A-Z0-9]{1,15})$"});
      $('#companyPanNo').inputmask('Regex',{regex: "^([A-Z0-9]{1,10})$"});
      $('#companyMobile').inputmask('Regex',{regex: "^[0-9](\\d{1,10})?$"});
        $("#companyDetails").validate({
        rules: {
          companyName:{
             required: true,
          },
          companyBillingName:{
             required: true,
          },
          companyCode:{
             required: true,
          },
          companyAddress:{
             required: true,
          },          
          companyGstNo:{
             required: true,
            minlength:15,
            maxlength:15,
          },
          companyPanNo:{
            required:true,
            minlength:10,
            maxlength:10,
          },
          companyEmail:{
             required: false,
             email:true,
          },
           companyBranchid:{
             required: true,
          },
           companyStateid:{
             required: true,
          },
        },
        messages: {
          companyName: "Please enter Name",
          companyBillingName: "Please enter Company Billing Name",
          companyCode: "Please enter Company Code",
          companyAddress: "Please enter Address",
          companyGstNo:{
          required:"Please enter  Gst Number",
          maxlength:"Gst number should be 15 characters",
          minlength:"Gst number should be 15 characters",
          },
          companyPanNo:{
          required:"Please enter Pan Number",
          maxlength:"Pan Number should be 10 characters",
          minlength:"Pan Number should be 10 characters",
          },
          companyBranchid: "Please select Branch Name",
          companyStateid: "Please select State Name",
        }
      });
    },
    render: function(){
      var selfobj = this;
        var source = iti_registrationSingle_temp;
        
        var template = _.template(source);
        this.$el.html(template({"model":this.model.attributes,"stateList":this.stateList.models}));
        $("#modalBody").append(this.$el);
        
        var profile = this.model.get("userName");
        $(".modal-title").html("Company Details");
        $('#companyData').show();
        $('#companyBranchid').select2({width:'100%'}); 
        $('#companyStateid').select2({width:'100%'}); 
        $('#businessSectorid').select2({width:'100%'}); 
        this.initializeValidate();
        selfobj.setValues();
        return this;
    },onDelete: function(){
        this.remove();
      
    }
});

  return companySingleView;
  
});
