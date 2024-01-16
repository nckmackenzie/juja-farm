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
}