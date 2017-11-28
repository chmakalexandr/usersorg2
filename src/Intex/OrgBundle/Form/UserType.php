<?php

namespace Intex\OrgBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('lastname',TextType::class, array('label' => 'Last Name','attr'=> array('class'=>'form-control')));
        $builder->add('firstname',TextType::class, array('label' => 'First Name','attr'=> array('class'=>'form-control')));
        $builder->add('middlename',TextType::class, array('label' => 'Middle Name','attr'=> array('class'=>'form-control')));
        $builder->add('bithday', DateType::class, array('label' => 'Bithday(YYYY-MM-DD)',
            'widget' => 'single_text','format' => 'yyyy-mm-dd','attr'=> array('class'=>'input-group date form-control')));
        $builder->add('inn',TextType::class, array('label' => 'ITN (12 digits)','attr'=> array('class'=>'form-control')));
        $builder->add('snils', TextType::class, array('label' => 'INILA (11 digits)','attr'=> array('class'=>'form-control')));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Intex\OrgBundle\Entity\User'
        ));
    }

    public function getBlockPrefix()
    {
        return 'intex_orgbundle_usertype';
    }
}
