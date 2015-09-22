<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        return $builder->add(
            'old_password',
            'password',
            array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 8))
                ),
                'label' => 'Stare hasło'
            )
        )
        ->add(
            'new_password', 
            'repeated',
            array(
                'type' => 'password',
                'invalid_message' => 'Hasła muszą być takie same.',
                'first_options'  => array('label' => 'Nowe hasło'),
                'second_options' => array('label' => 'Powtórz nowe hasło'),
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
        return 'changePasswordForm';
    }
}