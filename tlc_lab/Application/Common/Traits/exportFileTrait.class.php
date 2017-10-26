<?php
namespace Common\Traits;

use PHPExcel;
use PHPExcel_Writer_Excel2007;
use Symfony\Component\HttpFoundation\Response;

Trait exportFileTrait {

	/**
	 * 导出数据为Excel格式
	 * @param array  $lists     //转换的数据
	 * @param array  $set       //Excel设置
	 * @param array  $firstline //第一行的数据
	 * @param string $filename  //文件名
	 */
	public static function  exportExcel($lists, $set, $firstline, $filename = 'default.xls') {
		$objPHPExcel = new PHPExcel();
		foreach ($set as $k => $v) {
			if (empty($v) || !isset($v)) {
				$set[$k] = 'default';
			}
		}
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);//2007格式

		//设置excel的属性：
		$objPHPExcel->getProperties()->setCreator($set['author']);//创建人
		$objPHPExcel->getProperties()->setLastModifiedBy($set['author']);//最后修改人
		$objPHPExcel->getProperties()->setTitle($set['title']);//标题
		$objPHPExcel->getProperties()->setSubject($set['title']);//题目
		$objPHPExcel->getProperties()->setDescription($set['intru']);//描述
		$objPHPExcel->getProperties()->setKeywords($set['keyword']);//关键字
		$objPHPExcel->getProperties()->setCategory($set['category']);//种类
		$objPHPExcel->getActiveSheet()->setTitle($set['stitle']);//设置当前的sheet title
		//设置第一行的值：
		foreach ($firstline as $k => $v) {
			$v    = explode('|', $v);
			$v[2] = $v[2] ? $v[2] : 20;
			$objPHPExcel->getActiveSheet()->setCellValue($k . '1', $v[1]);
			$objPHPExcel->getActiveSheet()->getStyle($k . '1')->getFont()->setSize(14);//size
			$objPHPExcel->getActiveSheet()->getStyle($k . '1')->getFont()->setBold(true);//加粗
			$objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($v[2]);//宽度
		}
		//设置单元格的值
		foreach ($lists as $s => $t) {
			$s = $s + 2;
			foreach ($firstline as $k => $v) {
				$v = explode('|', $v);
				$objPHPExcel->getActiveSheet()->setCellValue($k . $s, $t[$v['0']]);
			}
		}
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-Disposition:inline;filename="' . $filename . '"');
		header("Content-Transfer-Encoding: binary");
		header("Expires: Mon, 26 Jul 2016 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");
		$objWriter->save('php://output');
		exit();
	}
        
      /**
	 * 导出数据为Excel格式
	 * @param array  $lists     //转换的数据
	 * @param array  $set       //Excel设置
	 * @param array  $firstline //第一行的数据
	 * @param string $filename  //文件名
	 */
	public static function  exportExcelOne($lists, $set, $firstline, $filename = 'default.xls') {
		$objPHPExcel = new PHPExcel();
		foreach ($set as $k => $v) {
			if (empty($v) || !isset($v)) {
				$set[$k] = 'default';
			}
		}
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);//2007格式

		//设置excel的属性：
		$objPHPExcel->getProperties()->setCreator($set['author']);//创建人
		$objPHPExcel->getProperties()->setLastModifiedBy($set['author']);//最后修改人
		$objPHPExcel->getProperties()->setTitle($set['title']);//标题
		$objPHPExcel->getProperties()->setSubject($set['title']);//题目
		$objPHPExcel->getProperties()->setDescription($set['intru']);//描述
		$objPHPExcel->getProperties()->setKeywords($set['keyword']);//关键字
		$objPHPExcel->getProperties()->setCategory($set['category']);//种类
		$objPHPExcel->getActiveSheet()->setTitle($set['stitle']);//设置当前的sheet title
		//设置第一行的值：
		foreach ($firstline as $k => $v) {
                        if($k == 0) $k = "A";
                        if($k == 1) $k = "B";
                        if($k == 2) $k = "C";
                        if($k == 3) $k = "D";
			$v    = explode('|', $v);
			$v[2] = $v[2] ? $v[2] : 20;
                        
			$objPHPExcel->getActiveSheet()->setCellValue($k . '1', $v[0]);
                        
			$objPHPExcel->getActiveSheet()->getStyle($k . '1')->getFont()->setSize(14);//size
			$objPHPExcel->getActiveSheet()->getStyle($k . '1')->getFont()->setBold(true);//加粗
			$objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($v[2]);//宽度
                        
		}
                $p = 1;
		//设置单元格的值
		foreach ($lists as $s => $t) {
                        $p++;
			foreach ($firstline as $k => $v) {
                                $str = $t[$k];
                                if($k == 0) $k = "A";
                                if($k == 1) $k = "B";
                                if($k == 2) $k = "C";
                                if($k == 3) $k = "D";

				$objPHPExcel->getActiveSheet()->setCellValue($k . $p, $str);
			}
		}
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header('Content-Disposition:inline;filename="' . $filename . '"');
		header("Content-Transfer-Encoding: binary");
		header("Expires: Mon, 26 Jul 2016 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");
		$objWriter->save('php://output');
		exit();
	}

	/**
	 * 导出文件为CSV格式
	 * @param $lists //转换的数据
	 * @param $set   //Excel设置  array('title'=>'标题标题')
	 * @param $firstline //第一行的数据 "姓名,院系(班级),学号,账号,手机号,已打卡次数,未打卡次数"
	 */
	public function exportCSV($lists, $set, $firstline) {

		$data = array();

		foreach ($lists as $v) {
			$data[] = implode(',', $v);
		};

		$data     = $firstline ."\r\n". implode("\r\n", $data);
		$data     = chr(239) . chr(187) . chr(191) . $data;
		$filename = sprintf("%s-%s-%s.csv", $set['title'] ? $set['title'] : 'default', '_', date('Y-n-d'));

		$response = new Response();
		$response->headers->set('Content-type', 'text/csv');
		$response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
		$response->headers->set('Content-length', strlen($data));
		$response->setContent($data);
		$response->send();
	}


}