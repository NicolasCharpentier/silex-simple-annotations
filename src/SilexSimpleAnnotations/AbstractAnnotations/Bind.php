<?php

namespace SilexSimpleAnnotations\AbstractAnnotations;


use SilexSimpleAnnotations\AbstractAnnotation;

class Bind extends AbstractAnnotation {

    public function __construct()
    {
        parent::construct(
            \SilexSimpleAnnotations\OPT_NO_VAL, 'Bind'
        );
    }

    public function isValidValue($val)
    {
        // TODO : Regex for any char and '.'
        return true;
    }

    static public function forUsage($val, $ctrlPrefix, $actionName) {
        if ($val !== null)
            return $val;

        $binded = strtolower($ctrlPrefix . '.' . str_replace('Action', '', $actionName));

        return substr(str_replace('/', '.', $binded), 1);
    }
}