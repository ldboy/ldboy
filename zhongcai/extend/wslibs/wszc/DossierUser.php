<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/4/27
 * Time: ����11:23
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
    private $job;
    private $address='';
    private $nation='';
    private $minzu='';
    private $f_uid = 0;
    private $f_user;
    private $email='';

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

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function setNation($nation)
    {
        $this->nation = $nation;
        return $this;
    }

    public function setJob($job)
    {
        $this->job = $job;
        return $this;
    }


    public function setFaRen(DossierUser $user)
    {
        $this->f_user = $user;
        return $this;
    }

    public function getInsertArray()
    {
        return ["role" => $this->role, "type" => $this->type, "name" => $this->name_or_orgname, "id_type" => $this->id_type, "id_num" => $this->idcard_or_creditno, "phone" => $this->phone, "f_uid" => $this->f_uid,"address"=>$this->address];
    }


    public function setMinZu($minzu)
    {
        $this->minzu = $minzu;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
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


        if (!($has = Db::name("dossier_users")->where(array("role" => $this->role, "id_num" => "$this->idcard_or_creditno", "dossier_id" => $id))->find())) {

            $data = $this->getInsertArray();
            $data['dossier_id'] = $id;
            $data['email'] = $this->email;
            $data['nation'] = $this->nation;
            $data['job'] = $this->job;

            if($this->type!=2){
                $idid = Db::name("idcards")->where(["id_card "=>$this->idcard_or_creditno])->value("id");


                if($idid){
                    $f_idid = $idid;
                }else{

                    $f_idid = User::addIdCard($this->idcard_or_creditno,$this->name_or_orgname);
                }

                $data['idid'] = $f_idid;


            }

            if($this->role==2){
                $doss = Db::name('dossier_users')->where("dossier_id",$id)->order('id desc')->find();
                $data['r_no'] = $doss['r_no']+1;
            }












            


            $insertid = Db::name("dossier_users")->insertGetId($data);
            /*if ($this->type != Constant::D_Type_JiGou) {
                echo Db::name("dossier_users")->getLastSql();
                die;
            }*/

            if (!$insertid) return 0;
            if ($this->type == Constant::D_Type_JiGou) {


                $idid = Db::name("idcards")->where(["id_card "=>$this->f_user->idcard_or_creditno])->value("id");


                if($idid){
                    $f_idid = $idid;
                }else{

                    $f_idid = User::addIdCard($this->f_user->idcard_or_creditno,$this->f_user->name_or_orgname);
                }


                $fid = $this->f_user->pushToDossier($id);
                //dump($this->f_user->idcard_or_creditno);

                Drole::addRole($id,LoginUser::getRoleThIdId(),$this->name_or_orgname,$this->role,$insertid,0);
 
                /*dump($insertid);die;*/
                Db::name("dossier_users")->where("id", $insertid)->update(array("f_uid" => $fid,'idid'=>$f_idid));
                Db::name("dossier_roles")->where("idid",$f_idid)->where("dossier_id",$id)->update(array("role_pid" => $insertid));

                return $insertid;
            } else {


                Drole::addRole($id,$data['idid'],$this->name_or_orgname,$this->role,$insertid,0);
                return $insertid;
            }
        }


        return $has['id'];


    }

    private function insertCheck()
    {
        $array = $this->getInsertArray();
        //dump($this->getInsertArray());die;

        unset($array['f_uid']);

//        dump($array);exit;

        foreach ($array as $value) {
            if (!$value) return false;
        }
        /*if ($this->type == Constant::D_Type_JiGou) {
            if (!$this->f_user) {
                return false;
            }

        }*/
        return true;
    }


}