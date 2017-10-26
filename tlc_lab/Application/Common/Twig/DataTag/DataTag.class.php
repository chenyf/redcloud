<?php

namespace Common\Twig\DataTag;

interface DataTag
{
    public function getData(array $arguments);
}
