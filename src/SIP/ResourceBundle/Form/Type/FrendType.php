<?php
/*
 * (c) JackDavyd
 */
namespace SIP\ResourceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

class FrendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  
        $builder            
            ->add('email', 'email', [
                'label'=>false,
                'attr' => [
                    'placeholder' => 'E-mail',
                    'class'=>'input-textarea',
                    'style'=>'width:300px;'
                ]
            ])
            ->add('subject', 'text', [
                'label'=>false,                
                'attr' => [
                    'placeholder' => 'Тема',                    
                    'class'=>'input-textarea',
                    'style'=>'width:300px;'
                ]
            ])
            ->add('body', 'textarea', [
                'label'=>false,
                'attr' => [
                    'placeholder' => 'Тема',                    
                    'class'=>'input-textarea',
                    'style'=>'width:300px; height:200px;',
                    'rows'=>10
                ]
            ])        
            ->add('submit', 'submit', [
                'label'=>'Отправить',
                'attr' => [
                    'id'=>'frend_submit',
                    'class' => 'btn btn-success',
                    'style'=>'float:left; margin-right:20px;',
                ]
            ])
            ->add('button', 'button', [
                'label'=>'Закрыть',
                'attr' => [
                    'placeholder' => 'Тема',                    
                    'class' => 'btn btn-danger',
                    'data-dismiss'=>"modal",
                    'aria-hidden'=>"true",
                    'style'=>'float:left;',
                ]
            ])        
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $collectionConstraint = new Collection([
            'email' => [
                new NotBlank(['message' => 'Имя не должно быть пустым.']),
                new Email(['message' => 'Не корректный адрес почты.'])
            ],
            'subject' => [
                new NotBlank(['message' => 'тема не должна быть пустой.']),
                new Length(['min' => 2])
            ],            
            'body' => [
                new NotBlank(['message' => 'Письмо не должно быть пустым.']),
                new Length(['min' => 2])
            ]
        ]);

        $resolver->setDefaults(array(            
            //'constraints' => $collectionConstraint,
            'data_class' => 'SIP\ResourceBundle\Entity\Frend'
        ));
    }

    public function getName()
    {
        return 'frend';
    }
}