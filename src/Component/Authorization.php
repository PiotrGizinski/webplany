<?php

namespace Component {

    use Singleton\Registry;
    
    final class Authorization extends \Engine\Component {

        /**
         * Przechowuje nazwe zmiennej sesyjnej przechowującej wartość zalogowania
         * @var string
         */
        private $logged = 'AUTH';

        /**
         * Przechowuje nazwe zmiennej sesyjnej przechowującej login usera
         * @var string
         */
        private $login = "LOGIN";

        /**
         * Przechowuje nazwe zmiennej sesyjnej przechowującej role użytkownika
         * @var string
         */
        private $role = "ROLA";

        protected function init() : void {
            $this->setRole();
        }

        /**
         * Funkcja logowania
         * @return void
         */
        public function signIn() : void {
            if (!$this->getLogged()) {
                $_SESSION[$this->logged] = true;
                $this->setRole();
                $this->putLog(array("Zalogowanie do systemu"));
            }
        }

        /**
         * Funkcja zwracająca login zalogowanego użytkownika
         * @return string
         */
        public function getLogin() : string {
            return (isset($_SESSION[$this->login])) ? $_SESSION[$this->login] : '';
        }

        /**
         * Funkcja zwracająca wartość zalogowania(czy jesteś zalogowany)
         * @return bool
         */
        public function getLogged() : bool {
            return (isset($_SESSION[$this->logged])) ? $_SESSION[$this->logged] : false;
        }

        /**
         * Role nazwowe
         * Funkcja pobierająca role zalogowanego użytkownika z bazy danych
         * @return void
         */
        private function setRole() : void {
            if ($this->getLogged()) {
                $_SESSION[$this->role] = array($this->getConfig('loggedRole'));
                $result = Registry::getInstance()->getDatabase()->query('SELECT rola FROM widokKont WHERE konto = :user', array(':user' => $this->getLogin()));
                if (isset($result[0]['rola'])) {
                    array_push($_SESSION[$this->role], $result[0]['rola']);
                }
            } else {
                $_SESSION[$this->role] = array($this->getConfig('defaultRole'));
            }
        }

        /**
         * Funkcja zwracająca role osoby zalogowanej
         * @return array
         */
        public function getRole() : array {
            (isset($_SESSION[$this->role])) ?: $this->setRole();
            return $_SESSION[$this->role];
        }

        /**
         * Funkcja wylogowania
         * @return void
         */
        public function signOut() : void {
            $this->putLog(array("Wylogowanie z systemu"));
            unset($_SESSION[$this->role]);
            unset($_SESSION[$this->login]);
            unset($_SESSION[$this->logged]);
        }
    }
}