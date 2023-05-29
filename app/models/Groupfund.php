<?php
class Groupfund
{
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }

    public function CheckRights($form)
    {
        return checkuserrights($this->db->dbh,$_SESSION['userId'],$form);
    }

    public function GetReqNo()
    {
        return getuniqueid($this->db->dbh,'ReqNo','tblfundrequisition',(int)$_SESSION['congId']);
    }

    public function PendingApprovalCount($gid)
    {
        $sql = 'SELECT COUNT(*) FROM tblfundrequisition WHERE (GroupId = ?) AND (Deleted = 0) AND (`Status` = 0)';
        return getdbvalue($this->db->dbh,$sql,[$gid]);
    }

    public function GetRequests()
    {
        $sql = 'SELECT * FROM vw_group_requisitions  
                          WHERE (congregationId = ?)
                          ORDER BY `Status`,RequestDate DESC';
        return loadresultset($this->db->dbh,$sql,[$_SESSION['congId']]);
    }

    public function GetGroups()
    {
        $this->db->query('SELECT ID,UCASE(groupName) as groupName 
                          FROM tblgroups 
                          WHERE (active = 1) AND (deleted = 0) AND (congregationId = :cid)
                          ORDER BY groupName');
        $this->db->bind(':cid',$_SESSION['congId']);
        return $this->db->resultSet();
    }

    public function GetBalance($group,$date)
    {
        return getdbvalue($this->db->dbh,'SELECT getmmfopeningbalbydate(?,?)',[$group,$date]);
    }

    public function CreateUpdate($data)
    {
        if(!$data['isedit']){
            $this->db->query('INSERT INTO tblfundrequisition (ReqNo,RequisitionDate,GroupId,Purpose,AmountRequested,DontDeduct,CongregationId) 
                              VALUES(:reqno,:rdate,:gid,:purpose,:amount,:deduct,:cid)');
            $this->db->bind(':reqno',$this->GetReqNo());
        }else{
            $this->db->query('UPDATE tblfundrequisition SET RequisitionDate=:rdate,GroupId=:gid,Purpose=:purpose,AmountRequested=:amount,DontDeduct=:deduct 
                              WHERE (ID = :id)');
        }
        $this->db->bind(':rdate',!empty($data['reqdate']) ? $data['reqdate'] : null);
        $this->db->bind(':gid',!empty($data['group']) ? $data['group'] : null);
        $this->db->bind(':purpose',!empty($data['reason']) ? strtolower($data['reason']) : null);
        $this->db->bind(':amount',!empty($data['amount']) ? $data['amount'] : null);
        $this->db->bind(':deduct',$data['dontdeduct']);
        if($data['isedit']){
            $this->db->bind(':id',$data['id']);
        }else{
            $this->db->bind(':cid',$_SESSION['congId']);
        }
        if(!$this->db->execute()){
            return false;
        }
        return true;
    }

    public function GetGroupCongregation($gid)
    {
        return getdbvalue($this->db->dbh,'SELECT congregationId FROM tblgroups WHERE ID = ?',[$gid]);
    }

    public function GetRequest($id)
    {
        $this->db->query('SELECT * FROM tblfundrequisition WHERE (ID=:id)');
        $this->db->bind(':id',(int)$id);
        return $this->db->single();
    }

    public function Delete($id)
    {
        $this->db->query('UPDATE tblfundrequisition SET Deleted = 1 WHERE (ID = :id)');
        $this->db->bind(':id',(int)$id);
        if(!$this->db->execute()){
            return false;
        }
        return true;
    }

    public function GetRequestStatus($id)
    {
        return getdbvalue($this->db->dbh,'SELECT `Status` FROM tblfundrequisition WHERE ID = ?',[$id]);
    }

    public function GetApprovals()
    {
        $sql = 'SELECT * FROM vw_requisition_approvals   
                WHERE (congregationId = ?)';
        return loadresultset($this->db->dbh,$sql,[$_SESSION['congId']]);
    }

    public function PayMethods()
    {
        return paymentMethods($this->db->dbh);
    }

    public function GetBanks()
    {
        $this->db->query("SELECT ID,
                                 CONCAT(UCASE(`accountType`),'-',IFNULL(`accountNo`,'')) As Bank
                          FROM   tblaccounttypes 
                          WHERE  (isBank=1) AND (Deleted=0) AND (congregationId=:cid)");
        $this->db->bind(':cid',$_SESSION['congId']);
        return $this->db->resultSet();
    }

    public function GetGroupName($id)
    {
        return getdbvalue($this->db->dbh,'SELECT groupName FROM tblgroups WHERE ID = ?',[$id]);
    }

    public function Approve($data)
    {
        try {
            $this->db->dbh->beginTransaction();
            $desc = strtolower($data['group']) .' funds request approval';

            $this->db->query('UPDATE tblfundrequisition 
                                SET AmountApproved = :app,ApprovalDate=:appdate,ApprovedBy = :appby,`Status` = 1
                              WHERE (ID = :id)');
            $this->db->bind(':app',!empty($data['approved']) ? $data['approved'] : null);
            $this->db->bind(':appdate',date('Y-m-d'));
            $this->db->bind(':appby',(int)$_SESSION['userId']);
            $this->db->bind(':id',$data['id']);
            $this->db->execute();

            if(!$data['dontdeduct']){
                $this->db->query('INSERT INTO tblmmf (TransactionDate,GroupId,Credit,BankId,Reference,Narration,TransactionType,TransactionId,
                CongregationId) VALUES(:tdate,:gid,:credit,:bid,:reference,:narr,:ttype,:tid,:cid)');
                $this->db->bind(':tdate',!empty($data['paydate']) ? $data['paydate'] : null);
                $this->db->bind(':gid',trim($_POST['groupid']));
                $this->db->bind(':credit',!empty($data['approved']) ? floatval($data['approved']) : null);
                $this->db->bind(':bid',!empty($data['bank']) ? $data['bank'] : null);
                $this->db->bind(':reference',!empty($data['reference']) ? strtolower($data['reference']) : null);
                $this->db->bind(':narr',$data['reason']);
                $this->db->bind(':ttype',12);
                $this->db->bind(':tid',$data['id']);
                $this->db->bind(':cid',$_SESSION['congId']);
                $this->db->execute();
            }
           

            $gbhparent = getparentgl($this->db->dbh,'groups balances held');
            $pettycash_name = 
            $cashparent = getparentgl($this->db->dbh,'cash at hand');
            if(!$data['dontdeduct']){
                saveToLedger($this->db->dbh,$data['paydate'],'groups balances held',$gbhparent,$data['approved'],0,$desc,
                                            4,12,$data['id'],$_SESSION['congId']);
            }else{
                saveToLedger($this->db->dbh,$data['paydate'],"groups' expenses","groups' expenses",$data['approved'],0,$desc,
                                            2,12,$data['id'],$_SESSION['congId']);
            }
            
            if((int)$data['paymethod'] === 1){
                saveToLedger($this->db->dbh,$data['paydate'],'cash at hand',$cashparent,0,$data['approved'],$desc,
                         3,12,$data['id'],$_SESSION['congId']);
            }else{
                saveToLedger($this->db->dbh,$data['paydate'],'cash at bank',$cashparent,0,$data['approved'],$desc,
                             3,12,$data['id'],$_SESSION['congId']);

                saveToBanking($this->db->dbh,$data['bank'],$data['paydate'],0,$data['approved'],2,
                             $data['reference'],12,$data['id'],$_SESSION['congId']); 
            }

            if(!$this->db->dbh->commit()){
                return false;
            }else{
                return true;
            }

        } catch (Exception $e) {
            if($this->db->dbh->inTransaction()){
                $this->db->dbh->rollback();
            }
           error_log($e->getMessage(),0);
        }
    }
    
    public function Reverse($id)
    {
        try {
            $this->db->dbh->beginTransaction();
            
            $this->db->query('UPDATE tblfundrequisition 
                                SET AmountApproved = null, ApprovalDate = null,ApprovedBy = null,`Status` = 0
                              WHERE (ID = :id)');
            $this->db->bind(':id',$id);
            $this->db->execute();

            $this->db->query('DELETE FROM tblmmf WHERE (TransactionType = 12) AND (TransactionId = :id)');
            $this->db->bind(':id',$id);
            $this->db->execute();

            deleteLedgerBanking($this->db->dbh,12,$id);

            if(!$this->db->dbh->commit()){
                return false;
            }else{
                return true;
            }

        } catch (Exception $e) {
            if($this->db->dbh->inTransaction()){
                $this->db->dbh->rollback();
            }
            error_log($e->getMessage(),0);
        }
    }
}