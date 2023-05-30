<?php
class Elder
{
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }

    function RightsSet($role)
    {
        return getdbvalue($this->db->dbh,'SELECT COUNT(*) FROM tblrolerights WHERE RoleId=?',[(int)$role]);
    }

    public function Create($data)
    {
        try {
            
            $this->db->dbh->beginTransaction();

            $this->db->query('INSERT INTO tblelders (ElderName,Contact) VALUES(:ename,:contact)');
            $this->db->bind(':ename',$data['name']);
            $this->db->bind(':contact',$data['contact']);
            $this->db->execute();
            $id = $this->db->dbh->lastInsertId();

            $this->db->query('INSERT INTO tbleldermovement (ElderId,ToCongregation,ToDistrict,TransferDate,IsTransfer) VALUES(:eid,:cong,:dist,:tdate,:istrans)');
            $this->db->bind(':eid',$id);
            $this->db->bind(':cong',$data['congregation']);
            $this->db->bind(':dist',$data['district']);
            $this->db->bind(':tdate',$data['date']);
            $this->db->bind(':istrans',0);
            $this->db->execute();

            $this->db->query('INSERT INTO tblusers (UserID,UserName,UsertypeId,`Password`,Active,contact,districtId,CongregationId) VALUES(:usid,:uname,:utype,:pass,:act,:contact,:district,:cong)');
            $this->db->bind(':usid',$data['userid']);
            $this->db->bind(':uname',$data['name']);
            $this->db->bind(':utype',4);
            $this->db->bind(':pass', password_hash('123456',PASSWORD_DEFAULT) );
            $this->db->bind(':act',1);
            $this->db->bind(':contact',$data['contact']);
            $this->db->bind(':district',$data['district']);
            $this->db->bind(':cong',$data['congregation']);
            $this->db->execute();
            $uid = $this->db->dbh->lastInsertId();

            if($this->RightsSet($data['role']) > 0){
                $this->db->query('CALL sp_role_pairing(:user1,:role)');
                $this->db->bind(':user1',$uid);
                $this->db->bind(':role',$data['role']);
                $this->db->execute();
            }

            if ($this->db->dbh->commit()) {
                return true;
            }
            else{
                return false;
            }

        } catch (\Exception $e) {
            if ($this->db->dbh->inTransaction()) {
                $this->db->dbh->rollback();
            }
            error_log($e->getMessage(),0);
            return false;
        }
    }

    public function CreateUpdate($data)
    {
        if(!$data['isedit']){
           return $this->Create($data);
        }
    }
}