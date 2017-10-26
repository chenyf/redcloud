<?php
namespace Common\Model\User;


class IFaceResultModel
{
    public $code = -1;

    public $msg = 'error';

    public function __construct($_code,$_msg){
        $this->code = $_code;
        $this->msg = $_msg;
    }
}