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

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label'=>false,
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Имя',
                    'pattern'     => '.{2,}', //minlength
                    'class'=>'input-textarea',
                    'style'=>'width:100%'
                )
            ))
            ->add('email', 'email', array(
                'label'=>false,
                'required' => false,
                'attr' => array(
                    'placeholder' => 'E-mail',
                    'class'=>'input-textarea',
                    'style'=>'width:100%'
                )
            ))
            ->add('phone', 'text', array(
                'label'=>false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Телефон*',
                    'class'=>'input-textarea',
                    'style'=>'width:100%'
                )
            ))
            ->add('submit', 'submit', array(
                'label'=>'Отправить заявку',
                'attr' => array(
                    'placeholder' => 'Ваш e-mail',
                    'class'=>'input-submit',
                    'style'=>''
                )
            ))    
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $collectionConstraint = new Collection(array(
            'name' => array(
                new NotBlank(array('message' => 'Имя не должно быть пустым.')),
                new Length(array('min' => 2))
            ),
            'email' => array(
                new NotBlank(array('message' => 'Имя не должно быть пустым.')),
                new Email(array('message' => 'Не корректный адрес почты.'))
            )            
        ));

        $resolver->setDefaults(array(
            //'constraints' => $collectionConstraint,
            'data_class' => 'SIP\ResourceBundle\Entity\Bid'
        ));
    }

    public function getName()
    {
        return 'contact';
    }
}