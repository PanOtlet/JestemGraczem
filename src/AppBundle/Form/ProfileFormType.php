<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('steam')
                ->add('battlenet')
                ->add('lol')
                ->add('beampro')
                ->add('twitch')
                ->add('youtube')
                ->add('localization')
                ->add('description')
                ->add('profilePictureFile')
                ->remove('email')
                ->remove('username')
                ->remove('plainPassword');
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}