<?php
/*
 * 数据层
 * @package    
 * @author     wanglei@wyzc.com
 * @version    $Id$
 */
namespace Common\Model\Order;

use Common\Model\Common\BaseModel;


class OrderLogModel extends BaseModel
{
    protected $tableName = 'order_log';

	public function getConnection() {
		return $this;
	}
    public function getLog($id)
    {
        //$sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
        return $this->getConnection()->where(array('id'=>$id))->find() ? : null;
    }

    public function addLog($log)
    {
        $affected = $this->getConnection()->add($log);
        if ($affected <= 0) {
            E('Insert log error.');
        }
        return $this->getLog($affected);
    }

    public function findLogsByOrderId($orderId)
    {
//        $sql = "SELECT * FROM {$this->tableName} WHERE orderId = ?";
        return $this->getConnection()->where(array('orderId'=>$orderId))->select();
    }

}