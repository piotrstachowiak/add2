<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Silex\Application;

use Model\CategoriesModel;

class UpForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {   

        return $builder->add(
            'image',
            'file'
        );

    }

    public function getName()
    {
        return 'upForm';
    }

}