<?php
namespace Common\Lib;
/*-formats	输出所有可用格式
-f fmt	指定格式(音频或视频格式)
-i filename	指定输入文件名，在linux下当然也能指定:0.0(屏幕录制)或摄像头
-y	覆盖已有文件
-t duration	记录时长为t
-fs limit_size	设置文件大小上限
-ss time_off	从指定的时间(s)开始， [-]hh:mm:ss[.xxx]的格式也支持
-itsoffset time_off	设置时间偏移(s)，该选项影响所有后面的输入文件。该偏移被加到输入文件的时戳，定义一个正偏移意味着相应的流被延迟了 offset秒。 [-]hh:mm:ss[.xxx]的格式也支持
-title string	标题
-timestamp time	时间戳
-author string	作者
-copyright string	版权信息
-comment string	评论
-album string	album名
-v verbose	与log相关的
-target type	设置目标文件类型("vcd", "svcd", "dvd", "dv", "dv50", "pal-vcd", "ntsc-svcd", ...)
-dframes number	设置要记录的帧数
视频选项:
-b	指定比特率(bits/s)，似乎ffmpeg是自动VBR的，指定了就大概是平均比特率
-vb	指定视频比特率(bits/s)
-vframes number	设置转换多少桢(frame)的视频
-r rate	桢速率(fps)
-s size	分辨率
-aspect aspect	设置视频长宽比(4:3, 16:9 or 1.3333, 1.7777)

-cropbottom size	设置底部切除尺寸(in pixels)
-cropleft size	设置左切除尺寸 (in pixels)
-cropright size	设置右切除尺寸 (in pixels)
-padtop size	设置顶部补齐尺寸(in pixels)
-padbottom size	底补齐(in pixels)
-padleft size	左补齐(in pixels)
-padright size	右补齐(in pixels)
-padcolor color	补齐带颜色(000000-FFFFFF)
-vn	取消视频
-vcodec codec	强制使用codec编解码方式('copy' to copy stream)
-sameq	使用同样视频质量作为源（VBR）
-pass n	选择处理遍数（1或者2）。两遍编码非常有用。第一遍生成统计信息，第二遍生成精确的请求的码率
-passlogfile file	选择两遍的纪录文件名为file
-newvideo	在现在的视频流后面加入新的视频流
高级视频选项
-pix_fmt format	set pixel format, 'list' as argument shows all the pixel formats supported
-intra	仅适用帧内编码
-qscale q	以<数值>质量为基础的VBR，取值0.01-255，约小质量越好
-loop_input	设置输入流的循环数(目前只对图像有效)
-loop_output	设置输出视频的循环数，比如输出gif时设为0表示无限循环
-g int	设置图像组大小
-cutoff int	设置截止频率
-qmin int	设定最小质量
-qmax int	设定最大质量
-qdiff int	量化标度间最大偏差 (VBR)
-bf int	使用frames B 帧，支持mpeg1,mpeg2,mpeg4
音频选项:
-ab	设置比特率(单位：bit/s，也许老版是kb/s)
-aframes number	设置转换多少桢(frame)的音频
-aq quality	设置音频质量 (指定编码)
-ar rate	设置音频采样率 (单位：Hz)
-ac channels	设置声道数
-an	取消音频
-acodec codec	指定音频编码('copy' to copy stream)
-vol volume	设置录制音量大小(默认为256)
-newaudio	在现在的音频流后面加入新的音频流
字幕选项:
-sn	取消字幕
-scodec codec	设置字幕编码('copy' to copy stream)
-newsubtitle	在当前字幕后新增
-slang code	设置字幕所用的ISO 639编码(3个字母)
Audio/Video 抓取选项:
-vc channel	设置视频捕获通道(只对DV1394)
-tvstd standard	设置电视标准 NTSC PAL(SECAM)*/
class Ffmpeg{
	private $bin = 'ffmpeg';
	public $err = '';
	function __construct() {
		$this->whereis ();
	}
	public function whereis() {
		$cmd = "whereis ffmpeg";
		$rs = $this->shell ( $cmd );
		if (! $rs [1]) {
			return false;
		} else {
			$output = $rs [0];
			$path = explode ( ' ', $output [0] );
			if (isset ( $path [1] ) && $path) {
				$this->bin = $path [1];
				return true;
				;
			}
			$this->err = '此命令未找到';
			return false;
		}
	}
	public function info($file) {
		if (! file_exists ( $file )) {
			$this->err = $file . '文件不存在';
			return false;
		}
		$cmd = sprintf ( "%s -i %s", $this->bin, escapeshellarg ( $file ) );
		$rs = $this->shell ( $cmd );
		if ($rs [0]) {
			$info = implode ( "\n", $rs [0] );
			if (preg_match ( "/Invalid data/i", $info )) {
				$this->err = '错误的文件格式';
				return false;
			}
			preg_match ( "/creation_time\s+:\s+(.*?)\n/", $info, $creation_time );
			preg_match ( "/Duration:(.*?),/", $info, $duration );
			preg_match ( "/bitrate:(.*kb\/s)/", $info, $bitrate );
			preg_match ( "/Stream.*?:\s+Video:(.*?)\n/", $info, $video );
			preg_match ( "/Stream.*?:\s+Audio:(.*?)\n/", $info, $audio );
			$return = array ();
			if ($creation_time && isset ( $creation_time [1] )) {
				$return ['creation_time'] = $creation_time [1];
			}
			if ($duration && isset ( $duration [1] )) {
				$return ['duration'] = $duration [1];
			}
			if ($bitrate && isset ( $bitrate [1] )) {
				$return ['bitrate'] = $bitrate [1];
			}
			if ($video && isset ( $video [1] )) {
				$vinfo = array();
				preg_match ( "/(\d+)x(\d+)/", $video[1],$vsize );
				$vinfo['width'] = isset($vsize[1])? $vsize[1] :0;
				$vinfo['height'] = isset($vsize[2])? $vsize[2] :0;
				preg_match ( "/DAR\s+(\d+:\d+)/", $video[1],$vdar );
				preg_match ( "/PAR\s+(\d+:\d+)/", $video[1],$vpar );
				$vinfo['dar'] = isset($vdar[1])? $vdar[1] :'';
				$vinfo['par'] = isset($vpar[1])? $vpar[1] :'';
				preg_match ( "/(\d+)\s+fps/", $video[1],$fps );
				preg_match ( "/(\d+)\s+tbr/", $video[1],$tbr );
				preg_match ( "/(\d+)\s+tbn/", $video[1],$tbn );
				preg_match ( "/(\d+)\s+tbc/", $video[1],$tbc );
				$vinfo['fps'] = isset($fps[1])? $fps[1] :'';
				$vinfo['tbr'] = isset($tbr[1])? $tbr[1] :'';
				$vinfo['tbn'] = isset($tbn[1])? $tbn[1] :'';
				$vinfo['tbc'] = isset($tbc[1])? $tbc[1] :'';
				$return ['video']['desc'] = <<<EOF
				25 tbr代表帧率；1200k tbn代表文件层（st）的时间精度，即1S=1200k，和duration相关；
				50 tbc代表视频层（st->codec）的时间精度，即1S=50，和strem->duration和时间戳相关
				SAR，Sample Aspect Ratio 采样纵横比。即视频横向对应的像素个数比上视频纵向的像素个数。即为我们通常提到的分辨率。 
				PAR，Pixel Aspect Ratio 像素宽高比。如果把像素想象成一个长方形，
				PAR即为这个长方形的长与宽的比。当长宽比为1时，这时的像素我们成为方形像素。 
				DAR，Display Aspect Ratio 显示宽高比。即最终播放出来的画面的宽与高之比。 
				公式 SAR x PAR = DAR
EOF;
				$return ['video']['raw'] = $video [1];
				$return ['video']['info'] = $vinfo;
			}
			if ($audio && isset ( $audio [1] )) {
				$return ['audio']['raw'] = $audio [1];
				$ainfo  = explode(',', $audio [1]);
				$return ['audio']['info']['codec'] = $ainfo[0];
				$return ['audio']['info']['sample_rate'] = $ainfo[1];
				$return ['audio']['info']['channels'] = $ainfo[2];
				//$return ['audio']['info']['channels'] = $ainfo[3];
				$return ['audio']['info']['bitrate'] = $ainfo[4];
			}
			return $return;
		}
		return false;
	}
	public function wavToamr($infile,$outfile,$option=array()){
		if (!file_exists($infile)) {
			$this->err = $infile . '文件不存在';
			return false;
		}
		if (!file_exists(dirname($outfile)) || !is_writable(dirname($outfile))) {
			if (!mk_dir(dirname($outfile))) {
                $this->err=$outfile ."目录创建失败";
				return false;
            }
			//$this->err =  '目录不存在或者不可写';
			
		}
		$default_option = array('bitrate'=>'12.2k','sample_rate'=>8000,'channels'=>1);
		$opt = array_merge($default_option,$option);
		$cmd = sprintf("%s -i %s -acodec libamr_nb -ar %s -ab %s -ac %s -y %s",$this->bin
				,escapeshellarg($infile),$opt['sample_rate'],$opt['bitrate'],$opt['channels']
				,escapeshellarg($outfile));
		$rs = $this->shell($cmd);
		if (!$rs[1]) {
			$this->err = $cmd.' 转码失败 '.array_pop($rs[0])."此方法需要用到AMR编码器请确认是否安装正确，下载OpenCORE AMR：http://sourceforge.net/projects/opencore-amr/";
			return false;
		}
		return true;
	}
        
        /**
         * wav跟amr 相互转换
         * @param string $infile
         * @param string $outfile
         * @param type $option
         * @return boolean
         * @author LiangFuJian
         */
        public function amrOrwav($infile,$outfile,$option = array()){
            
		if (!file_exists($infile)) {
			$this->err = $infile . '文件不存在';
			return false;
		}
		if (!file_exists(dirname($outfile)) || !is_writable(dirname($outfile))) {
			if (!mk_dir(dirname($outfile))) {
                $this->err=$outfile ."目录创建失败";
				return false;
            }
			//$this->err = $outfile . '目录不存在或者不可写';
			//return false;
		}
                $default_option = array('bitrate'=>'12.2k','sample_rate'=>8000,'channels'=>1);
		$opt = array_merge($default_option,$option);
		$cmd = sprintf("ffmpeg -i %s -ar %s -ab %s -ac %s %s",escapeshellarg($infile),
                        $opt['sample_rate'],$opt['bitrate'],$opt['channels'],escapeshellarg($outfile));
		$rs = $this->shell($cmd);
		if (!$rs[1]) {
			$this->err = $cmd.' 转码失败 '.array_pop($rs[0]);
			return false;
		}
		return true;
	}
        
	public function wavTomp3($infile,$outfile,$option=array()){
		if (!file_exists($infile)) {
			$this->err = $infile . '文件不存在';
			return false;
		}
		if (!file_exists(dirname($outfile)) || !is_writable(dirname($outfile))) {
			$this->err = $outfile . '目录不存在或者不可写';
			return false;
		}
		$default_option = array('bitrate'=>'12.2k','sample_rate'=>8000,'channels'=>1);
		$opt = array_merge($default_option,$option);
		$cmd = sprintf("%s -i %s -acodec libmp3lame -ar %s -ab %s -ac %s -y %s",$this->bin
				,escapeshellarg($infile),$opt['sample_rate'],$opt['bitrate'],$opt['channels']
				,escapeshellarg($outfile));
		$rs = $this->shell($cmd);
		var_dump($rs);
		if (!$rs[1]) {
			$this->err = $cmd." 转码失败 ".array_pop($rs[0]);
			return false;
		}
		return true;
	}
        /**
         * 声音转码
         * @author 钱志伟 2014-08-12
         * @link http://keren.iteye.com/blog/1773515
         */
        public function audioConvert($paramArr=array()){
            $options = array(
                'srcAudio'      => '',
                'targetAudio'   => '',
                'type'          => '',    #amr、caf等
            );
            $options = array_merge($options, $paramArr);
            extract($options);
            
            if (!file_exists($srcAudio)) {
                    $this->err = $srcAudio . '文件不存在';
                    return false;
            }
            if (!file_exists(dirname($targetAudio)) || !is_writable(dirname($targetAudio))) {
                    $this->err = $targetAudio . '目录不存在或者不可写';
                    return false;
            }
            $cmd = sprintf("ffmpeg -i %s %s",escapeshellarg($srcAudio), escapeshellarg($targetAudio));
            $rs = $this->shell($cmd);
            if (!$rs[1]) {
                    $this->err = $cmd.' 转码失败 '.array_pop($rs[0]);
                    return false;
            }
            return true;
        }
        
	//默认使用源文件编码进行转码，如果转码失败 可尝试设置 $option=array() 使用libx264转码
	public function mp4Tom3u8($infile,$outfile_list,$outfile_ts_prefix,$option=array('vcodec'=>'copy')){
		if (!file_exists($infile)) {
			$this->err = $infile . '文件不存在';
			return false;
		}
		if (!file_exists(dirname($outfile_list)) || !is_writable(dirname($outfile_list))) {
			$this->err = $outfile_list . '目录不存在或者不可写';
			return false;
		}
		if (!file_exists(dirname($outfile_ts_prefix)) || !is_writable(dirname($outfile_ts_prefix))) {
			$this->err = $outfile_ts_prefix . '目录不存在或者不可写';
			return false;
		}
		$default_option = array('segment_time'=>10,'vcodec'=>'libx264');
		$opt = array_merge($default_option,$option);
		
		$cmd = sprintf("%s -y -i %s 
				-c:a copy -absf aac_adtstoasc -c:v %s -vbsf h264_mp4toannexb 
				-flags -global_header -map 0 -f segment -segment_time %d 
				-segment_list %s 
				-segment_format mpegts %s",$this->bin
				,escapeshellarg($infile),$opt['vcodec'],$opt['segment_time']
				,escapeshellarg($outfile_list)
				,escapeshellarg($outfile_ts_prefix.'%4d'.substr(md5($infile), 0,5).'.ts'));
		$rs = $this->shell($cmd);
		if (!$rs[1]) {
			$this->err = $cmd." 转码失败 ".array_pop($rs[0]);
			return false;
		}
		return true;
	}
	private function shell($cmd) {
		unset ( $rsarr );
		unset ( $rs );
		@exec ( $cmd . ' 2>&1', $rsarr, $rs );
		$flag = true;
		if ($rs !== 0) {
			$this->err = $cmd . '  执行命令失败';
			$flag = false;
		}
		return array (
				$rsarr,
				$flag 
		);
	}
	public function test() {
		header("Content-type: text/html; charset=utf-8");
		echo '<pre>';
		//$this->info ( SITE_PATH . '/data/PHP.mp4' );
		//$this->info ( SITE_PATH . '/data/uploads/audio/1403599638.wav' );
		//$this->wavTomp3(SITE_PATH . '/data/uploads/audio/1403599638.wav',SITE_PATH . '/data/uploads/audio/1403599638.wav.mp3' );
		$this->mp4Tom3u8(SITE_PATH . '/data/PHP.mp4',SITE_PATH . '/data/PHP.mp4.m3u8',SITE_PATH . '/data/PHP123' );
		echo $this->err;
		echo '</pre>';
	}
}

?>