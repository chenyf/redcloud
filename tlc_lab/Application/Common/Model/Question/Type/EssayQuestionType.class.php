<?php
namespace Common\Model\Question\Type;

class EssayQuestionType extends AbstractQuestionType
{
    public function judge(array $question, $answer)
    {
        return array('status' => 'right');
    }

}