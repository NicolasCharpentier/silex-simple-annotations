<?php

namespace OriginalNamespace;

const REQUIRED    = 0;
const OPT_DEF_VAL = 1;
const OPT_NO_VAL  = 2;


abstract class AbstractAnnotation {
    
    public function construct($type, $name, $defaultValue = null, $isCtrl = false)
    {
        $this->setType($type)
            ->setName($name)
            ->setDefaultValue($defaultValue)
            ->setIsCtrl($isCtrl)
            ;
    }

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isCtrl; // Whether its a ctrlr annot or not

    /**
     * @var string|null
     */
    private $defaultValue;

    //private $strictValues;


    private function setType($type) {
        $this->type = $type;

        return $this;
    }

    private function setName($name) {
        $this->name = $name;

        return $this;
    }

    private function setDefaultValue($defaultVal) {
        $this->defaultValue = $defaultVal;

        return $this;
    }

    private function setIsCtrl($boule) {
        $this->isCtrl = $boule;

        return $this;
    }

    private function setStrictValues($values) {
        $this->strictValues = $values;

        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function getName() {
        return $this->name;
    }

    public function getDefaultValue() {
        return $this->defaultValue;
    }

    public function getIsCtrl() {
        return $this->isCtrl;
    }

    abstract public function isValidValue($val);
}