<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Candidate extends CI_Controller {

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


	public function getCandidateDetails()
	{
		$this->access->checkTokenKey();
		$this->response->decodeRequest();
		$isAll = $this->input->post('getAll');
		$textSearch = trim($this->input->post('textSearch'));
		$curPage = $this->input->post('curpage');
		$cID = $this->input->post('cID');
		$textval = $this->input->post('textval');
		$orderBy = $this->input->post('orderBy');
		$order = $this->input->post('order');
		$statuscode = $this->input->post('status');
		$filterSName = $this->input->post('filterSName');
		
		$config = array();
		if(!isset($orderBy) || empty($orderBy)){
			$orderBy = "candidateName";
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
		// 		//$wherec["cm.cID IN "] = "(".$iti_registration[0]->companyList.")";
		// }else{
		// 	$status['msg'] = $this->systemmsg->getErrorCode(263);
		// 	$status['statusCode'] = 263;
		// 	$status['flag'] = 'F';
		// 	$this->response->output($status,200);
		// }

		// Check is data process already
		// $other['whereIn'] = "cID";

		// $other["whereData"]=$iti_registration[0]->companyList;

		$config["base_url"] = base_url() . "candidate";
	    $config["total_rows"] = $this->CommonModel->getCountByParameter('cID','candidate',$wherec,$other);
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
			$instituteDetails = $this->CommonModel->GetMasterListDetails($selectC='','candidate',$wherec,'','',$join,$other);	
		}else{
			
			// //$join = array();
			// $join[0]['type'] ="LEFT JOIN";
			// $join[0]['table']="stateMaster";
			// $join[0]['alias'] ="s";
			// $join[0]['key1'] ="companyStateid";
			// $join[0]['key2'] ="stateID";
			
			$selectC = "*";
			$instituteDetails = $this->CommonModel->GetMasterListDetails($selectC,'candidate',$wherec,$config["per_page"],$page,$join,$other);
		}
//print_r($candidateDetails);exit;
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
	public function candidateDetails($cID="")
	{
		
		$this->access->checkTokenKey();
		$this->response->decodeRequest();
		$method = $this->input->method(TRUE);
			// echo $method;
		if($method=="POST"||$method=="PUT")
		{
				$candidateDetails = array();
				$updateDate = date("Y/m/d H:i:s");
				// $candidateDetails['regiNoYSF'] = $this->validatedata->validate('regiNoYSF','regiNoYSF',true,'',array());

				$candidateDetails['regNoPortal'] = $this->validatedata->validate('regNoPortal','regNoPortal',true,'',array());

				$candidateDetails['ITICode'] = $this->validatedata->validate('ITICode','ITICode',true,'',array());

				// $candidateDetails['companyAddress'] = $this->validatedata->validate('companyAddress','companyAddress',true,'',array());

				$candidateDetails['instituteName'] = $this->validatedata->validate('instituteName','instituteName',true);

				$candidateDetails['candidateName'] = $this->validatedata->validate('candidateName','candidateName',true);

				$candidateDetails['middleName'] = $this->validatedata->validate('middleName','middleName',false,'',array());

				$candidateDetails['relationship'] =$this->validatedata->validate('relationship','relationship',false,'',array());

				$candidateDetails['dateOfBirth'] =$this->validatedata->validate('dateOfBirth','dateOfBirth',false,'',array());

				$candidateDetails['gender'] = $this->validatedata->validate('gender','gender',true,'',array());

				$candidateDetails['disability'] = $this->validatedata->validate('disability','disability',true,'',array());

				$candidateDetails['aadharNo'] = $this->validatedata->validate('aadharNo','aadharNo',true,'',array());

				$candidateDetails['panNo'] = $this->validatedata->validate('panNo','panNo',true,'',array());
				$candidateDetails['category'] = $this->validatedata->validate('category','category',true,'',array());
				$candidateDetails['secondaryIDName'] = $this->validatedata->validate('secondaryIDName','secondaryIDName',true,'',array());
				$candidateDetails['secondryID'] = $this->validatedata->validate('secondryID','secondryID',true,'',array());
				$candidateDetails['aboutMe'] = $this->validatedata->validate('aboutMe','aboutMe',true,'',array());
				$candidateDetails['fullAddress'] = $this->validatedata->validate('fullAddress','fullAddress',true,'',array());
				$candidateDetails['state'] = $this->validatedata->validate('state','state',true,'',array());
				$candidateDetails['pinCode'] = $this->validatedata->validate('pinCode','pinCode',true,'',array());
				$candidateDetails['mobile1'] = $this->validatedata->validate('mobile1','mobile1',true,'',array());
				$candidateDetails['mobile2'] = $this->validatedata->validate('mobile2','mobile2',true,'',array());
				$candidateDetails['pMobile1'] = $this->validatedata->validate('pMobile1','pMobile1',true,'',array());
				$candidateDetails['pMobile2'] = $this->validatedata->validate('pMobile2','pMobile2',true,'',array());
				$candidateDetails['email'] = $this->validatedata->validate('email','email',true,'',array());
				$candidateDetails['eudQualification'] = $this->validatedata->validate('eudQualification','eudQualification',true,'',array());
				$candidateDetails['trade'] = $this->validatedata->validate('trade','trade',true,'',array());
				$candidateDetails['aggregateMarks'] = $this->validatedata->validate('aggregateMarks','aggregateMarks',true,'',array());
				$candidateDetails['percentage'] = $this->validatedata->validate('percentage','percentage',true,'',array());
				$candidateDetails['PassingYear'] = $this->validatedata->validate('PassingYear','PassingYear',true,'',array());
					if($method=="POST")
					{
						$candidateDetails['status'] = "active";
						// $candidateDetails['regiNoYSF']="ITI".uniqid();
						$candidateDetails['createdBy'] = $this->input->post('SadminID');
						$candidateDetails['createdDate'] = $updateDate;
						$iscreated = $this->CommonModel->saveMasterDetails('candidate',$candidateDetails);
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

					}elseif($method="PUT")
					{
						$where=array('cID'=>$cID);
						if(!isset($cID) || empty($cID)){
						$status['msg'] = $this->systemmsg->getErrorCode(998);
						$status['statusCode'] = 998;
						$status['data'] = array();
						$status['flag'] = 'F';
						$this->response->output($status,200);
					}
					$candidateDetails['modifiedBy'] = $this->input->post('SadminID');
					$candidateDetails['modifiedDate'] = $updateDate;
					$iscreated = $this->CommonModel->updateMasterDetails('candidate',$candidateDetails,$where);
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
			
	
		}elseif($method=="dele")
		{
			$candidateDetails = array();
			$where=array('cID'=>$cID);
				if(!isset($cID) || empty($cID)){
					$status['msg'] = $this->systemmsg->getErrorCode(996);
					$status['statusCode'] = 996;
					$status['data'] = array();
					$status['flag'] = 'F';
					$this->response->output($status,200);
				}

				$iscreated = $this->CommonModel->deleteMasterDetails('candidate',$where);
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
			}
		

		}else
		{
				
				$where = array("cID"=>$cID);
				$companyHistory = $this->CommonModel->getMasterDetails('candidate','',$where);
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
				$changestatus = $this->CommonModel->changeMasterStatus('iti_registration',$statusCode,$ids,'cID');
				
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