<?php
//数据采集，doGET,doPOST
namespace Common\Lib;
class HttpRequest
{//类定义开始
	//通过get方式获取数据
	public function doGet($url,$header,$timeout=5)
	{
		$code=self::getSupport();
		switch($code)
		{
			case 1:return self::curlGet($url,$header,$timeout);break;
			default:return false;
		}
	}
	//通过POST方式发送数据
	 public function doPost($url,$header,$data,$timeout=5)
	{
		$code=self::getSupport();
		switch($code)
		{
			case 1:return self::curlPost($url,$header,$data,$timeout);break;
			default:return false;
		}
	}

	//获取支持读取远程文件的方式
	 public function getSupport()
	{
		if(function_exists('curl_init'))//curl方式
		{
			return 1;
		}
		else if(function_exists('fsockopen'))//socket
		{
			return 2;
		}
		else if(function_exists('file_get_contents'))//php系统函数file_get_contents
		{
			return 3;
		}
		else if(ini_get('allow_url_fopen')&&function_exists('fopen'))//php系统函数fopen
		{
			return 4;
		}
		else
		{
			return 0;
		}
	}
 private function curlGet($url,$header,$timeout){
        $ch =curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($ch, CURLOPT_GET, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $output = curl_exec($ch);
        if( $output == FALSE){
            die(curl_error($ch));
            return False;
        }
       // $data_output = json_decode($output);
        curl_close($ch);
        return $output;
    }
 private function curlPost($url,$header,$data,$timeout){
        $ch =curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        $output = curl_exec($ch);
        if($output==""){
             curl_close($ch);
             return True;
        }
        if( $output == FALSE){
            die(curl_error($ch));   
            return False;
        }
        
        //$data_output = json_decode($output);
        curl_close($ch);
        return $output;

    }

}//类定义结束
?>
