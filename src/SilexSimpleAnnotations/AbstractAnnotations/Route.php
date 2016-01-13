<?php

namespace SilexSimpleAnnotations\AbstractAnnotations;


use Silex\Controller;
use SilexSimpleAnnotations\AbstractAnnotation;

class Route extends AbstractAnnotation {

    public function __construct()
    {
        parent::construct(
            \SilexSimpleAnnotations\REQUIRED, 'Route'
        );
    }

    public function isValidValue($val)
    {
        return $val[0] === '/';
    }

    static public function buildRoute($ctrlsFactory, $routeName, $ctrlPrefix, $ctrlPath, $actionName)
    {
        return $ctrlsFactory->match(
            $ctrlPrefix . $routeName, $ctrlPath . $actionName
        );
    }
}