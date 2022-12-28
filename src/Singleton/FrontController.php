<?php

namespace Singleton {

    final class FrontController {        

        /**
         * Przechowuje instancję(obiekt) klasy Registry
         */
        private $reg;

        /**
         * Inicjuje utworzenie obiektu klasy Registry oraz ExceptionHandler(obsługa błędów)
         */
        public function __construct() {
            $this->init();
        }

        private function init() {
            $this->reg = Registry::getInstance();
            $this->reg->getExceptionHandler();      //inicjowanie komponentu obsługi błędów
            $this->reg->getAuthorization();         //inicjowanie komponentu autoryzacji
            
            //Testowanie obsługi błedów z wywołaniem komponentów
            //$this->reg->getTester();
            //$this->reg->getAddh();
        }

        /**
         * Wywołuje obsługę zapytania do strony
         */
        public function run() {
            $this->reg->getRequest()->handleRequest();
        }
    }
}
    
?>