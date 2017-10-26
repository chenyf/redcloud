<?php
namespace Common\Twig\Util;

use Topxia\Service\Common\ServiceKernel;

class CategoryBuilder
{
	public function buildChoices($groupCode, $indent = '　')
	{
        $choices = array();
        $categories = $this->getCategoryService()->getCategoryTree();

        foreach ($categories as $category) {
            $choices[$category['id']] = str_repeat(is_null($indent) ? '' : $indent, ($category['depth']-1)) . $category['name'];
        }

        return $choices;
	}

    private function getCategoryService()
    {
        return ServiceKernel::instance()->createService('Taxonomy.CategoryService');
    }
}
    