<?php
namespace Common\Model\Question\Type;

class SinglechoiceQuestionType extends ChoiceQuestionType
{
    public function hasMissScore()
    {
        return false;
    }
}