<?php
/**
 * 图片尺寸转化
 * @author 钱志伟 2013-11-13
 * @link http://www.oschina.net/code/snippet_157781_11058
 */
$convert = "/usr/bin/convert";
$tlc_data_dir = "";
if(defined("PICTURE_PARENT_PATH")){
    $tlc_data_dir = PICTURE_PARENT_PATH;
}else{
    if(file_exists('./env.php')){
        $local_env = include('./env.php');
        if(!empty($local_env['PICTURE_PARENT_PATH'])){
            $tlc_data_dir = rtrim($local_env['PICTURE_PARENT_PATH'],DIRECTORY_SEPARATOR);
        }
    }
}

if(!file_exists($convert)) $convert = "/usr/local/bin/convert";
if(!file_exists($convert)) $convert = "convert";
$cmdFmt = "{$convert} -quality 85 -strip -resize %dx%d %s %s";

$file = $_SERVER ['REQUEST_URI'];//请求字串 /file/abc.jpg.w320.jpg

if($loc=strpos($file, '?')) $file = substr($file, 0, $loc);

$desfile = $_SERVER ['DOCUMENT_ROOT'] . $file; //目标目标路径 /var/www/http/file/abc.jpg.w320.jpg

if(!is_file($desfile)){
    $desfile = $tlc_data_dir . DIRECTORY_SEPARATOR . ltrim($file,DIRECTORY_SEPARATOR);
//    echo $desfile;
    outImg($desfile);
}

$dirname = dirname ( $desfile ) . "/";
$filename = basename ( $desfile );

$r = preg_match("/(.+\.(png|jpg|jpeg|gif))((\.[a-zA-Z0-9_]*)+)\.(jpg)$/i", $filename, $m);
if($r){
    $format = $m[3];
    $srcfile = $dirname . $m [1];

    $tmp = '/tmp/tmp'.time().'_'.rand(0, 1000).'.jpg';
    #尺寸
    if(preg_match('/\.w(\d+)/', $format, $mw)){

        if(!file_exists($srcfile)) die("NOSRCPIC");
        $width = $mw[1];
        $srcSize = @getimagesize($srcfile);
        $allow = isset($srcSize[0]) && $srcSize[0]>=$width;
        $checkW = in_array($width, array(20, 24, 35, 46, 36, 38,50,60, 62, 64, 90, 94, 180, 200, 373,210, 400, 1000)) || 1;
	if ($allow && $checkW && file_exists($srcfile)) {//而且文件不存在
            if(isset($_SERVER['OS']) && stripos($_SERVER['OS'], 'Windows_NT')!==false){
                thumbnail ( $srcfile, $tmp, $width );
            } else {
                $cmd = sprintf($cmdFmt, $width, 2000, $srcfile, $tmp);
                exec($cmd);
            }
            $srcfile = $tmp;
        }
    }
    #灰
    if(preg_match('/\.(grey)\.?/', $format, $mg)){
        if(stripos($_SERVER['OS'], 'Windows_NT')!==false){
        }else{
            $cmd = "{$convert} -colorspace Gray {$srcfile} {$tmp}";
//            echo $cmd;exit;
            exec($cmd);
        }
    }
    if(file_exists($tmp)){
        $content = file_get_contents($tmp);
        file_put_contents($desfile, $content);
        @unlink($tmp);
        outImg($desfile);
    }
    
}else{
    echo "no picture!!!";exit;
}

if(preg_match("/(.+\.(png|jpg|jpeg|gif))\.w([\d]+)\.(jpg)/i", $filename, $m)) {
	$srcfile = $dirname . $m [1];
        if(!file_exists($srcfile)) die("NOSRCPIC");
	$width = $m[3];	//匹配出输出文件宽度
        $srcSize = @getimagesize($srcfile);
        $allow = isset($srcSize[0]) && $srcSize[0]>=$width;
	if ($allow && in_array($width, array(20, 24, 46, 36, 38, 60, 62, 64, 90, 94, 180, 200, 373,210, 400, 1000)) && file_exists($srcfile)) {//而且文件不存在
		if(stripos($_SERVER['OS'], 'Windows_NT')!==false){
			thumbnail ( $srcfile, $desfile, $width );
		} else {
                        $cmd = sprintf($cmdFmt, $width, 2000, $srcfile, $desfile);
			exec($cmd);
			outImg($desfile);
		}	
	}
	outImg($srcfile);
}

/**
 * 显示图片
 */
function outImg($imgfile){
    if(file_exists($imgfile)){
        header ( 'content-type:image/jpg' );
        echo file_get_contents($imgfile);
    }
    exit;
}

/**
 * 生成缩略图
 *
 * @param 源 $src        	
 * @param 缩放后的宽带 $width        	
 *
 */
function thumbnail($src, $des, $width) {
	ob_start ();//开始截获输出流
	$imageinfos = getimagesize ( $src );
	$ext = strtolower ( pathinfo ( $src, 4 ) );
	if ($imageinfos [2] == 1) {
		$im = imagecreatefromgif ( $src );
	} elseif ($imageinfos [2] == 2) {
		$im = imagecreatefromjpeg ( $src );
	} elseif ($imageinfos [2] == 3) {
		$im = imagecreatefrompng ( $src );
	}
	
	if (isset ( $im )) {
		$height = $imageinfos [1] * $width / $imageinfos [0];
		$dst_img = ImageCreateTrueColor ( $width, $height );
		
		imagesavealpha ( $dst_img, true );
		$trans_colour = imagecolorallocatealpha ( $dst_img, 0, 0, 0, 127 );
		imagefill ( $dst_img, 0, 0, $trans_colour );
		
		imagecopyresampled ( $dst_img, $im, 0, 0, 0, 0, $width, $height, $imageinfos [0], $imageinfos [1] );
		
		header ( 'content-type:image/jpg' );
		imagejpeg ( $dst_img, null, 90 );//输出文件流，90--压缩质量，100表示最高质量。
		
		@imagedestroy ( $im );
		@imagedestroy ( $dst_img );
	} else {
		echo @file_get_contents ( $src );
	}
	$content = ob_get_contents ();//获取输出流
	ob_end_flush ();//输出流到网页,保证第一次请求也有图片数据放回
	@file_put_contents ( $des, $content );//保存文件
}
