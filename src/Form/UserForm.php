<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $data = array();
        $data = $options['data']['roles'];
        $roles = array();

        foreach ($data as $role){
            $roles[$role['id']] = $role['name'];    
        }

        return $builder->add(
            'login',
            'text',
            array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 5, 'max' => 16))
                )
            )
        )
        ->add(
            'password',
            'password',
            array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 5))
                )
            )
        )
        ->add(
            'mail',
            'email',
            array(
                'constraints' => array(
                    new Assert\NotBlank()
                )
            )
        )
        ->add(
            'role',
            'choice',
            array(
                'choices' => $roles,
                'constraints' => array(
                    new Assert\NotBlank(),
                )
            )
        );
    }

    public function getName()
    {
        return 'userForm';
    }
}