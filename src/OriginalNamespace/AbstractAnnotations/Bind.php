<?php
/**
 * Created by PhpStorm.
 * User: Nico
 * Date: 02/01/2016
 * Time: 17:44
 */

namespace OriginalNamespace\AbstractAnnotations;


use OriginalNamespace\AbstractAnnotation;

class Bind extends AbstractAnnotation {

    public function __construct()
    {
        parent::construct(
            \OriginalNamespace\OPT_NO_VAL, 'Bind'
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