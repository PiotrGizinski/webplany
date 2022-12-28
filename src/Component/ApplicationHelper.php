<?php

namespace Component {

    use Exception;

    /**
     * obiekt ustawiający zmienne pobrane z plików konfiguracyjnych
     */
    class ApplicationHelper {
        //private $config_path = DIR_MAIN."/../config/";// tu zrobić zmianę

        /**
         * Przechowuje ściężkę do pliku konfiguracyjnego
         */
        private $config_path = "../";

        /**
         * Przechowuje dane z pliku konfiguracyjnego
         */
        private $config;

        /**
         * 
         * @return void
         */
        public function __construct() {
            $this->init();     
        }

        /**
         * pobiera z pliku dane konfiguracyjne i dane dla requestu
         * @return void
         */
        private function init() {
            $_fileinfo = $this->config_path."config.json";
            if (file_exists($_fileinfo)) {
                $xml = file_get_contents($_fileinfo);
                if ($xml!= false) {
                    $config = json_decode($xml, 1);
                    if (is_null($config)) throw new Exception("Błąd w trakcie dekodowania pliku konfiguracji config", 1);
                    $this->config = $config;
                } else {
                    throw new Exception('Problem z plikiem konfiguracyjnym config!');
                }
            } else {
                throw new Exception('Brak pliku konfiguracyjnego config!');
            }
        }
        // ----------------------- forma przejściowa GET'ów -----------------------------------

        /**
         * Zwraca wartości z tablicy zmiennych konfiguracyjnych
         * - do zmiany  na metodę prywatną po dodaniu metod lub metody personalizującej zapytanie *
         * @param string $nameConfig
         * @return array
         */
        public function getconfig(string $nameConfig) : array {
            if (is_null($this->config))$this->init();
            if (isset($this->config[$nameConfig])) {
                return  $this->config[$nameConfig];                     
            } else return array();
        }

        /**
         * używane w registry
         * @return array
         */
        public function getSettingsArray($key) : array {
            if (!empty($key) && is_string($key)) {
                switch($key) {
                    case "settings": {
                        return $this->getconfig("objects");
                        break;
                    }
                    case "command":
                    case "request": {
                        return $this->getconfig("request");
                        break;
                    }
                    case "global": {
                        return $this->getconfig("global");
                        break;
                    }
                    default: throw new Exception('niewłaściwa wartość parametru !!');
                }
            }
            return array();
        }

        /**
         * Domyslnie ma zwracać listę obiektów do których można się odwołać za pomocą menu lub wywołania
         * czyli nazwy z katalogu Modules
         * - na potrzeby edycji menu i requestów *
         * @return array
         */
        public function getModelList() : array {
            $ret = array();
            $kat = glob(APP."/Modules/*", GLOB_ONLYDIR);    
            //echo  dirname($_SERVER["SCRIPT_NAME"]);
            foreach($kat as $katalog) {
                $katalog = basename(rtrim($katalog, '/'));
                $ret[] = $katalog;
            }
            //echo "<pre>"; print_r($ret); echo "</pre>"; 
            return $ret;

        }
        //--------------END GET po modernizacji ------------------------------------------------        
    }
}

?>