<?php
class Deacons extends Controller
{
    private $authmodel;
    private $deaconmodel;
    private $membermodel;
    private $parishofficialsmodel;
    public function __construct()
    {
        if (!isset($_SESSION['userId'])) {
            redirect('users');
            exit;
        }
        $this->authmodel = $this->model('Auth');
        checkrights($this->authmodel,'deacons');
        $this->deaconmodel = $this->model('Deacon');
        $this->membermodel = $this->model('Member');
        $this->parishofficialsmodel = $this->model('Parishofficial');
    }

    public function index()
    {
        $data = ['deacons' => $this->deaconmodel->GetDeacons()];
        $this->view('deacons/index',$data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Deacon',
            'members' => [],
            'districts' => $this->membermodel->getDistricts(),
            'years' => $this->parishofficialsmodel->getYears(),
            'id' => '',
            'isedit' => false,
            'deacon' => '',
            'year' => '',
            'district' => '',
            'zone' => '',
            'role' => '',
            'errors' => []
        ];
        $this->view('deacons/add',$data);
    }

    public function getmembers()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $district = isset($_GET['district']) && !empty(trim($_GET['district'])) ? (int)trim($_GET['district']) : null;
            if(is_null($district))
            {
                echo json_encode(['success' => false]);
            }
            $members = $this->deaconmodel->GetMembersByDistrict($district);
            echo json_encode(['success' => true, 'members' => $members]);
        }
    }

    public function createupdate()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $data = [
                'title' => converttobool($_POST['isedit']) ? 'Edit deacon' : 'Add Deacon',
                'members' => [],
                'districts' => $this->membermodel->getDistricts(),
                'years' => $this->parishofficialsmodel->getYears(),
                'id' => isset($_POST['id']) && !empty(trim($_POST['id'])) ? trim($_POST['id']) : null,
                'isedit' => converttobool($_POST['isedit']),
                'deacon' => isset($_POST['deacon']) && !empty(trim($_POST['deacon'])) ? (int)trim($_POST['deacon']) : null,
                'year' => isset($_POST['year']) && !empty(trim($_POST['year'])) ? (int)trim($_POST['year']) : null,
                'district' => isset($_POST['district']) && !empty(trim($_POST['district'])) ? (int)trim($_POST['district']) : null,
                'zone' => isset($_POST['zone']) && !empty(trim($_POST['zone'])) ? trim(strtolower($_POST['zone'])) : null,
                'role' => '',
                'errors' => []
            ];

            if(is_null($data['deacon'])){
                array_push($data['errors'],'Deacon not selected.');
            }
            if(is_null($data['year'])){
                array_push($data['errors'],'Year not selected.');
            }
            if(is_null($data['district'])){
                array_push($data['errors'],'District not selected.');
            }

            if(!is_null($data['district']))
            {
                $data['members'] = $this->deaconmodel->GetMembersByDistrict($data['district']);
            }

            if(count($data['errors']) > 0)
            {
                $this->view('deacons/add',$data);
                exit();
            }

            if(!$this->deaconmodel->CreateUpdate($data))
            {
                array_push($data['errors'],'Unable to save. Retry or contact admin');
                $this->view('deacons/add',$data);
                exit;
            }

            flash('deacon_msg', !$data['isedit'] ? "Deacon Added Successfully!" : 'Deacon Edited Successfully!');
            redirect('deacons');

        }
    }

    public function edit($id)
    {
        $deacon = $this->deaconmodel->GetDetails($id);
        $data = [
            'title' => 'Edit Deacon',
            'members' => $this->deaconmodel->GetMembersByDistrict($deacon->DistrictId),
            'districts' => $this->membermodel->getDistricts(),
            'years' => $this->parishofficialsmodel->getYears(),
            'id' => $deacon->ID,
            'isedit' => true,
            'deacon' => $deacon->DeaconId,
            'year' => $deacon->YearId,
            'district' => $deacon->DistrictId,
            'zone' => ucwords($deacon->Zone),
            'role' => '',
            'errors' => []
        ];
        $this->view('deacons/add',$data);
    }

    public function delete()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id = isset($_POST['id']) && !empty(trim($_POST['id'])) ? trim($_POST['id']) : null;
          
            if(is_null($id)){
                flash('deacon_msg', "Deacon not selected",'alert custom-danger alert-dismissible fade show');
                redirect('deacons');
                exit();
            }
            
            if(!$this->deaconmodel->Delete($id))
            {
                flash('deacon_msg', "Unable to deleted. Retry or contact admin",'alert custom-danger alert-dismissible fade show');
                redirect('deacons');
                exit();
            }

            flash('deacon_msg', "Deacon deleted Successfully!");
            redirect('deacons');

        }
    }
}