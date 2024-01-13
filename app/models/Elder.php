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

    public function GetElders()
    {
        return loadresultset($this->db->dbh,'SELECT * FROM vw_elders ORDER BY ElderName',[]);
    }

    public function genMemberNo($cong)
    {
       $this->db->query('SELECT (IFNULL(MAX(RIGHT(memberId,4)),0) + 1) as new FROM tblmember
                         WHERE (congregationId=:cong)');
       $this->db->bind(':cong',$cong);
       $result = $this->db->single();
       $added =str_pad($result->new,4,'0',STR_PAD_LEFT); 

       $this->db->query('SELECT prefix FROM tblcongregation WHERE (ID=:cong)');
       $this->db->bind(':cong',$cong);
       $prefix = $this->db->single();
       $new = $prefix->prefix .'/'. $added;
       return $new;
    }

    public function Create($data)
    {
        try {
            
            $this->db->dbh->beginTransaction();
            $memberid = getLastId($this->db->dbh,'tblmember');
            $mno = $this->genMemberNo($data['congregation']);

            $this->db->query('INSERT INTO tblmember (ID,memberId,memberName,contact,districtId,positionId,congregationId)
                              VALUES(:id,:mid,:mname,:contact,:did,:pid,:cid)');
            $this->db->bind(':id',$memberid);
            $this->db->bind(':mid',$mno);     
            $this->db->bind(':mname',$data['name']);
            $this->db->bind(':contact',$data['contact']);   
            $this->db->bind(':did',$data['district']);
            $this->db->bind(':pid',2);
            $this->db->bind(':cid',$data['congregation']);
            $this->db->execute();

            $this->db->query('INSERT INTO tblelders (ElderName,Contact,MemberId) VALUES(:ename,:contact,:mid)');
            $this->db->bind(':ename',$data['name']);
            $this->db->bind(':contact',$data['contact']);
            $this->db->bind(':mid',$memberid);
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

    public function Edit($data)
    {
        try {
            
            $this->db->dbh->beginTransaction();
 
            $this->db->query('UPDATE tblmember SET memberName=:mname,contact=:contact,districtId=:did,congregationId=:cid
                              WHERE (ID=:id)');
           
            $this->db->bind(':mname',$data['name']);
            $this->db->bind(':contact',$data['contact']);   
            $this->db->bind(':did',$data['district']);
            $this->db->bind(':cid',$data['congregation']);
            $this->db->bind(':id',$data['memberid']);
            $this->db->execute();

            $this->db->query('UPDATE tblelders SET ElderName=:ename,Contact=:contact WHERE (ID=:id)');
            $this->db->bind(':ename',$data['name']);
            $this->db->bind(':contact',$data['contact']);
            $this->db->bind(':id',$data['id']);
            $this->db->execute();
     
            $this->db->query('UPDATE tbleldermovement SET ToCongregation=:cong,ToDistrict=:dist,TransferDate=:tdate
                              WHERE (ElderId=:id AND IsTransfer=0)');
            $this->db->bind(':cong',$data['congregation']);
            $this->db->bind(':dist',$data['district']);
            $this->db->bind(':tdate',$data['date']);
            $this->db->bind(':id',$data['id']);
            $this->db->execute();


            $this->db->query('UPDATE tblusers SET UserID=:usid,UserName=:uname,contact=:contact,districtId=:district,CongregationId=:cong
                              WHERE(ID=:id)');
            $this->db->bind(':usid',$data['userid']);
            $this->db->bind(':uname',$data['name']);
            $this->db->bind(':contact',$data['contact']);
            $this->db->bind(':district',$data['district']);
            $this->db->bind(':cong',$data['congregation']);
            $this->db->bind(':id',$data['useridprimary']);
            $this->db->execute();
            
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
        if(!$data['isedit'])
        {
           return $this->Create($data);
        }
        else
        {
            return $this->Edit($data);
        }
    }

    public function GetUserDetails($id)
    {
        $sql = 'SELECT 
                    e.ID,
                    e.ElderName,
                    e.Contact,
                    e.MemberId,
                    m.districtId,
                    m.congregationId
                FROM tblelders e join tblmember m on e.MemberId = m.ID 
                WHERE e.ID = :id';
        $this->db->query($sql);
        $this->db->bind(':id',$id);
        return $this->db->single();
    }

    public function GetSetDate($id)
    {
        return getdbvalue($this->db->dbh,'SELECT TransferDate FROM tbleldermovement  WHERE (ElderId=?) AND (IsTransfer=0)',[$id]);
    }

    public function GetUserId($id)
    {
        $contact = getdbvalue($this->db->dbh,'SELECT Contact FROM tblelders  WHERE (ID=?)',[$id]);
        return  getdbvalue($this->db->dbh,'SELECT ID FROM tblusers WHERE (contact=?)',[$contact]);
    }

    public function GetElderContactAndId($id)
    {
        $contact = getdbvalue($this->db->dbh,'SELECT Contact FROM tblelders WHERE (ID=?)',[$id]);
        $userid = getdbvalue($this->db->dbh,'SELECT ID FROM tblusers WHERE (contact=?)',[$contact]);
        $username = getdbvalue($this->db->dbh,'SELECT UserID FROM tblusers WHERE (ID=?)',[$userid]);
        return [substr($contact,1),$userid,$username];
    }

    public function GetElderDetails($id)
    {
        $name = getdbvalue($this->db->dbh,'SELECT ElderName FROM tblelders WHERE ID=?',[$id]);
        $memberid = getdbvalue($this->db->dbh,'SELECT MemberId FROM tblelders WHERE ID=?',[$id]);
        $oldcong = getdbvalue($this->db->dbh,'SELECT ToCongregation FROM tbleldermovement WHERE ElderId=? ORDER BY ID DESC LIMIT 1',[$id]);
        $olddist = getdbvalue($this->db->dbh,'SELECT ToDistrict FROM tbleldermovement WHERE ElderId=? ORDER BY ID DESC LIMIT 1',[$id]);
        $oldcongname = getdbvalue($this->db->dbh,'SELECT getcongregationname(?)',[$oldcong]);
        $olddistname = getdbvalue($this->db->dbh,'SELECT getdistrictname(?)',[$olddist]);
        return [$oldcong,$olddist,$oldcongname,$olddistname,$name,$memberid];
    }

    public function GetLastIDAndPrefix($cong)
    {
        $sql = 'SELECT IFNULL(MAX(RIGHT(memberId,4)),0) As id FROM `tblmember` where congregationId = ?';
        $lastid = getdbvalue($this->db->dbh,$sql,[$cong]);
        $sql1 ='SELECT prefix FROM tblcongregation WHERE ID = ?';
        $prefix = getdbvalue($this->db->dbh,$sql1,[$cong]);
        return [$lastid,$prefix];
    }

    public function Transfer($data)
    {
        $firstId = (int)$this->GetLastIDAndPrefix($data['congregation'])[0] + 1;
        $prefix = $this->GetLastIDAndPrefix($data['congregation'])[1];
        $mid = getdbvalue($this->db->dbh,'SELECT MemberId FROM tblelders WHERE ID=?',[$data['elderid']]);
        try {
            
            $this->db->dbh->beginTransaction();

            $this->db->query('INSERT INTO tbleldermovement (ElderId,FromCongregation,ToCongregation,FromDistrict,ToDistrict,TransferDate,Reason) VALUES(:eid,:fcong,:cong,:fdist,:dist,:tdate,:reason)');
            $this->db->bind(':eid',$data['elderid']);
            $this->db->bind(':fcong',$data['oldcongregation']);
            $this->db->bind(':cong',$data['congregation']);
            $this->db->bind(':fdist',$data['olddistrict']);
            $this->db->bind(':dist',$data['district']);
            $this->db->bind(':tdate',$data['date']);
            $this->db->bind(':reason',$data['reason']);
            $this->db->execute();

            $this->db->query('UPDATE tblusers SET districtId=:district,CongregationId=:cong WHERE (TransferId=:tid)');
            $this->db->bind(':district',$data['district']);
            $this->db->bind(':cong',$data['congregation']);
            $this->db->bind(':tid',$data['elderid']);
            $this->db->execute();

            $memberno = formatStringId($firstId);
            $memberid = $prefix .'/'.$memberno;
            $this->db->query('INSERT INTO tblmembertransfers (memberId,fromId,toId,transferDate,
                                        reason,fromDistrict,toDistrict)
                            VALUES(:mid,:fid,:tid,:tdate,:reason,:fdist,:tdist)');
            $this->db->bind(':mid',$mid);
            $this->db->bind(':fid',!empty($data['oldcongregation']) ? $data['oldcongregation'] : null);
            $this->db->bind(':tid',!empty($data['congregation']) ? $data['congregation'] : null);
            $this->db->bind(':tdate',!empty($data['date']) ? $data['date'] : null);
            $this->db->bind(':reason',!empty($data['reason']) ? strtolower($data['reason']) : null);
            $this->db->bind(':fdist',!empty($data['olddistrict']) ? $data['olddistrict'] : null);
            $this->db->bind(':tdist',!empty($data['district']) ? $data['district'] : null);
            $this->db->execute();

            $this->db->query('UPDATE tblmember SET 
                                congregationId = :cid,
                                districtId=:did,
                                memberId = :mid 
                                WHERE (ID=:id)');
            $this->db->bind(':cid',$data['congregation']);
            $this->db->bind(':did',$data['district']);
            $this->db->bind(':mid',$memberid);
            $this->db->bind(':id', $mid);
            $this->db->execute();
          
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

    public function Delete($id)
    {
        $mid = getdbvalue($this->db->dbh,'SELECT MemberId FROM tblelders WHERE ID=?',[$id]);
        try {
            
            $this->db->dbh->beginTransaction();

            $this->db->query('UPDATE tblelders SET Deleted=1 WHERE (ID=:id)');
            $this->db->bind(':id',$id);
            $this->db->execute();

            $this->db->query('UPDATE tblusers SET Active=0 WHERE (TransferId=:tid)');
            $this->db->bind(':tid',$id);
            $this->db->execute();

            $this->db->query('UPDATE tblmember SET deleted=0 WHERE (ID=:id)');
            $this->db->bind(':id',$mid);
            $this->db->execute();
          
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
}