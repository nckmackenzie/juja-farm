<?php
class Elders extends Controller
{
    private $eldermodel;
    private $authmodel;
    private $usermodel;
    private $reusemodel;
    private $transfermodel;
    public function __construct()
    {
        if(!isset($_SESSION['userId'])){
            redirect('users');
            exit;
        }
        $this->authmodel = $this->model('Auth');
        $this->eldermodel = $this->model('Elder');
        $this->reusemodel = $this->model('Reusables');
        $this->usermodel = $this->model('User');
        $this->transfermodel = $this->model('Transfer');
        checkrights($this->authmodel,'group fund requisition');
    }

    public function index()
    {
        $data = ['elders' => $this->eldermodel->GetElders()];
        $this->view('elders/index',$data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Elder',
            'congregations' => $this->reusemodel->GetCongregations(),
            'roles' => $this->reusemodel->GetRoles(),
            'id' => '',
            'isedit' => false,
            'name' => '',
            'contact' => '',
            'congregation' => '',
            'district' => '',
            'role' => '',
            'date' => date('Y-m-d'),
            'errmsg' => [],
        ];
        $this->view('elders/add',$data);
    }

    function createuserid($name)
    {
        $arrayed = explode(" ",strtolower($name));
        $concat = substr($arrayed[0], 0, 1) . $arrayed[1];
        return $concat;
    }

    public function createupdate()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $data = [
                'title' => converttobool($_POST['isedit']) ? 'Edit Elder' : 'Add Elder',
                'congregations' => $this->reusemodel->GetCongregations(),
                'roles' => $this->reusemodel->GetRoles(),
                'id' => $_POST['id'],
                'isedit' => converttobool($_POST['isedit']),
                'name' => isset($_POST['name']) && !empty(trim($_POST['name'])) ? trim($_POST['name']) : null,
                'contact' => isset($_POST['contact']) && !empty(trim($_POST['contact'])) ? trim($_POST['contact']) : null,
                'congregation' => isset($_POST['congregation']) && !empty(trim($_POST['congregation'])) ? (int)trim($_POST['congregation']) : null,
                'district' => isset($_POST['district']) && !empty(trim($_POST['district'])) ? (int)trim($_POST['district']) : null,
                'role' => isset($_POST['role']) && !empty(trim($_POST['role'])) ? (int)trim($_POST['role']) : null,
                'date' => isset($_POST['date']) && !empty(trim($_POST['date'])) ? date('Y-m-d',strtotime($_POST['date'])) : null,
                'memberid' => converttobool($_POST['isedit']) ? trim($_POST['memberid']) : null,
                'useridprimary' => converttobool($_POST['isedit']) ? trim($_POST['userid']) : null,
                'errmsg' => [],
            ];

            if(is_null($data['name'])){
                array_push($data['errmsg'],'Enter elder name');
            }
            if(is_null($data['contact'])){
                array_push($data['errmsg'],'Enter elder contact');
            }
            if(is_null($data['congregation'])){
                array_push($data['errmsg'],'Select congregation');
            }
            if(is_null($data['district'])){
                array_push($data['errmsg'],'Select district');
            }
            if(is_null($data['role'])){
                array_push($data['errmsg'],'Select role');
            }
            if(is_null($data['date'])){
                array_push($data['errmsg'],'Select date');
            }
            if(!is_null($data['date']) && $data['date'] > date('Y-m-d')){
                array_push($data['errmsg'],'Invalid date selected');
            }

            if(count($data['errmsg']) > 0){
                $this->view('elders/add',$data);
                exit;
            }

            $data['userid'] = substr($data['contact'],1);

            // $data['userid'] = $this->createuserid($data['name']);

            if(!$this->eldermodel->CreateUpdate($data))
            {
                array_push($data['errmsg'],'Unable to save. Retry or contact admin');
                $this->view('elders/add',$data);
                exit;
            }

            flash('elder_msg', !$data['isedit'] ? "Elder Added Successfully!" : 'Elder Edited Successfully!');
            redirect('elders');

        }
        else
        {
            redirect('users/deniedaccess');
            exit;
        }
    }

    public function edit($id)
    {
        $details = $this->eldermodel->GetUserDetails($id);
        $date = $this->eldermodel->GetSetDate($id);
        $data = [
            'title' => 'Edit Elder',
            'congregations' => $this->reusemodel->GetCongregations(),
            'districts' => $this->transfermodel->GetDistricts($details->congregationId),
            'roles' => $this->reusemodel->GetRoles(),
            'id' => $id,
            'isedit' => true,
            'name' => strtoupper($details->ElderName),
            'contact' => $details->Contact,
            'congregation' => $details->congregationId,
            'district' => $details->districtId,
            'memberid' => $details->MemberId,
            'userid' => $this->eldermodel->GetUserId($id),
            'role' => 1,
            'date' => date('Y-m-d',strtotime($date)),
            'errmsg' => [],
        ];
        $this->view('elders/add',$data);
    }

    public function transfer($id)
    {
        $elderdetails = $this->eldermodel->GetElderDetails($id);
        $data = [
            'title' => 'Transfer Elder',
            'elderid' => $id,
            'oldcongregation' => strtoupper($elderdetails[0]),
            'olddistrict' => strtoupper($elderdetails[1]),
            'oldcongregationname' => strtoupper($elderdetails[2]),
            'olddistrictname' => strtoupper($elderdetails[3]),
            'congregations' => $this->reusemodel->GetCongregations(),
            'name' => strtoupper($elderdetails[4]),
            'congregation' => '',
            'reason' => '',
            'district' => '',
            'date' => date('Y-m-d'),
            'errmsg' => [],
        ];
        $this->view('elders/transfer',$data);
    }

    public function createtransfer()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $elderdetails = $this->eldermodel->GetElderDetails($_POST['elderid']);
            $data = [
                'title' => 'Transfer Elder',
                'elderid' => $_POST['elderid'],
                'oldcongregation' => strtoupper($elderdetails[0]),
                'olddistrict' => strtoupper($elderdetails[1]),
                'oldcongregationname' => strtoupper($elderdetails[2]),
                'olddistrictname' => strtoupper($elderdetails[3]),
                'congregations' => $this->reusemodel->GetCongregations(),
                'name' => strtoupper($elderdetails[4]),
                'congregation' => isset($_POST['congregation']) && !empty(trim($_POST['congregation'])) ? (int)trim($_POST['congregation']) : null,
                'reason' => isset($_POST['reason']) && !empty(trim($_POST['reason'])) ? trim($_POST['reason']) : null,
                'district' => isset($_POST['district']) && !empty(trim($_POST['district'])) ? (int)trim($_POST['district']) : null,
                'date' => isset($_POST['date']) && !empty(trim($_POST['date'])) ? date('Y-m-d',strtotime($_POST['date'])) : null,
                'errmsg' => [],
            ];

            if(is_null($data['congregation'])){
                array_push($data['errmsg'],'Select congregation');
            }
            if(is_null($data['district'])){
                array_push($data['errmsg'],'Select district');
            }
            if(is_null($data['date'])){
                array_push($data['errmsg'],'Select date');
            }
            if(!is_null($data['date']) && $data['date'] > date('Y-m-d')){
                array_push($data['errmsg'],'Invalid date selected');
            }

            if(count($data['errmsg']) > 0){
                $this->view('elders/transfer',$data);
                exit;
            }

            if(!$this->eldermodel->Transfer($data))
            {
                array_push($data['errmsg'],'Unable to transfer. Retry or contact admin');
                $this->view('elders/transfer',$data);
                exit;
            }

            flash('elder_msg',"Elder Transfered Successfully!");
            redirect('elders');
        }
    }

    public function reset()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id = (int)trim($_POST['id']);
            $contact = $this->eldermodel->GetElderContactAndId($id)[0];

            $random = substr(md5(mt_rand()),0,7);
            $hashed =  password_hash($random,PASSWORD_DEFAULT);
            $userid = $this->eldermodel->GetElderContactAndId($id)[1];
            $loginid = $this->eldermodel->GetElderContactAndId($id)[2];

            $data = [
                'id' => $userid,
                'password' => $hashed
            ];

            if($this->usermodel->resetCredentials($data))
            {
                $message = 'Password Reset Successful! Your New Password Is '.$random .' and your User ID is '.$loginid.' Click on the provided link to log in. ' . URLROOT . '/users';
                $full = '+254' . $contact;
                sendgeneral($full,$message);
                flash('elder_msg',"Password reset Successfully!");
                redirect('elders');
            }

            flash('elder_msg',"Elder Transfered Successfully!");
            redirect('elders');
        }
    }

    public function delete()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id = (int)trim($_POST['id']);
            if(!$this->eldermodel->Delete($id))
            {
                flash('elder_msg',"Elder Transfered Successfully!",'alert custom-danger alert-dismissible fade show');
                redirect('elders');
                exit;
            }

            flash('elder_msg',"Elder Transfered Successfully!");
            redirect('elders');
        }
    }
}