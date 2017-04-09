<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('md5', [$this, 'md5Filter']),
        ];
    }

    public function md5Filter($string)
    {
        return md5($string);
    }

    public function getName()
    {
        return 'app_extension';
    }
}