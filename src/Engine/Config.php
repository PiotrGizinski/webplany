<?php

namespace Engine {

    use ArrayAccess;

    final class Config implements ArrayAccess {
        
        /**
         * Przechowuje konfiguracje
         * @var array
         */
        private $container = array();

        /**
         * Ładuje konfiguracje do pola klasy
         * @param array $array
         */
        public function __construct($array) {
            $this->container = $array;
        }

        public function offsetExists($offset) : bool {
            return isset($this->container[$offset]);
        }

        public function offsetGet($offset) : mixed {
            return isset($this->container[$offset]) ? $this->container[$offset] : null;
        }

        public function offsetSet($offset, $value) : void { 
            throw new \Exception("próba nieautoryzowanych zmian w config");
        }

        public function offsetUnset($offset) : void {
            unset($this->container[$offset]);
        }

        /**
         * Nie wykorzystywane
         */
        /*public function get($pole) {
            return $this->offsetGet($pole);
        }*/

        /*public function getNameSet(){
            echo "<pre>";
            print_r($this->container);
            echo "</pre>";
        }*/
    }
}

?>