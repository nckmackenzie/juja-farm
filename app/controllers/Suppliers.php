<?php
class Suppliers extends Controller 
{
    public function __construct()
    {
        if(!isset($_SESSION['userId'])){
            redirect('users');
            exit;
        }
        $this->authmodel = $this->model('Auth');
        checkrights($this->authmodel,'suppliers');
        $this->suppliermodel = $this->model('Supplier');
    }

    //index
    public function index()
    {
        $data = [
            'suppliers' => $this->suppliermodel->GetSuppliers()
        ];
        $this->view('suppliers/index',$data);
        exit;
    }

    //add suppliers method
    public function add()
    {
        $data = [
            'title' => 'Add Supplier',
            'id' => 0,
            'isedit' => false,
            'suppliername' => '',
            'contact' => '',
            'address' => '',
            'contactperson' => '',
            'email' => '',
            'pin' => '',
            'openingbal' => '',
            'asof' => '',
        ];
        $this->view('suppliers/add',$data);
        exit;
    }

    //create or update supplier method
    public function createupdate()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fields = json_decode(file_get_contents('php://input')); //extract submited json data
            $data = [
                'id' => (int)trim($fields->id),
                'isedit' => converttobool(trim($fields->isedit)),
                'suppliername' => !empty($fields->suppliername) ? strtolower(trim($fields->suppliername)) : null,
                'contact' => !empty($fields->contact) ? trim($fields->contact) : null,
                'pin' => !empty($fields->pin) ? trim($fields->pin) : null,
                'address' => !empty($fields->address) ? strtolower(trim($fields->address)) : null,
                'email' => !empty($fields->email) ? strtolower(trim($fields->email)) : null,
                'contactperson' => !empty($fields->contactperson) ? strtolower(trim($fields->contactperson)) : null,
                'balance' => converttobool($fields->isedit) ? null : (!empty($fields->openingbal) ? trim($fields->openingbal) : 0),
                'asof' => converttobool($fields->isedit) ? null : (!empty($fields->asof) ? date('Y-m-d',strtotime(trim($fields->asof))) : null)
            ];
            //validate date
            if(is_null($data['suppliername'])){
                http_response_code(400);
                echo json_encode(['message' => 'Enter supplier name']);
                exit;
            }
            if(!$this->suppliermodel->CheckExists($data['suppliername'],$data['id'])){
                http_response_code(400);
                echo json_encode(['message' => 'Supplier name exists']);
                exit;
            }
            if($data['balance'] !== 0 && is_null($data['asof']) && !$data['isedit']){
                http_response_code(400);
                echo json_encode(['message' => 'Enter date of balance']);
                exit;
            }
            if($data['asof'] > date('Y-m-d')){
                http_response_code(400);
                echo json_encode(['message' => 'Invalid balance date selected']);
                exit;
            }
            //if was unable to save
            if(!$this->suppliermodel->CreateUpdate($data)){
                http_response_code(400);
                echo json_encode(['message' => 'Unable to save supplier. Retry or contact admin']);
                exit;
            }
            //success
            echo json_encode(['success' => true]);
            exit;

        }else{
            redirect('users/deniedaccess');
            exit;
        }
    }

    public function edit($id)
    {
        $supplier = $this->suppliermodel->GetSupplier($id);
        $data = [
            'title' => 'Edit Supplier',
            'id' => $supplier->ID,
            'isedit' => true,
            'suppliername' => strtoupper($supplier->supplierName),
            'contact' => $supplier->contact,
            'address' => strtoupper($supplier->address),
            'contactperson' => strtoupper($supplier->contactPerson),
            'email' => $supplier->email,
            'pin' => strtoupper($supplier->pin),
        ];
        $this->view('suppliers/add',$data);
        exit;
    }

    public function delete()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $id = !empty(trim($_POST['id'])) ? trim(htmlentities($_POST['id'])) : null;
            
            if(is_null($id)){
                flash('supplier_msg','Unable to get selected supplier',alerterrorclass());
                redirect('suppliers');
                exit;
            }

            if(!$this->suppliermodel->Referenced($id)){
                flash('supplier_msg','Cannot delete supplier as they are referenced elsewhere',alerterrorclass());
                redirect('suppliers');
                exit;
            }

            if(!$this->suppliermodel->Delete($id)){
                flash('supplier_msg','Something went wrong while deleting. Retry or contact admin',alerterrorclass());
                redirect('suppliers');
                exit;
            }

            flash('supplier_msg','Deleted successfully!');
            redirect('suppliers');
            exit;

        }else{
            redirect('users/deniedaccess');
            exit;
        }
    }
}