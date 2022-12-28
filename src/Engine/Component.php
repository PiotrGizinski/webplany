<?php

namespace Engine {
    
    use Singleton\Registry;
    use Exception;

    abstract class Component {

        /**
         * Przechowuje konfigurację dla tej klasy 
         * @var array
         */
        private $config = array();

        /**
         * Konstruktor wywołuje ustawianie konfiguracji a następnie metodę prepare
         * @param Config $config
         */
        final public function __construct(Config $config) {
            $this->config = $config;
            if ($this->config['enable'] || !isset($this->config['enable'])) {
                $this->init();
            }
        }

        final public function __call($name, $arguments) {
            if ($this->config['enable'] || !isset($this->config['enable'])) {
                $this->$name($arguments);
            }
        }

        /**
         * Metoda inicjalizująca komponent
         * Wywoływana w konstruktorze klasy po pobraniu konfiguracji
         * @return void
         */
        abstract protected function init() : void;

        /**
         * Funkcja zwracająca konfigurację o zadanym kluczu z konfiguracji
         * @param string $key
         * @return mixed
         */
        final protected function getConfig(string $key) {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            } else throw new Exception("Brak elementu w konfiguracji o nazwie ".$key." dla ".get_class($this));
        }

        /**
         * Umieszcza informacje w logach
         * @param array $info   Array/string
         * @return void
         */
        final protected function putLog(array $message) : string {
            if (!file_exists(APP . "../logs/")) mkdir(APP . '../logs/');
            $path = APP . "../logs/" . (new \ReflectionClass($this))->getShortName() . ".log";
            $info = date('H:i:s', time()) . " " .
                "From: " . Registry::getInstance()->getRequest()->getIp() . "; " .
                "Url: "  . Registry::getInstance()->getRequest()->getUrl() . "; " .
                "Role: " . implode(", ",Registry::getInstance()->getAuthorization()->getRole()) . "; ".
                "User: " . Registry::getInstance()->getAuthorization()->getLogin() . "\n";
            foreach ($message as $msg) {
                $info .= "\t-> " . $msg . "\n";
            }
            file_put_contents($path, $info, FILE_APPEND | LOCK_EX);
            return $info;
        }

        
        
    }
}

?>