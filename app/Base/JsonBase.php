<?php

namespace App\Base;

use Illuminate\Support\Facades\File;

class JsonBase extends BaseCommand
{
    private $data;

    public function __construct($uuid=''){
        $this->data = [];
        if(!empty($uuid)) $this->data['uuid'] = $uuid; // not all requests need a UUID, eg post a PDF
        $this->signers = [];
    }
    public function add($key,$value){
        $this->data[$key] = $value;
        return $this;
    }

    public function addSigner($key, $name, $email){
        $this->data['signers'][] = [
            'key' => $key,
            'name' => $name,
            'email' => $email,
        ];
        return $this;
    }

    public function getJsonString(){
        return json_encode($this->data);
    }

    public function getCurlForTemplate($filename){
        return "curl --url ".$this->getBasePath()."/api/usetemplate \
--header 'authorization: Bearer ".$this->getApiKey()."' \
--data @".$filename;
    }

    public function getCurlForTemplateLong($filename){
        return "curl --url ".$this->getBasePath()."/api/usetemplate \
--header 'authorization: Bearer ".$this->getApiKey()."' \
--data '".File::get(getcwd() .'/'. $filename)."'";
    }

    public function getCurlForPdfUpload($filename){
        return "curl --url ".$this->getBasePath()."/api/uploadpdf \
--header 'authorization: Bearer ".$this->getApiKey()."' \
-F 'file=@./goodsign_guide.pdf' \
-F 'payload=@./$filename'";
    }
   public function getCurlForPdfUploadLong($filename){
        return "curl --url ".$this->getBasePath()."/api/uploadpdf \
--header 'authorization: Bearer ".$this->getApiKey()."' \
-F 'file=@./goodsign_guide.pdf' \
-F 'payload=".File::get(getcwd() .'/'. $filename)."'";
    }

    public function getCurlForReminder($uuid, $email){
        return "curl --url ".$this->getBasePath()."/api/document/".$uuid."/remind \
--header 'authorization: Bearer ".$this->getApiKey()."' \
-F 'signer_email=".$email."'";
    }


    public function getCurlForVoid($uuid, $notify, $msg){
        $curl =  "curl --url ".$this->getBasePath()."/api/document/".$uuid."/void \
--header 'authorization: Bearer ".$this->getApiKey() ."'";
        if($notify){
            $curl .= "\
            -F 'slient=true'";
        }
        if($msg!=''){
            $curl .= "\
            -F 'msg=".$msg."'";
        }
        return $curl;
    }



}
