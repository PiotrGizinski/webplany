<?php

namespace Engine {

    use Component\Database;
    use Singleton\Registry;
    
    /**
     * Klasa wzorcowa, zawierająca metody dla modelów
     * @abstract
     */
    abstract class Model {

        /**
         * Instancja klasy bazowej
         * @var Database
         */
        private $db;

        /**
         * Dane z bazy danych
         * @var array
         */
        protected $data = array();

        /**
         * Pobiera obiekt komponentu Database
         */
        final public function __construct()
        {
            $this->db = Registry::getInstance()->getDatabase();
        }

        /**
         * Funkcja dodająca dane do pudełka je trzymające
         * @param array $data
         * @return void
         */
        final protected function setData(array $data) : void {
            array_push($this->data, $data);
        }

        /**
         * Funkcja zwaracająca dane z pudełka o podanym numerze
         * @param int $number
         * @return array
         */
        final public function getData($number) : array {
            return (isset($this->data[$number])) ? $this->data[$number] : [] ;
        }

        /**
         * Zwraca obiekt bazy danych
         * @return Database
         */
        final protected function getDb() : Database {
            return $this->db;
        }

        /**
         * Funkcja wywołona po wykonaniu konstruktora tej klasy
         * @return void
         */
        abstract public function init() : void;
    }
}