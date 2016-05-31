<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('steam');
        $builder->add('battlenet');
        $builder->add('lol');
        $builder->add('twitch');
        $builder->add('youtube');
        $builder->add('localization');
        $builder->remove('email');
        $builder->remove('username');
        $builder->remove('plainPassword');
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