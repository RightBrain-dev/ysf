<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ITI_registration extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('CommonModel');
		$this->load->model('TraineeModel');
		$this->load->library("pagination");
		$this->load->library("response");
		$this->load->library("ValidateData");
	}


	public function getRegistrationDetails()
	{
		$this->access->checkTokenKey();
		$this->response->decodeRequest();
		$isAll = $this->input->post('getAll');
		$textSearch = trim($this->input->post('textSearch'));
		$curPage = $this->input->post('curpage');
		$ITIID = $this->input->post('ITIID');
		$textval = $this->input->post('textval');
		$orderBy = $this->input->post('orderBy');
		$order = $this->input->post('order');
		$statuscode = $this->input->post('status');
		$filterSName = $this->input->post('filterSName');
		
		$config = array();
		if(!isset($orderBy) || empty($orderBy)){
			$orderBy = "instituteName";
			$order ="ASC";
		}
		$other = array("orderBy"=>$orderBy,"order"=>$order);
		
		$config = $this->config->item('pagination');
		$wherec = $join = array();
		if(isset($textSearch) && !empty($textSearch) && isset($textval) && !empty($textval)){

		$wherec["$textSearch like  "] = "'".$textval."%'";
		}

		if(isset($statuscode) && !empty($statuscode)){
		$statusStr = str_replace(",",'","',$statuscode);
		$wherec["t.status"] = 'IN ("'.$statusStr.'")';
		}

		if(isset($filterSName) && !empty($filterSName)){
			$wherec["t.companyStateid"] = ' = "'.$filterSName.'"';
		}

		// get comapny access list
		$adminID = $this->input->post('SadminID');
		// echo  $adminID;exit();
		// $where = array("adminID ="=>"'".$adminID."'");
		// $iti_registration = $this->CommonModel->GetMasterListDetails('*','iti_registration',$where,'','',array(),array());
		// if(isset($iti_registration) && !empty($iti_registration)){
		// 		//$wherec["cm.ITIID IN "] = "(".$iti_registration[0]->companyList.")";
		// }else{
		// 	$status['msg'] = $this->systemmsg->getErrorCode(263);
		// 	$status['statusCode'] = 263;
		// 	$status['flag'] = 'F';
		// 	$this->response->output($status,200);
		// }

		// Check is data process already
		// $other['whereIn'] = "ITIID";

		// $other["whereData"]=$iti_registration[0]->companyList;

		$config["base_url"] = base_url() . "ITIDetails";
	    $config["total_rows"] = $this->CommonModel->getCountByParameter('ITIID','iti_registration',$wherec,$other);
	    $config["uri_segment"] = 2;
	    $this->pagination->initialize($config);
	    if(isset($curPage) && !empty($curPage)){
		$curPage = $curPage;
		$page = $curPage * $config["per_page"];
		}
		else{
		$curPage = 0;
		$page = 0;
		}
		if($isAll=="Y"){
			$join = array();
			$instituteDetails = $this->CommonModel->GetMasterListDetails($selectC='','iti_registration',$wherec,'','',$join,$other);	
		}else{
			
			// //$join = array();
			// $join[0]['type'] ="LEFT JOIN";
			// $join[0]['table']="stateMaster";
			// $join[0]['alias'] ="s";
			// $join[0]['key1'] ="companyStateid";
			// $join[0]['key2'] ="stateID";
			
			$selectC = "*";
			$instituteDetails = $this->CommonModel->GetMasterListDetails($selectC,'iti_registration',$wherec,$config["per_page"],$page,$join,$other);
		}
//print_r($companyDetails);exit;
		$status['data'] = $instituteDetails;
		$status['paginginfo']["curPage"] = $curPage;
		if($curPage <=1)
		$status['paginginfo']["prevPage"] = 0;
		else
		$status['paginginfo']["prevPage"] = $curPage - 1 ;

		$status['paginginfo']["pageLimit"] = $config["per_page"] ;
		$status['paginginfo']["nextpage"] =  $curPage+1 ;
		$status['paginginfo']["totalRecords"] =  $config["total_rows"];
		$status['paginginfo']["start"] =  $page;
		$status['paginginfo']["end"] =  $page+ $config["per_page"] ;
		$status['loadstate'] = true;
		if($config["total_rows"] <= $status['paginginfo']["end"])
		{
		$status['msg'] = $this->systemmsg->getErrorCode(232);
		$status['statusCode'] = 400;
		$status['flag'] = 'S';
		$status['loadstate'] = false;
		$this->response->output($status,200);
		}
		if($instituteDetails){
		$status['msg'] = "sucess";
		$status['statusCode'] = 400;
		$status['flag'] = 'S';
		$this->response->output($status,200);

		}else{
		$status['msg'] = $this->systemmsg->getErrorCode(227);
		$status['statusCode'] = 227;
		$status['flag'] = 'F';
		$this->response->output($status,200);
		}				
	}
	public function registrationDetails($ITIID="")
	{
		
		$this->access->checkTokenKey();
		$this->response->decodeRequest();
		$method = $this->input->method(TRUE);
		
		switch ($method) {
			case "PUT":
			{
				$companyDetails = array();
				$updateDate = date("Y/m/d H:i:s");
				$companyDetails['ITICode'] = $this->validatedata->validate('ITICode','ITICode',true,'',array());

				$companyDetails['instituteName'] = $this->validatedata->validate('instituteName','instituteName',true,'',array());

				$companyDetails['fullAddress'] = $this->validatedata->validate('fullAddress','fullAddress',true,'',array());

				// $companyDetails['companyAddress'] = $this->validatedata->validate('companyAddress','companyAddress',true,'',array());

				$companyDetails['state'] = $this->validatedata->validate('state','state',true);

				$companyDetails['pinCode'] = $this->validatedata->validate('pinCode','pinCode',true);

				$companyDetails['principalName'] = $this->validatedata->validate('principalName','principalName',false,'',array());

				$companyDetails['mobileNumber'] =$this->validatedata->validate('mobileNumber','mobileNumber',false,'',array());

				$companyDetails['landLineNumber'] =$this->validatedata->validate('landLineNumber','landLineNumber',false,'',array());

				$companyDetails['emailId'] = $this->validatedata->validate('emailId','Email-ID',true,'',array());

				$companyDetails['coordinatorName'] = $this->validatedata->validate('coordinatorName','coordinatorName',true,'',array());

				$companyDetails['co_mobileNumber'] = $this->validatedata->validate('co_mobileNumber','co_mobileNumber',true,'',array());

				$companyDetails['co_emailId'] = $this->validatedata->validate('co_emailId','co_emailId',true,'',array());

				$companyDetails['status'] = $this->validatedata->validate('status','status',true,'',array());
				$companyDetails['createdBy'] = $this->input->post('SadminID');

				$companyDetails['createdDate'] = $updateDate;
				
				//print_r($companyDetails);
				$iscreated = $this->CommonModel->saveMasterDetails('iti_registration',$companyDetails);
				if(!$iscreated){
					$status['msg'] = $this->systemmsg->getErrorCode(998);
					$status['statusCode'] = 998;
					$status['data'] = array();
					$status['flag'] = 'F';
					$this->response->output($status,200);

				}else{
					$status['msg'] = $this->systemmsg->getSucessCode(400);
					$status['statusCode'] = 400;
					$status['data'] =array();
					$status['flag'] = 'S';
					$this->response->output($status,200);
				}
				break;
			}
				
			case "POST":
			{
				$companyDetails = array();
				$updateDate = date("Y/m/d H:i:s");
				$where=array('ITIID'=>$ITIID);
				if(!isset($ITIID) || empty($ITIID)){
					$status['msg'] = $this->systemmsg->getErrorCode(998);
					$status['statusCode'] = 998;
					$status['data'] = array();
					$status['flag'] = 'F';
					$this->response->output($status,200);
		}
				$companyDetails['ITICode'] = $this->validatedata->validate('ITICode','ITICode',true,'',array());

				$companyDetails['instituteName'] = $this->validatedata->validate('instituteName','instituteName',true,'',array());

				$companyDetails['status'] = $this->validatedata->validate('status','status',true,'',array());

				$companyDetails['fullAddress'] = $this->validatedata->validate('fullAddress','fullAddress',true,'',array());

				$companyDetails['pinCode'] = $this->validatedata->validate('pinCode','pinCode',true);

				$companyDetails['principalName'] = $this->validatedata->validate('principalName','principalName',true);

				$companyDetails['mobileNumber'] = $this->validatedata->validate('mobileNumber','mobileNumber',false,'',array());

				$companyDetails['landLineNumber'] =$this->validatedata->validate('landLineNumber','landLineNumber',false,'',array());

				$companyDetails['emailId'] =$this->validatedata->validate('emailId','emailId',false,'',array());

				$companyDetails['coordinatorName'] = $this->validatedata->validate('coordinatorName','coordinatorName',false,'',array());
				
				$companyDetails['co_mobileNumber'] = $this->validatedata->validate('co_mobileNumber','co_mobileNumber',true,'',array());

				$companyDetails['co_emailId'] = $this->validatedata->validate('co_emailId','co_emailId',true,'',array());
				

				$companyDetails['modifiedBy'] = $this->input->post('SadminID');
				$companyDetails['modifiedDate'] = $updateDate;
				
				$iscreated = $this->CommonModel->updateMasterDetails('iti_registration',$companyDetails,$where);
				if(!$iscreated){
					$status['msg'] = $this->systemmsg->getErrorCode(998);
					$status['statusCode'] = 998;
					$status['data'] = array();
					$status['flag'] = 'F';
					$this->response->output($status,200);

				}else{
					$status['msg'] = $this->systemmsg->getSucessCode(400);
					$status['statusCode'] = 400;
					$status['data'] =array();
					$status['flag'] = 'S';
					$this->response->output($status,200);
				}
				break;
			}
			case "DELETE":
			{	
				$companyDetails = array();

				$where=array('ITIID'=>$ITIID);
				if(!isset($ITIID) || empty($ITIID)){
					$status['msg'] = $this->systemmsg->getErrorCode(996);
					$status['statusCode'] = 996;
					$status['data'] = array();
					$status['flag'] = 'F';
					$this->response->output($status,200);
				}

				$iscreated = $this->CommonModel->deleteMasterDetails('iti_registration',$where);
				if(!$iscreated){
					$status['msg'] = $this->systemmsg->getErrorCode(996);
					$status['statusCode'] = 996;
					$status['data'] = array();
					$status['flag'] = 'F';
					$this->response->output($status,200);

				}else{
					$status['msg'] = $this->systemmsg->getSucessCode(400);
					$status['statusCode'] = 400;
					$status['data'] =array();
					$status['flag'] = 'S';
					$this->response->output($status,200);
				}
				break;
			}	
			default:
			{
				$where = array("ITIID"=>$ITIID);
				$companyHistory = $this->CommonModel->getMasterDetails('iti_registration','',$where);
				if(isset($companyHistory) && !empty($companyHistory)){

				$status['data'] = $companyHistory;
				$status['statusCode'] = 200;
				$status['flag'] = 'S';
				$this->response->output($status,200);
				}else{

				$status['msg'] = $this->systemmsg->getErrorCode(227);
				$status['statusCode'] = 227;
				$status['data'] =array();
				$status['flag'] = 'F';
				$this->response->output($status,200);
				}
				break;
			}
		}
		
	}
	public function itiChangeStatus()
	{
		$this->access->checkTokenKey();
		$this->response->decodeRequest(); 
		$action = $this->input->post("action");
			if(trim($action) == "changeStatus"){
				$ids = $this->input->post("list");
				$statusCode = $this->input->post("status");	
				$changestatus = $this->CommonModel->changeMasterStatus('iti_registration',$statusCode,$ids,'ITIID');
				
			if($changestatus){
				$status['data'] = array();
				$status['statusCode'] = 200;
				$status['flag'] = 'S';
				$this->response->output($status,200);
			}else{
				$status['data'] = array();
				$status['msg'] = $this->systemmsg->getErrorCode(996);
				$status['statusCode'] = 996;
				$status['flag'] = 'F';
				$this->response->output($status,200);
			}
		}
	}	
	
}