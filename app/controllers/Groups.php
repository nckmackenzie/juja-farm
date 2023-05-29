<?php

class Groups extends Controller {
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
            'name_err' =>''
        ];
        $this->view('groups/add',$data);
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           $_POST = filter_input_array(INPUT_POST,FILTER_UNSAFE_RAW);
           $data = [
               'name' => trim(strtolower($_POST['groupname'])),
               'active' => isset($_POST['active']) ? 1 : 0,
               'name_err' => ''
           ];
           //validate
           if (empty($data['name'])) {
               $data['name_err'] = 'Enter Group Name';
           }
           else{
               if (!$this->groupModel->checkExists($data['name'])) {
                    $data['name_err'] = 'Group Exists';
               }
           }
           if (empty($data['name_err'])) {
                if ($this->groupModel->create($data)) {
                    flash('group_msg','Group Added Successfully!');
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
    public function edit($id)
    {
        $groups = $this->groupModel->fetchGroup(decrypt($id));
        $data = ['groups' =>$groups];
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
         else{
             $data = [
                 'name' => '',
                 'name_err' =>''
             ];
             $this->view('groups/index',$data); 
         }
    }
}