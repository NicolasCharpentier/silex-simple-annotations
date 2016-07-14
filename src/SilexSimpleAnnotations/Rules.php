<?php

namespace SilexSimpleAnnotations;

use SilexSimpleAnnotations\AbstractAnnotation;
use SilexSimpleAnnotations\AbstractAnnotations as Annots;
use Silex\Controller;
use Silex\ControllerCollection;

class Rules {

    /**
     * @var AbstractAnnotation[]
     */
    private $annotations;

    public function __construct() {

        $this->annotations = array(
            new Annots\Method(),
            new Annots\Prefix(),
            new Annots\Route(),
            new Annots\Bind(),
        );

        return $this;
    }

    static public function getVendorAnnotationShortCut() {
        return '@';
    }


    public function getCtrlRequiredAnnotations()
    {
        return $this->getAnnotationsWithRequirements(function (AbstractAnnotation $annot) {
            return $annot->getIsCtrl() and $annot->getType() === REQUIRED;
        });
    }

    public function getCtrlAnnotations()
    {
        return $this->getAnnotationsWithRequirements(function (AbstractAnnotation $annot) {
            return $annot->getIsCtrl();
        });
    }

    public function getActionAnnotations()
    {
        return $this->getAnnotationsWithRequirements(function (AbstractAnnotation $annot) {
            return ! $annot->getIsCtrl();
        });
    }

    static public function connectController(ControllerCollection $ctrlsFactory, $controllerArray)
    {
        $ctrlAccessPath = $controllerArray['Access'] . ':';

        foreach ($controllerArray['Actions'] as $_action) {
            $route = Annots\Route::buildRoute($ctrlsFactory, $_action['Path'], $controllerArray['Prefix'], $ctrlAccessPath, $_action['Name']);
            Annots\Method::buildRoute($route, $_action['Method']);
            Annots\Bind::buildRoute($route, $_action['Bind'], $controllerArray['Prefix'], $_action['Name']);
        }
    }

    static public function throwInvalidAnnotationsException($errorsArray, $guiltyFilePath = null)
    {
        $excStr = '';

        if ($guiltyFilePath) {
            $excStr .= 'File ' . realpath($guiltyFilePath) . PHP_EOL;
        }

        //TO DO: create specific exception?
        foreach ($errorsArray as $errorInfo) {
            $excStr .= 'Annotation ' . self::getVendorAnnotationShortCut() .
                $errorInfo['annot'] . ': ' . $errorInfo['err'];
            
            if ('Path' === $errorInfo['annot']) {
                $excStr .= ' (@Route has been changed for @Path for PHPStorm compatiblity reasons)';
            }
            
            $excStr .= PHP_EOL;
        }

        throw new \Exception(substr($excStr, 0, -1));
    }

    /**
     * @param $requirementsCallback
     * @return AbstractAnnotation[]
     */
    private function getAnnotationsWithRequirements($requirementsCallback)
    {
        $annotations = array();

        foreach ($this->annotations as $annot) {
            if ($requirementsCallback($annot))
                $annotations[] = $annot;
        }

        return $annotations;
    }
}