<?php
namespace Common\Lib;

class ArrayToolkit {


	public static function column(array $array, $columnName) {
		if (empty($array)) {
			return array();
		}

		$column = array();
		foreach ($array as $item) {
			if (isset($item[$columnName])) {
				$column[] = $item[$columnName];
			}
		}

		return $column;
	}

	public static function remove_value(array $array,$value){
		if(empty($array)){
			return array();
		}

		foreach ($array as $key => $val){
			if($value == $val){
				unset($array[$key]);
			}
		}

		return $array;
	}

	public static function parts(array $array, array $keys) {
		foreach (array_keys($array) as $key) {
			if (!in_array($key, $keys)) {
				unset($array[$key]);
			}
		}
		return $array;
	}

	public static function requireds(array $array, array $keys) {
		foreach ($keys as $key) {
			if (!array_key_exists($key, $array)) {
				return false;
			}
		}
		return true;
	}

	public static function changes(array $before, array $after) {
		$changes = array('before' => array(), 'after' => array());
		foreach ($after as $key => $value) {
			if (!isset($before[$key])) {
				continue;
			}
			if ($value != $before[$key]) {
				$changes['before'][$key] = $before[$key];
				$changes['after'][$key]  = $value;
			}
		}
		return $changes;
	}

	public static function group(array $array, $key) {
		$grouped = array();
		foreach ($array as $item) {
			if (empty($grouped[$item[$key]])) {
				$grouped[$item[$key]] = array();
			}
			$grouped[$item[$key]][] = $item;
		}

		return $grouped;
	}

	public static function index(array $array, $name) {
		$indexedArray = array();
		if (empty($array)) {
			return $indexedArray;
		}

		foreach ($array as $item) {
			if (isset($item[$name])) {
				$indexedArray[$item[$name]] = $item;
				continue;
			}
		}
		return $indexedArray;
	}

	public static function filter(array $array, array $specialValues) {
		$filtered = array();
		foreach ($specialValues as $key => $value) {
			if (!array_key_exists($key, $array)) {
				continue;
			}

			if (is_array($value)) {
				$filtered[$key] = (array)$array[$key];
			} elseif (is_int($value)) {
				$filtered[$key] = (int)$array[$key];
			} elseif (is_float($value)) {
				$filtered[$key] = (float)$array[$key];
			} elseif (is_bool($value)) {
				$filtered[$key] = (bool)$array[$key];
			} else {
				$filtered[$key] = (string)$array[$key];
			}

			if (empty($filtered[$key])) {
				$filtered[$key] = $value;
			}
		}

		return $filtered;
	}

	public static function pushInArray(array &$arr,$key,$value){
		if(empty($arr[$key]) || !is_array($arr[$key])){
			$arr[$key] = array($value);
		}else{
			array_push($arr[$key],$value);
		}
		return $arr;
	}

	/**
	 * 对二维数据进行排序
	 * @author 钱志伟 2013-11-19
	 */
	public static function sort2Array($paramArr = array()) {
		$options = array(
			'data'    => array(), // 组
			'field'   => 'time', // 序依据的字段
			'asc'     => 0, // =>降序 1=>升序
			'numeric' => 0
		);
		$options = array_merge($options, $paramArr);
		extract($options);

		if ($numeric)
			$data = array_values($data);
		// 入排序
		$len = count($data); // 后一个元素下标
		for ($i = 1; $i < $len; $i++) {
			$tmp = $data [$i];
			$j   = $i - 1;
			if ($asc) { // 序
				while ($j >= 0 && $data [$j] [$field] > $tmp [$field]) {
					$data [$j + 1] = $data [$j];
					$j--;
				}
			} else { // 序
				while ($j >= 0 && $data [$j] [$field] < $tmp [$field]) {
					$data [$j + 1] = $data [$j];
					$j--;
				}
			}

			$data [$j + 1] = $tmp;
		}
		return $data;
	}

    
    /**
     * 递归获取数组父节点下子节点的总数
     * @author lvwulong 2016.4.20
     * $mapList = array(array('id' => 2, 'pid' => 0,...),....);
     */
    public static function getTreeNodeCount($mapList, $pid, $x = -1) {
        static $num = 0;
        $num = ($x == 1) ? $num : 0;
        if (empty($mapList)) {
            return false;
        }
        foreach ($mapList as $k => $v) {
            if ($v['pid'] == $pid) {
                $num = self::getTreeNodeCount($mapList, $v['id'], 1)+1;
                unset($mapList[$k]);
            }
        }
        return $num;
    }
    /**
     * 递归获取数组父节点下子节点列表
     * @author lvwulong 2016.4.20
     * $mapList = array(array('id' => 2, 'pid' => 0,...),....);
     */
    public static function getTreeNodeList($mapList, $pid, $x = -1) {
        static $tree = array();
        $tree = ($x == 1) ? $tree : array();
        if (empty($mapList)) {
            return false;
        }
        foreach ($mapList as $k => $v) {
            if ($v['pid'] == $pid) {
                $tree[] = $v;
                unset($mapList[$k]);
                self::getTreeNodeList($mapList, $v['id'], 1);
            }
        }
        return $tree;
    }
}
