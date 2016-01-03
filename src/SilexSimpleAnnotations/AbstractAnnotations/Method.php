<?php

namespace SilexSimpleAnnotations\AbstractAnnotations;


use SilexSimpleAnnotations\AbstractAnnotation;

class Method extends AbstractAnnotation{

    public function __construct()
    {
        parent::construct(
            \SilexSimpleAnnotations\OPT_DEF_VAL, 'Method', 'GET'
        );
    }

    public function isValidValue($val)
    {
        return array_search(strtoupper($val), [
            'GET', 'POST', 'PUT', 'DELETE'
        ]) !== false;
    }

    static public function forUsage($val) {
        return strtolower($val);
    }
}