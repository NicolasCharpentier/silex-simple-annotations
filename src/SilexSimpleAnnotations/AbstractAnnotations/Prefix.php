<?php

namespace SilexSimpleAnnotations\AbstractAnnotations;


use SilexSimpleAnnotations\AbstractAnnotation;

class Prefix extends AbstractAnnotation {

    public function __construct()
    {
        parent::construct(
            \SilexSimpleAnnotations\REQUIRED, 'Prefix', null, true
        );
    }


    public function isValidValue($val)
    {
        return $val[0] === '/' and substr($val, -1) !== '/';
    }
}