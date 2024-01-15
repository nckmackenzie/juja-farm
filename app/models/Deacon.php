<?php

class Deacon
{
    private $db;
    public function __construct()
    {
        $this->db = new Database;
    }

    public function GetDeacons()
    {
        $sql = 'SELECT 
                    d.ID,
                    ucase(m.memberName) as DeaconName,
                    ucase(t.districtName) as DistrictName,
                    ucase(y.yearName) as YearName,
                    ucase(d.Zone) as Zone
                FROM `tbldeacons` d join tblmember m on d.DeaconId = m.ID join tbldistricts t on d.DistrictId = t.ID
                    join tblfiscalyears y on d.YearId = y.ID
                WHERE
                    d.CongregationId=?';
        return loadresultset($this->db->dbh,$sql,[(int)$_SESSION['congId']]);
    }

    public function GetMembersByDistrict($district)
    {
        $sql = 'SELECT ID,UCASE(memberName) as memberName FROM tblmember WHERE (districtId=?) AND (memberStatus = 1) ORDER BY memberName';
        return loadresultset($this->db->dbh,$sql,[$district]);
    }

    public function RoleIsSet($role,$year,$district,$id)
    {
        if($role === 'none') return false;

        $exists = getdbvalue($this->db->dbh,'SELECT COUNT(*) FROM tbldeacons 
                                             WHERE (Role=?) AND (YearId=?) AND (DistrictId=?) AND (ID <> ?)',[$role, $year,$district,$id]);
        if((int)$exists > 0) return true;
        return false;
    }

    public function CreateUpdate($data)
    {
       if(!$data['isedit'])
       {
            $this->db->query('INSERT INTO tbldeacons (DeaconId,DistrictId,YearId,Zone,Role,CongregationId) 
                              VALUES(:deacon,:did,:yid,:zone,:role,:cid)');
            $this->db->bind(':deacon',$data['deacon']);
            $this->db->bind(':did',$data['district']);
            $this->db->bind(':yid',$data['year']);
            $this->db->bind(':zone',$data['zone']);
            $this->db->bind(':role',$data['role']);
            $this->db->bind(':cid',$_SESSION['congId']);
            if(!$this->db->execute())
            {
                return false;
            }
            return true;
       }
       else
       {
            $this->db->query('UPDATE tbldeacons SET DeaconId=:deacon,DistrictId=:did,YearId=:yid,Zone=:zone,Role=:role 
                              WHERE (ID=:id)');
            $this->db->bind(':deacon',$data['deacon']);
            $this->db->bind(':did',$data['district']);
            $this->db->bind(':yid',$data['year']);
            $this->db->bind(':zone',$data['zone']);
            $this->db->bind(':role',$data['role']);
            $this->db->bind(':id',$data['id']);
            if(!$this->db->execute())
            {
            return false;
            }
            return true;
       }
    }

    public function GetDetails($id)
    {
        $this->db->query('SELECT * FROM tbldeacons WHERE ID=:id');
        $this->db->bind(':id',$id);
        return $this->db->single();
    }

    public function Delete($id)
    {
        $this->db->query('DELETE FROM tbldeacons WHERE (ID=:id)');
        $this->db->bind(':id',$id);
        if(!$this->db->execute())
        {
            return false;
        }
            return true;
    }
}