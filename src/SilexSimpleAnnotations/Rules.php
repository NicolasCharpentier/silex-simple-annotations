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
        $ctrlAccessPath = $controllerArray['Namespace'] . '\\' . $controllerArray['Name'] . '::';
        $prefix = $controllerArray['Prefix'];

        foreach ($controllerArray['Actions'] as $_action) {

            $method = Annots\Method::forUsage($_action['Method']);
            $route  = $prefix . $_action['Route'];
            $action = $_action['Name'];
            $binded = Annots\Bind::forUsage($_action['Bind'], $prefix, $_action['Name']);

            /** @var Controller $routeFactored */
            $routeFactored = $ctrlsFactory
                ->$method($route, $ctrlAccessPath . $action)
                ->bind($binded);

            // TODO : Virer et proposer le assert en annot
            if (strpos($route, '{id}') !== false)
                $routeFactored->assert('id', "\d+");
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
                $errorInfo['annot'] . ': ' . $errorInfo['err'] . PHP_EOL;
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