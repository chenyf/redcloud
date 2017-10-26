<?php
namespace Common\Model\Question\Type;

class UncertainchoiceQuestionType extends ChoiceQuestionType
{
    public function hasMissScore()
    {
        return true;
    }
}