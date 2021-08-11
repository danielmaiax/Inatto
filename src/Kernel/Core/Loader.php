<?php

namespace Inatto\Kernel\Core;

class Loader
{

    public function __construct()
    {
        //
        spl_autoload_register([$this, 'autoload'], true);
    }

    function autoload($class)
    {
        // apenas classes do namespace inatto
        if (substr($class, 0, 6) != 'Inatto') return null;

        //
        $class = str_replace("\\", "/", $class);
        $class = str_replace("Inatto/", "Inatto.phar/", $class);
        require_once("phar://{$class}.php");
        return null;
    }

}

//
new Loader();