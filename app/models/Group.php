<?php
class Group {
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }
    function createLog($con,$activity){
        $this->db->query('INSERT INTO tbllogs (userId,activity,activityDate,congregationId)
                          VALUES(:user,:act,:actdate,:congid)');
        $currdate = date("Y/m/d");                  
        $this->db->bind(':user',$_SESSION['userId']);
        $this->db->bind(':act',$activity);
        $this->db->bind(':actdate',$currdate);
        $this->db->bind(':congid',$_SESSION['congId']);
        $this->db->execute();
    }
    public function getgroups()
    {
        return loadresultset($this->db->dbh,'SELECT ID,UCASE(groupName) AS groupName 
                                             FROM tblgroups 
                                             WHERE (deleted=0) AND (congregationId=?)',[$_SESSION['congId']]);
    }
    public function GetMembers($group)
    {
        return loadresultset($this->db->dbh,'SELECT ID,UCASE(memberName) AS memberName 
                                             FROM tblmember 
                                             WHERE (deleted=0) AND (congregationId=?) AND ID NOT IN 
                                             (SELECT memberId FROM tblgroupmembership WHERE (groupId=?))
                                             ORDER BY memberName',[$_SESSION['congId'],$group]);
    }
    public function GetGroupMembers($group)
    {
        return loadresultset($this->db->dbh,'SELECT memberId FROM tblgroupmembership WHERE groupId=?',[$group]);
    }
    public function GetGroupMembership()
    {
        return loadresultset($this->db->dbh,'SELECT * FROM vw_group_memberships WHERE congregationId=?',[$_SESSION['congId']]);
    }
    public function index()
    {
        $this->db->query("SELECT ID,
                                 groupName,
                                 active,
                                 IF(active=1,'Active','Inactive') as `status`
                          FROM tblgroups
                          WHERE (deleted=:del) AND (congregationId=:cid)
                          ORDER BY groupName");
        $this->db->bind(':del',$_SESSION['zero']);
        $this->db->bind(':cid',$_SESSION['congId']);
        return $this->db->resultSet();
    }
    public function checkExists($name,$id)
    {
        $this->db->query('SELECT ID FROM tblgroups WHERE (groupName=:ame)
                          AND (congregationId=:cid) AND (ID <> :id)');
        $this->db->bind(':ame',$name);
        $this->db->bind(':cid',$_SESSION['congId']);
        $this->db->bind(':id',$id);
        $this->db->execute();
        if ($this->db->rowCount() > 0) {
           return false;
        }else{
            return true;
        }
    }

    function RightsSet($role)
    {
        return getdbvalue($this->db->dbh,'SELECT COUNT(*) FROM tblrolerights WHERE RoleId=?',[(int)$role]);
    }

    public function useridexists($userid)
    {
        $count = getdbvalue($this->db->dbh,'SELECT COUNT(*) FROM tblusers 
                                            WHERE (UserID=?) AND (CongregationId=?)',[$userid,$_SESSION['congId']]);
        if((int)$count > 0) return true;
        return false;
    }

    function userscreated($groupid)
    {
        return getdbvalue($this->db->dbh,'SELECT COUNT(*) FROM tblusers WHERE GroupId=?',[$groupid]);
    }
    
    function create($data)
    {
        try {
            
            $this->db->dbh->beginTransaction();
            $this->db->query('INSERT INTO tblgroups (groupName,active,chairuserid,treasureruserid,secretaryuserid,congregationId) 
                              VALUES(:did,:act,:chair,:treas,:sec,:cid)');
            $this->db->bind(':did',$data['name']);
            $this->db->bind(':act',$data['active']);
            $this->db->bind(':chair',$data['chairuserid']);
            $this->db->bind(':treas',$data['treasureruserid']);
            $this->db->bind(':sec',$data['secretaryuserid']);
            $this->db->bind(':cid',$_SESSION['congId']);
            $this->db->execute();
            $id = $this->db->dbh->lastInsertId();

            $this->db->query('INSERT INTO tblusers (UserID,UserName,UsertypeId,`Password`,Active,GroupId,CongregationId) VALUES(:usid,:uname,:utype,:pass,:act,:group,:cong)');
            $this->db->bind(':usid',$data['chairuserid']);
            $this->db->bind(':uname',$data['name'] . 'chairman');
            $this->db->bind(':utype',3);
            $this->db->bind(':pass', password_hash('123456',PASSWORD_DEFAULT) );
            $this->db->bind(':act',1);
            $this->db->bind(':group',$id);
            $this->db->bind(':cong',$_SESSION['congId']);
            $this->db->execute();
            $chairid = $this->db->dbh->lastInsertId();

            if($this->RightsSet(2) > 0){
                $this->db->query('CALL sp_role_pairing(:user1,:role)');
                $this->db->bind(':user1',$chairid);
                $this->db->bind(':role',2);
                $this->db->execute();
            }

            $this->db->query('INSERT INTO tblusers (UserID,UserName,UsertypeId,`Password`,Active,GroupId,CongregationId) VALUES(:usid,:uname,:utype,:pass,:act,:group,:cong)');
            $this->db->bind(':usid',$data['treasureruserid']);
            $this->db->bind(':uname',$data['name'] . 'treasurer');
            $this->db->bind(':utype',3);
            $this->db->bind(':pass', password_hash('123456',PASSWORD_DEFAULT) );
            $this->db->bind(':act',1);
            $this->db->bind(':group',$id);
            $this->db->bind(':cong',$_SESSION['congId']);
            $this->db->execute();
            // $treasurerid = $this->db->dbh->lastInsertId();

            // if($this->RightsSet(2) > 0){
            //     $this->db->query('CALL sp_role_pairing(:user1,:role)');
            //     $this->db->bind(':user1',$treasurerid);
            //     $this->db->bind(':role',2);
            //     $this->db->execute();
            // }

            $this->db->query('INSERT INTO tblusers (UserID,UserName,UsertypeId,`Password`,Active,GroupId,CongregationId) VALUES(:usid,:uname,:utype,:pass,:act,:group,:cong)');
            $this->db->bind(':usid',$data['secretaryuserid']);
            $this->db->bind(':uname',$data['name'] . 'secretary');
            $this->db->bind(':utype',3);
            $this->db->bind(':pass', password_hash('123456',PASSWORD_DEFAULT) );
            $this->db->bind(':act',1);
            $this->db->bind(':group',$id);
            $this->db->bind(':cong',$_SESSION['congId']);
            $this->db->execute();
            $secretaryid = $this->db->dbh->lastInsertId();

            if($this->RightsSet(3) > 0){
                $this->db->query('CALL sp_role_pairing(:user1,:role)');
                $this->db->bind(':user1',$secretaryid);
                $this->db->bind(':role',3);
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
    public function fetchGroup($id)
    {
        $this->db->query('SELECT * FROM tblgroups WHERE (ID=:id)');
        $this->db->bind(':id',$id);
        return $this->db->single();
    }

    function update($data)
    {
        try {
            
            $this->db->dbh->beginTransaction();
            $this->db->query('UPDATE tblgroups SET groupName=:gname,active=:act,chairuserid=:chair,treasureruserid=:treas,
                                                   secretaryuserid=:sec 
                              WHERE (ID=:id)');
            $this->db->bind(':gname',$data['name']);
            $this->db->bind(':act',$data['active']);
            $this->db->bind(':chair',$data['chairuserid']);
            $this->db->bind(':treas',$data['treasureruserid']);
            $this->db->bind(':sec',$data['secretaryuserid']);
            $this->db->bind(':id',$data['id']);
            $this->db->execute();
          
            if((int)$this->userscreated($data['id']) === 0){
                $this->db->query('INSERT INTO tblusers (UserID,UserName,UsertypeId,`Password`,Active,GroupId,CongregationId) VALUES(:usid,:uname,:utype,:pass,:act,:group,:cong)');
                $this->db->bind(':usid',$data['chairuserid']);
                $this->db->bind(':uname',$data['name'] . 'chairman');
                $this->db->bind(':utype',3);
                $this->db->bind(':pass', password_hash('123456',PASSWORD_DEFAULT) );
                $this->db->bind(':act',1);
                $this->db->bind(':group',$data['id']);
                $this->db->bind(':cong',$_SESSION['congId']);
                $this->db->execute();
                $chairid = $this->db->dbh->lastInsertId();

                if($this->RightsSet(2) > 0){
                    $this->db->query('CALL sp_role_pairing(:user1,:role)');
                    $this->db->bind(':user1',$chairid);
                    $this->db->bind(':role',2);
                    $this->db->execute();
                }

                $this->db->query('INSERT INTO tblusers (UserID,UserName,UsertypeId,`Password`,Active,GroupId,CongregationId) VALUES(:usid,:uname,:utype,:pass,:act,:group,:cong)');
                $this->db->bind(':usid',$data['treasureruserid']);
                $this->db->bind(':uname',$data['name'] . 'treasurer');
                $this->db->bind(':utype',3);
                $this->db->bind(':pass', password_hash('123456',PASSWORD_DEFAULT) );
                $this->db->bind(':act',1);
                $this->db->bind(':group',$data['id']);
                $this->db->bind(':cong',$_SESSION['congId']);
                $this->db->execute();

                $this->db->query('INSERT INTO tblusers (UserID,UserName,UsertypeId,`Password`,Active,GroupId,CongregationId) VALUES(:usid,:uname,:utype,:pass,:act,:group,:cong)');
                $this->db->bind(':usid',$data['secretaryuserid']);
                $this->db->bind(':uname',$data['name'] . 'secretary');
                $this->db->bind(':utype',3);
                $this->db->bind(':pass', password_hash('123456',PASSWORD_DEFAULT) );
                $this->db->bind(':act',1);
                $this->db->bind(':group',$data['id']);
                $this->db->bind(':cong',$_SESSION['congId']);
                $this->db->execute();
                $secretaryid = $this->db->dbh->lastInsertId();

                if($this->RightsSet(3) > 0){
                    $this->db->query('CALL sp_role_pairing(:user1,:role)');
                    $this->db->bind(':user1',$secretaryid);
                    $this->db->bind(':role',3);
                    $this->db->execute();
                }
            }else{
                $users = loadresultset($this->db->dbh,'SELECT ID FROM tblusers WHERE (GroupId=?)',[$data['id']]);
                $this->db->query('UPDATE tblusers SET UserID=:usid WHERE(ID=:id)');
                $this->db->bind(':usid',$data['chairuserid']);
                $this->db->bind(':id',$users[0]->ID);
                $this->db->execute();

                $this->db->query('UPDATE tblusers SET UserID=:usid WHERE(ID=:id)');
                $this->db->bind(':usid',$data['treasureruserid']);
                $this->db->bind(':id',$users[1]->ID);
                $this->db->execute();

                $this->db->query('UPDATE tblusers SET UserID=:usid WHERE(ID=:id)');
                $this->db->bind(':usid',$data['secretaryuserid']);
                $this->db->bind(':id',$users[0]->ID);
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

    public function createupdate($data)
    {
        if(!$data['isedit']){
            return $this->create($data);
        }
        return $this->update($data);
    }
    public function delete($data)
    {
        try {
           
            $this->db->dbh->beginTransaction();

            $this->db->query('UPDATE tblgroups SET deleted=:del WHERE (ID=:id)');
            $this->db->bind(':del',$_SESSION['one']);
            $this->db->bind(':id',$data['id']);
            $this->db->execute();

            $this->db->query('DELETE FROM tblusers WHERE (GroupId=:gid)');
            $this->db->bind(':gid',$data['id']);
            $this->db->execute();

            if ($this->db->dbh->commit())
            {
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

    public function AddMembership($data)
    {
        try {
            $this->db->dbh->beginTransaction();

            foreach($data['members'] as $member)
            {
                $this->db->query('INSERT INTO tblgroupmembership (memberId,groupId) VALUES(:member,:group)');
                $this->db->bind(':member',$member);
                $this->db->bind(':group',$data['group']);
                $this->db->execute();
            }

            if ($this->db->dbh->commit())
            {
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

    public function DeleteMembership($id)
    {
        $this->db->query('DELETE FROM tblgroupmembership WHERE (ID=:id)');
        $this->db->bind(':id',$id);
        if(!$this->db->execute()){
            return false;
        }

        return true;
    }
}