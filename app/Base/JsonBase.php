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

    public function addExtraField($signerKey, $type, $opt, $value, $left, $top, $width, $height, $page, $style=''){
        $this->data['extrafields'][] = [
            'key' => $signerKey, // signer id, eg signer 1
            'type' => $type,     //sign (xs, sm, md, lg, xl), in, input, c (checkbox), c1(checkbox group 1), c1, date, name
            'opt' => $opt,       // "?" or nothing. Only work with input fields to make them optional
            'value' => $value,   // input or checkbox, use "x" to set a checkbox checked
            'left' => $left,     // location in points. A4 =  595 wide × 842 high points. top left is (0,0)
            'top' => $top,
            'width' => $width,
            'height' => $height,  // 10 is a good height for an input, not needed for signing fields
            'page' => $page,      // page number - first page is page 1
            'style'=>$style,      // css type styles eg color:red;
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
   public function getCurlForPdfUploadLong($filename,$pdf='goodsign_guide.pdf'){
        return "curl --url ".$this->getBasePath()."/api/uploadpdf \
--header 'authorization: Bearer ".$this->getApiKey()."' \
-F 'file=@./$pdf' \
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
