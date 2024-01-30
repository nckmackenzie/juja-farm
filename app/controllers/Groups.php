<?php

class Groups extends Controller {
    private $authmodel;
    private $groupModel;
    public function __construct()
    {
        if (!isset($_SESSION['userId'])) {
            redirect('users');
        }
        $this->authmodel = $this->model('Auth');
        checkrights($this->authmodel,'groups');
        $this->groupModel = $this->model('Group');
    }
    public function index()
    {
        $groups = $this->groupModel->index();
        $data =[
            'groups' => $groups
        ];
        $this->view('groups/index',$data);
    }
    public function add()
    {
        $data = [
            'name' => '',
            'chairuserid' => '',
            'treasureruserid' => '',
            'secretaryuserid' => '',
            'id' => '',
            'isedit' => false,
            'errors' => [],
        ];
        $this->view('groups/add',$data);
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           $_POST = filter_input_array(INPUT_POST,FILTER_UNSAFE_RAW);
           $data = [
               'name' => isset($_POST['groupname']) && !empty(trim($_POST['groupname'])) ? trim(strtolower($_POST['groupname'])) : NULL,
               'chairuserid' => isset($_POST['chairuserid']) && !empty(trim($_POST['chairuserid'])) ? trim(strtolower($_POST['chairuserid'])) : NULL,
               'treasureruserid' => isset($_POST['treasureruserid']) && !empty(trim($_POST['treasureruserid'])) ? trim(strtolower($_POST['treasureruserid'])) : NULL,
               'secretaryuserid' => isset($_POST['secretaryuserid']) && !empty(trim($_POST['secretaryuserid'])) ? trim(strtolower($_POST['secretaryuserid'])) : NULL,
               'active' => isset($_POST['active']) ? 1 : 0,
               'isedit' => converttobool($_POST['isedit']),
               'id' => isset($_POST['id']) && !empty(trim($_POST['id'])) ? trim(strtolower($_POST['id'])) : NULL,
               'errors' => []
           ];
           //validate
           if (is_null($data['name'])) {
               array_push($data['errors'],'Enter Group Name.');
           }
           else{
               if (!$this->groupModel->checkExists($data['name'],$data['id'])) {
                    array_push($data['errors'],'Group Exists.');
               }
           }

           if(is_null($data['chairuserid']))
           {
                array_push($data['errors'],'Enter userid to be used by group chairmen.');
           }
           if(is_null($data['treasureruserid']))
           {
                array_push($data['errors'],'Enter userid to be used by group treasurers.');
           }
           if(is_null($data['secretaryuserid']))
           {
                array_push($data['errors'],'Enter userid to be used by group secretaries.');
           }

           if(!$data['isedit'] && $this->groupModel->useridexists($data['chairuserid']))
           {
                array_push($data['errors'],'Entered chairman UserID already exists.');
           }
           if(!$data['isedit'] && $this->groupModel->useridexists($data['treasureruserid']))
           {
                array_push($data['errors'],'Entered treasurer UserID already exists.');
           }
           if(!$data['isedit'] && $this->groupModel->useridexists($data['secretaryuserid']))
           {
                array_push($data['errors'],'Entered secretary UserID already exists.');
           }

           if(count($data['errors']) > 0){
                $this->view('groups/add',$data);
                exit();
           }

           if(!$this->groupModel->createupdate($data)){
                array_push($data['errors'],'Something Went Wrong!.');
                $this->view('groups/add',$data);
                exit();
           }

           flash('group_msg','Group Added Successfully!');
           redirect('groups');
           exit();
        }
    }
    public function edit($id)
    {
        $group = $this->groupModel->fetchGroup(decrypt($id));
        $data = [
            'id' => $group->ID,
            'name' => ucwords($group->groupName),
            'chairuserid' => !is_null($group->chairuserid) ? ucwords($group->chairuserid) : '',
            'treasureruserid' => !is_null($group->treasureruserid) ? ucwords($group->treasureruserid) : '' ,
            'secretaryuserid' => !is_null($group->secretaryuserid) ? ucwords($group->secretaryuserid) : '',
            'active' => $group->active,
            'isedit' => true,
            'errors' => [],
        ];
        $this->view('groups/edit',$data);
    }
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           $_POST = filter_input_array(INPUT_POST,FILTER_UNSAFE_RAW);
           $data = [
               'name' => trim(strtolower($_POST['groupname'])),
               'active' => isset($_POST['active']) ? 1 : 0,
               'id' => $_POST['id'],
               'name_err' => ''
           ];
           //validate
           if (empty($data['name'])) {
               $data['name_err'] = 'Enter Group Name';
           }
           
           if (empty($data['name_err'])) {
                if ($this->groupModel->update($data)) {
                    flash('group_msg','Group Edited Successfully!');
                    redirect('groups');
                }
                else{
                    flash('group_msg','Something Went Wrong!','alert alert-danger');
                    redirect('groups');
                }
           }
           else{
               $this->view('groups/add',$data);
           }
        }
        else{
            $data = [
                'name' => '',
                'name_err' =>''
            ];
            $this->view('groups/add',$data); 
        }
    }
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST,FILTER_UNSAFE_RAW);
            $data = [
                'id' => $_POST['id'],
                'name' => trim(strtolower($_POST['groupname']))
            ];
            //validate
            if (isset($data['id'])) {
                 if ($this->groupModel->delete($data)) {
                     flash('group_msg','Group Deleted Successfully!');
                     redirect('groups');
                 }
                 else{
                     flash('group_msg','Something Went Wrong!','alert alert-danger');
                     redirect('groups');
                 }
            }
            else{
                $this->view('groups/index',$data);
            }
         }
    }

    public function membership()
    {
        $data = [
            'groups' => $this->groupModel->getgroups(),
            'members' => $this->groupModel->GetGroupMembership(),
        ];
        $this->view('groups/membership',$data);
    }

    public function getmembers()
    {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $data = [
                'group' => isset($_GET['group']) && !empty(trim($_GET['group'])) ? (int)trim($_GET['group']) : null,
                'members' => []
            ];

            if(is_null($data['group'])){
                http_response_code(400);
                echo json_encode(['success' => false,'message' => 'Select group']);
                exit;
            }
            
            $members = $this->groupModel->GetMembers($data['group']);
            foreach($members as $member)
            {
                array_push($data['members'],['id' => $member->ID,'memberName' => ucwords($member->memberName)]);
            }

            echo json_encode(['success' => true,'data' => $data['members']]);
        }
    }

    public function addmembership()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fields = json_decode(file_get_contents('php://input'));

            $data = [
                'group' => isset($fields->group) && !empty(trim($fields->group)) ? (int)trim($fields->group) : null,
                'members' => isset($fields->members) && !empty($fields->members) ? $fields->members : null,
                'errors' => []
            ];

            if(is_null($data['group'])){
                http_response_code(400);
                echo json_encode(['success' => false,'message' => 'Select group','errors' => null]);
                exit;
            }

            if(is_null($data['members'])){
                http_response_code(400);
                echo json_encode(['success' => false,'message' => 'Select one or more members','errors' => null]);
                exit;
            }

            $existing_members = [];
            $groupmembers = $this->groupModel->GetGroupMembers($data['group']);
            foreach($groupmembers as $groupmember){
                array_push($existing_members,$groupmember->memberId);
            }

            foreach($data['members'] as $member){
                if(in_array($member,$existing_members)){
                    array_push($data['errors'], 'Member already added');
                }
            }
            
            if(count($data['errors']) > 0){
                http_response_code(400);
                echo json_encode(['success' => false,'message' => 'One or more members already added to selected group.','errors' => $data['errors']]);
                exit;
            }
         

            if(!$this->groupModel->AddMembership($data)){
                http_response_code(500);
                echo json_encode(['success' => false,'message' => 'Unable to save membership at this time','errors' => null]);
                exit;
            }

            echo json_encode(['success' => true,'message' => 'Saved successfully.','errors' => null]);
            exit;
        }
    }

    public function deletemembership()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id = isset($_POST['id']) && !empty(trim($_POST['id'])) ? (int)trim($_POST['id']) : null;
            if(is_null($id)){
                flash('groupmember_msg','Select membership to delete','alert alert-danger');
                redirect('groups/membership');
                exit;
            }

            if(!$this->groupModel->DeleteMembership($id)){
                flash('groupmember_msg','Unable to delete selected membership.','alert alert-danger');
                redirect('groups/membership');
                exit;
            }

            flash('groupmember_msg','Membership deleted successfully.');
            redirect('groups/membership?redirect=true');
        }
    }
}