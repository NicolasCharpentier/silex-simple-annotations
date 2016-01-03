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
        $val = strtoupper($val);
        $methodCount = 0;

        $this->loopInAvailableOptions(function ($method, &$methodCount) use ($val) {
            if (strpos($val, $method) !== false)
                $methodCount++;
        }, $methodCount);

        return !! $methodCount;
    }

    static public function forUsage($val) {
        $val = strtoupper($val);
        $methods = '';

        self::loopInAvailableOptions(function ($method, &$methods) use ($val) {
            if (strpos($val, $method) !== false)
                $methods = strlen($methods) ? $methods . '|' . $method : $method;
        }, $methods);

        return $methods;
    }

    static private function loopInAvailableOptions($callback, &$customFlag)
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'] as $method) {
            if ($callback($method, $customFlag) === false)
                return true;
        }

        return true;
    }
}