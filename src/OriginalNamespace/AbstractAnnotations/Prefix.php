<?php

namespace OriginalNamespace\AbstractAnnotations;


use OriginalNamespace\AbstractAnnotation;

class Prefix extends AbstractAnnotation {

    public function __construct()
    {
        parent::construct(
            \OriginalNamespace\REQUIRED, 'Prefix', null, true
        );
    }


    public function isValidValue($val)
    {
        return $val[0] === '/' and substr($val, -1) !== '/';
    }
}