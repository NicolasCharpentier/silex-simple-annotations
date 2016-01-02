<?php
/**
 * Created by PhpStorm.
 * User: Nico
 * Date: 02/01/2016
 * Time: 13:18
 */

namespace OriginalNamespace\AbstractAnnotations;


use OriginalNamespace\AbstractAnnotation;

class Method extends AbstractAnnotation{

    public function __construct()
    {
        parent::construct(
            \OriginalNamespace\OPT_DEF_VAL, 'Method', 'GET'
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