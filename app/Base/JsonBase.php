<?php

namespace App\Base;

use Illuminate\Support\Facades\File;

class JsonBase extends BaseCommand
{
    private $data;

    public function __construct($uuid){
        $this->data = [];
        $this->data['uuid'] = $uuid;
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

}
