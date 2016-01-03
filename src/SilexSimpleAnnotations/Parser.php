<?php

namespace SilexSimpleAnnotations;

use SilexSimpleAnnotations\Rules;

class Parser {

    /**
     * @param $controllersDirs
     * @param bool $youRoll
     * @throws \Exception
     */
    public function __construct($controllersDirs, $youRoll = false) {

        if (is_string($controllersDirs))
            $controllersDirs = [$controllersDirs];

        if (! $this->areValidArgs($controllersDirs, $youRoll))
            throw new \Exception('Invalid arguments');

        $this->Rules = new Rules();
        $this->controllerFiles = array();
        $this->registerDemControllers($controllersDirs, $youRoll);
    }

    public function getParsedControllers() {
        return $this->controllerAnnotations;
    }

    private $controllerFiles;

    private $Rules;

    private $controllerAnnotations;


    public function parseEmAll()
    {
        foreach ($this->controllerFiles as $file) {
            $parsed = $this->parseFile($file);

            if ($parsed !== null)
                $this->controllerAnnotations[] = $parsed;
        }

        return $this;
    }

    private function parseFile($path)
    {
        $fileArray  = $this->classFileContentToArray(file_get_contents($path));
        if ($fileArray === null)
            return null;

        $controller = $this->parseController($fileArray[0][1], $fileArray[0][2], $path);
        if ($controller === null)
            return null;

        array_shift($fileArray); // Kicking away ctrl crap

        $controller['Namespace'] = $this->parseNamespace(file_get_contents($path));
        $controller['Actions'] = $this->parseActions($fileArray, $path);

        return $controller;
    }

    private function parseController($comments, $declaration, $filePath = null)
    {
        $output = null;

        $controller = $this->getAnnotationsArrayInComment(
            $this->Rules->getCtrlAnnotations(), $comments, $filePath
        );

        if (preg_match("/class\s*(\w*)./", $declaration, $output) === false
            or ! $output or count($output) != 2) {
            return null;
        }
        $controller['Name'] = $output[1];

        return $controller;
    }

    private function parseNamespace($fileContent)
    {
        $output = null;

        if (preg_match("/namespace\s*(.*);/", $fileContent, $output) === false
        or !$output or count($output) != 2) {
            return null;
        }

        return $output[1];
    }

    private function parseActions($fileArrayWithoutControllerMetadata, $path)
    {
        $actions = array();
        $actionAnnotations = $this->Rules->getActionAnnotations();

        foreach ($fileArrayWithoutControllerMetadata as $data) {

            $action = $this->getAnnotationsArrayInComment(
                $actionAnnotations, $data[1], $path, true
            );  if ($action === null) {
                continue;
            }

            $output = null;
            if (preg_match("/function\s*(\w*)./", $data[2], $output) === false
                or ! $output or count($output) != 2) {
                continue;
            }

            $action['Name'] = $output[1];

            $actions[] = $action;
        }

        return $actions;
    }



    /*****************************************************************************************
     *  FILES PARSING
     */

    private function areValidArgs($controllersDirs, $recursiv)
    {
        foreach ($controllersDirs as $cD) {
            if (! is_dir($cD)) return false;
        }

        return is_bool($recursiv);
    }

    private function registerDemControllers($paths, $recursiv)
    {
        foreach ($paths as $path) {

            foreach ($this->findControllersInPath($path) as $ctrl) {
                $this->controllerFiles[] = $ctrl;
            }

            if ($recursiv) {
                $this->registerDemControllers(
                    $this->findPathsInPath($path), true
                );
            }
        }
    }

    private function loopInHandle($path, $callback, &$result, $utilData = null)
    {
        if (! $handle = opendir($path))
            return false;

        while (false !== ($filePath = readdir($handle))) {
            $_path = ($path . '/' . $filePath);

            if ($utilData) {
                if ($callback($_path, $filePath, $result, $utilData) === false)
                    break;
            } else {
                if ($callback($_path, $filePath, $result) === false)
                    break;
            }
        }

        closedir($handle);

        return true;
    }

    private function findPathsInPath($path)
    {
        $paths = array();

        $this->loopInHandle($path, function ($fullPath, $partialPath, &$result) {
            if ($partialPath === '.' || '..' === $partialPath)
                return true;

            if (is_dir($fullPath))
                $result[] = $fullPath;

            return true;
        }, $paths);

        return $paths;
    }


    private function findControllersInPath($path)
    {
        $controllers = array();

        $this->loopInHandle($path, function ($fullPath, $partialPath, &$result, $Rules) {
            if (is_dir($fullPath))
                return true;

            if (self::isValidCtrlFile($fullPath, $Rules))
                $result[] = $fullPath;

            return true;
        }, $controllers, $this->Rules);

        return $controllers;
    }

    static private function isValidCtrlFile($path, Rules $Rules) {
        $fileArrayContent  = file($path);
        //^ *class .*$
        $classLinesContent = preg_grep('/^ *class .*$/', $fileArrayContent);

        if (count($classLinesContent) != 1) return false;

        $fileAsArray = self::classFileContentToArray(implode('', $fileArrayContent));

        if ($fileAsArray === null)
            return $fileAsArray;

        try {
            $parsedAsCtrl =
                self::getAnnotationsArrayInComment($Rules->getCtrlRequiredAnnotations(), $fileAsArray[0][1], true);
        } catch (\Exception $e) {
            $parsedAsCtrl = null;
        }

        return !! $parsedAsCtrl;
        // true if file fits minimum controller annots required, false otherwise
    }



    /*****************************************************************************************
     * ANNOTATIONS PARSING
     */

    static private function classFileContentToArray($fileContent)
    {
        $result = array();

        // This regex will give [[ [0]dont care, [1] the comments, [2] the associated class||function ] ... ]
        // First regex result will always be the class one
        preg_match_all(
            "/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))([\s*\r?\n*].*\r?\n*.*{|.*$)/",
            $fileContent,
            $result, PREG_SET_ORDER
        );

        if (empty($result) or empty($result[0]) or count($result[0]) != 3) {
            return null; // should not happen at this stage, but not worth an exception
        }


        return $result;
    }

    /**
     * @param AbstractAnnotation[] $annotations
     * @param $comment
     * @param null $filePath
     * @param bool $avoidIfNoAnnotations
     * @return array|null
     * @throws \Exception
     */
    static private function getAnnotationsArrayInComment($annotations, $comment, $filePath = null, $avoidIfNoAnnotations = false)
    {
        $annotationsArray = array();
        $errorsArray    = array();
        $hadAnnotationRegistered = false;

        foreach ($annotations as $annot) {
            $stalked = self::stalkAnnot($annot->getName(), $comment);

            if ($stalked === null and $annot->getType() === REQUIRED) {
                $errorsArray[] = [
                    'err'   => 'Missing',
                    'annot' => $annot->getName()
                ];
                continue;
            }

            if ($stalked !== null and ! $annot->isValidValue($stalked)) {
                $errorsArray[] = [
                    'err'   => 'Wrong value (' . $stalked . ')',
                    'annot' => $annot->getName(),
                ];
                continue;
            }

            if ($stalked !== null)
                $hadAnnotationRegistered = true;

            $annotationsArray[$annot->getName()] = $stalked ? : $annot->getDefaultValue();
        }

        if (count($errorsArray)) {
            if ($avoidIfNoAnnotations and ! $hadAnnotationRegistered) {
                return null;
            } else Rules::throwInvalidAnnotationsException($errorsArray, $filePath);
        }

        return $annotationsArray;
    }

    static private function stalkAnnot($needle, $haystack)
    {
        $sc = Rules::getVendorAnnotationShortCut();
        $output = null;

        $regex = "/^.*$sc$needle\s*(.*)$/";

        $line = preg_grep($regex, explode(PHP_EOL, $haystack));
        preg_match($regex, reset($line), $output);

        return ($output and count($output) > 1) ? $output[1] : null;
    }

    /*
    public function debug()
    {
        file_put_contents('tlp.txt',
            var_export($this->controllerAnnotations, true));
    }
    */
}