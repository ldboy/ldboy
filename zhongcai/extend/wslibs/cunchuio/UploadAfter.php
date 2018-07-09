<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/10
 * Time: ÏÂÎç4:03
 */

namespace wslibs\cunchuio;


use think\Model;

class UploadAfter
{
  public function run(Model $attachment)
  {

 
     if( CunChuIO::uploadImageFile($url=ltrim($attachment->getData("url"),"/"), ROOT_PATH."public".$attachment->getData("url")))
     {
         $attachment->save(array("storage"=>"azure"),array("id"=>$attachment->getLastInsID()));
     }

  }
}