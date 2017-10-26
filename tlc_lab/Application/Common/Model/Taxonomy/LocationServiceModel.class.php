<?php
/*
 * 业务层
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace  Common\Model\Taxonomy;

use Common\Model\Common\BaseModel;

class LocationServiceModel extends BaseModel
{
	public function getLocationFullName($id)
	{
		$provinceId = intval($id / 10000) * 10000;
		$cityId = intval($id / 100) * 100;
		$districtId = $id;
		$locations = $this->getLocationDao()->findLocationsByIds(array(
			$provinceId, $cityId, $districtId
		));

		$names = array('province' => '', 'city' => '', 'district' => '');
		foreach ($locations as $location) {
			if ($location['id'] == $provinceId) {
				$names['province'] = $location['name'];
			} else if ($location['id'] == $cityId) {
				$names['city'] = $location['name'];
			} else if ($location['id'] == $districtId) {
				$names['district'] = $location['name'];
			} 
		}

		return $names;
	}

	public function getAllLocations()
	{
		return $this->getLocationDao()->findAllLocations();
	}

    private function getLocationDao()
    {
        return $this->createDao("Taxonomy.Location");
    }

}