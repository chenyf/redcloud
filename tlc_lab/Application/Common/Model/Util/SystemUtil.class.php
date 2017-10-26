<?php
namespace Common\Model\Util;

use Common\Common\ServiceKernel;
use Common\Model\Util\MySQLDumper;

class SystemUtil
{
	public static function getDownloadPath()
	{
		return getParameter('redcloud.disk.upgrade_dir');
	}

	public static function getBackUpPath()
	{
		return getParameter('redcloud.disk.backup_dir');
	}	


	public static function getCachePath(){
		$realPath =getParameter('kernel.root_dir');
		$realPath .= DIRECTORY_SEPARATOR.'Runtime';
		return 	$realPath;
	}

	public static function getSystemRootPath()
	{
		$realPath = getParameter('kernel.root_dir');
		return dirname($realPath).DIRECTORY_SEPARATOR;
	}

	public static function getUploadTmpPath()
	{
                $realPath = getParameter('redcloud.disk.backup_dir') ;
		//$realPath = SystemUtil::getSystemRootPath().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'tmp';
		return $realPath;
	}

	public static function backupdb()
	{
		$backUpdir = SystemUtil::getUploadTmpPath();
		$backUpdir .= DIRECTORY_SEPARATOR.uniqid(mt_rand()).'.txt';
		$dbSetting = array('exclude'=>array('session','cache'));
                $baseModel = new \Common\Model\Common\BaseModel();
		$dump = new MySQLDumper($baseModel->getConnection(),$dbSetting);
		return 	$dump->export($backUpdir);			
	}

}