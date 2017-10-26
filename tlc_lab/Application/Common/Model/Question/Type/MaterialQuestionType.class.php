<?php
namespace Common\Model\Question\Type;

class MaterialQuestionType extends AbstractQuestionType
{
    public function judge(array $question, $answer)
    {
        return array('status' => 'right');
    }

    public function canHaveSubQuestion()
    {
        return true;
    }

}