<?php

namespace SilexSimpleAnnotations\AbstractAnnotations;


use Silex\Controller;
use SilexSimpleAnnotations\AbstractAnnotation;

class Bind extends AbstractAnnotation {

    public function __construct()
    {
        parent::construct(
            \SilexSimpleAnnotations\OPT_NO_VAL, 'Bind'
        );
    }

    static public function buildRoute(Controller $route, $value, $ctrlPrefix, $actionName)
    {
        $route->bind(self::forUsage($value, $ctrlPrefix, $actionName));
    }

    public function isValidValue($val)
    {
        // TODO : Regex for any char and '.'
        return true;
    }

    static private function forUsage($val, $ctrlPrefix, $actionName) {
        if ($val !== null)
            return $val;

        $binded = strtolower($ctrlPrefix . '.' . str_replace('Action', '', $actionName));

        return substr(str_replace('/', '.', $binded), 1);
    }
}