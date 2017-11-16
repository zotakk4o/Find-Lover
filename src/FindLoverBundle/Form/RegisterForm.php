<?php

namespace FindLoverBundle\Form;

use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
		    ->add('nickname', TextType::class)
	        ->add('firstName', TextType::class)
	        ->add('lastName', TextType::class)
	        ->add('email', TextType::class)
		    ->add('gender', ChoiceType::class, array(
		    	'choices' => array(
				    'Male' => 'male',
				    'Female' => 'female'
			        ),
				'expanded' => true
			    )
		    )
	        ->add('password', PasswordType::class)
		    ->add('birthDate', DateType::class, array(
		    	'widget' => 'single_text'
		        )
		    )
		    ->add('phoneNumber', NumberType::class)
    	    ->add('submit', SubmitType::class,  array(
		        'label' => 'Register',
		        'attr'  => array('class' => 'register-login-button register-form-button')
	            )
	        );

    }

    public function configureOptions(OptionsResolver $resolver)
    {

	    $resolver->setDefaults(array(
		    'data_class' => 'FindLoverBundle\Entity\Lover'
	    ));

    }

    public function getBlockPrefix()
    {
        return 'find_lover_bundle_register_form';
    }
}
