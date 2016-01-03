<?php

namespace SilexSimpleAnnotations\AbstractAnnotations;


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
}