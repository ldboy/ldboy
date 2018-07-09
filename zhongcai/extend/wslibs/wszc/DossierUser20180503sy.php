<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/4/27
 * Time: ÉÏÎç11:23
 */

namespace wslibs\wszc;


use think\Db;

class DossierUser
{



    private $type = 1;
    private $role;
    private $name_or_orgname = "";
    private $idcard_or_creditno = 0;
    private $id_type = 1;
    private $phone;
    private $f_uid = 0;
    private $f_user;

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
        if ($this->type == 2) {
            $this->id_type = 2;
        } else {
            $this->id_type = 1;
        }
        return $this;
    }


    /**
     * @param string $name_or_orgname
     */
    public function setNameOrOrgname($name_or_orgname)
    {
        $this->name_or_orgname = $name_or_orgname;
        return $this;
    }


    /**
     * @param int $idcard_or_creditno
     */
    public function setIdcardOrCreditno($idcard_or_creditno)
    {
        $this->idcard_or_creditno = $idcard_or_creditno;
        return $this;
    }


    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }


    public function setFaRen(DossierUser $user)
    {
        $this->f_user = $user;
        return $this;
    }

    public function getInsertArray()
    {
        return ["role" => $this->role, "type" => $this->type, "name" => $this->name_or_orgname, "id_type" => $this->id_type, "id_num" => $this->idcard_or_creditno, "phone" => $this->phone, "f_uid" => $this->f_uid];
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    public function pushToDossier($id)
    {


        if (!$this->insertCheck()) {
            return 0;
        }
        if ($has = Db::name("dossier_users")->where(array("role" => $this->role, "id_num" => $this->idcard_or_creditno))->find()) {
            $data = $this->getInsertArray();
            $data['dossier_id'] = $id;
            $insertid = Db::name("dossier_users")->insertGetId($data);
            if (!$insertid) return 0;
            if ($this->type == Constant::D_Type_JiGou) {
                $fid = $this->f_user->pushToDossier($id);
                Db::name("dossier_users")->where("id", $insertid)->update(array("f_uid" => $fid));
                return $insertid;
            }
        }
        return $has['id'];


    }

    private function insertCheck()
    {
        $array = $this->getInsertArray();

        unset($array['f_uid']);
        foreach ($array as $value) {
            if (!$value) return false;
        }
        if ($this->type == Constant::D_Type_JiGou) {
            if (!$this->f_user) {
                return false;
            }

        }
        return true;
    }


}