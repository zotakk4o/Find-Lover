<?php

namespace FindLoverBundle\Form;

use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
		    ->add('nickname', TextType::class, array('attr'=>array('class'=>'form-field')))
	        ->add('firstName', TextType::class)
	        ->add('lastName', TextType::class)
	        ->add('email', TextType::class)
	        ->add('password', TextType::class)
    	    ->add('submit', SubmitType::class,  array(
		        'label' => 'Register',
		        'attr'  => array('id' => 'register-button')
	        ));

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
