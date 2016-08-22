<?php

namespace GameBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GameBundle extends Bundle
{
    /**
     * @return string
     */
    public static function getColor()
    {
        return 'orange';
    }
}
