<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EditProfileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['data']['id'];
        $role = $options['data']['role'];
        
        return $builder->add(
            'role_id',
            'hidden',
            array(
                'data' => $role,
            )
        )
        ->add(
            'id',
            'hidden',
            array(
                'data' => $id,
            )
        )
        ->add(
            'login', 
            'text',
            array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 5))
                )
            )
        )
        ->add(
            'password', 
            'repeated',
            array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
                'options' => array('attr' => array('class' => 'password-field')),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 5))
                )
            )
        );
    }

    public function getName()
    {
        return 'editProfileForm';
    }
}