<?php
namespace Common\Model\{{app}};

use Common\Model\Common\BaseModel;

class {{module}}ServiceModel extends BaseModel {

	private $page = 15;

	private function SearchCondition($condition) {
		$searchCondition = array();
		return $searchCondition;
	}

	public function paginate($request, $condition) {
		$condition = $this->SearchCondition($condition);
		return $this->get{{module}}()->condition($condition)->paginate($this->page, $request);
	}

	public function create($data) {
		$res = $this->get{{module}}()->store([

		]);
		return $res;
	}

	public function update($data,$id) {
        $res = $this->get{{module}}()->update([

        ],$id);
        return $res;
    }

    public function delete($data,$id) {
        $res = $this->get{{module}}()->destroy($id);
        return $res;
    }


	private function get{{module}}() {
		return $this->createService('{{app}}.{{module}}Model');
	}


}