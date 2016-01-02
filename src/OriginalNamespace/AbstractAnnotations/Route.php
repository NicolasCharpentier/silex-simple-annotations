<?php
/**
 * Created by PhpStorm.
 * User: Nico
 * Date: 02/01/2016
 * Time: 13:15
 */

namespace OriginalNamespace\AbstractAnnotations;


use OriginalNamespace\AbstractAnnotation;

class Route extends AbstractAnnotation {

    public function __construct()
    {
        parent::construct(
            \OriginalNamespace\REQUIRED, 'Route'
        );
    }

    public function isValidValue($val)
    {
        return $val[0] === '/';
    }
}