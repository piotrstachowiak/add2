<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Silex\Application;

use Model\CategoriesModel;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {   

        $data = array();
        $data = $options['data']['categories'];
        $categories = array();

        foreach ($data as $category){
            $categories[$category['id']] = $category['name'];    
        }

        return $builder->add(
            'id',
            'hidden',
            array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type(array('type' => 'digit'))
                )
            )
        )
        ->add(
            'title', 
            'text',
            array(
		'label' => 'Tytuł ogłoszenia',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 5))
                )
            )
        )
        ->add(
            'text', 
            'text',
            array(
		'label' => 'Opis',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 5))
                )
            )
        )
        ->add(
            'category',
            'choice',
            array(
		'label' => 'Kategoria',
                'choices' => $categories,
                'constraints' => array(
                    new Assert\NotBlank()
                ),
            )
        )
/*
        ->add(
            'image_name',
            'file'
        )
*/
        ;
    }

    public function getName()
    {
        return 'adForm';
    }
}
