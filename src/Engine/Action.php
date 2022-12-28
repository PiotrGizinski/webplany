<?php

namespace Engine {

    use Singleton\Registry;

    abstract class Action {

        /**
         * @var Registry
         */
        protected $reg;

        final public function __construct() { 
            $this->reg = Registry::getInstance();
        }
        
        /**
         * Funkcja wykonująca akcję
         * @return void
         */
        abstract public function execute() : void;
    }
}

?>