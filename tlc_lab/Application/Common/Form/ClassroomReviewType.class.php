<?php

namespace Common\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class ClassroomReviewType extends AbstractType
{

    public function buildForm (FormBuilderInterface $builder, array $options)
    {

        $builder->add('rating', 'hidden');
        $builder->add('content', 'textarea');
    }

    public function getName ()
    {
        return 'review';
    }

}