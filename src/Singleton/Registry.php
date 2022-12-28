<?php

namespace Singleton {

    use Component;
    use Engine\Config;
    use Component\ApplicationHelper;
    use Throwable;

class Registry {

        /**
         * Przechowuje instancję obiektu tej klasy
         * @var self
         */
        private static $instance;

        /**
         * Przechowuje tablicę obiektów klas z przestrzeni nazw Component (Komponenty)
         * @var array
         */
        private $objects = array();

        /**
         * Przechowuje nazwę 
         */
        private $objectsPath = 'Component\\';

        /**
         * Przechowuje dane z debugera
         * @var array
         */
        private $debug = array();

        /**
         * przechowuje obiekt Application Helpera
         *
         * @var ApplicationHelper
         */
        private $AH;

        /**
         * 
         * @return self
         */
        public static function getInstance() : self {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        /**
         * Wywoływany przy tworzeniu obiektu dynamicznego, nie widziany przy obiekcie statycznym
         */
        private function __construct() {
            try {
                $this->AH = new Component\ApplicationHelper();
            } catch(\Exception $e) {
                var_dump($e->getMessage());
            }
        }

        //---------------------------- Component(Komponenty) ----------------------------//

        /**
         * Metoda przeszukająca przestrzeń Component w poszukiwaniu klasy o konretnej nazwie i dziedziczącej po klasie Engine\Component
         * @param string $name
         * @return mixed
         */
        private function getObject($name) {
            try {
                if (!isset($this->objects[$name])) {
                    $class = $this->objectsPath.$name;
                    if (class_exists($class)) {
                        $refclass = new \ReflectionClass($class);
                        if ($refclass->isSubclassOf("Engine\\Component")) {
                            $this->objects[$name] = new $class($this->getSettings($name));
                        }
                    }
                }
                if (!isset($this->objects[$name])) throw new \Exception('Nie znaleziono komponentu o nazwie: '.$class);
                return (isset($this->objects[$name])) ? $this->objects[$name] : null;
            } catch (Throwable $e) {
                Registry::getInstance()->getExceptionHandler()->catchException($e);
            }
        }

        /**
         * Metoda zwracająca objekty z przestrzeni Component przechowywane w klasie Registry
         * @return array
         */
        private function getObjects() : array {
            return $this->objects;
        }

        /**
         * Funkcja zwracająca obiekty o zadanej nazwie, wywołanie metody np. getNazwa_obiektu()
         * @var string $name
         * @var undefined $arguments
         * @return mixed
         */
        public function __call(string $name, $arguments = null) {
            if (empty($arguments)) {
                if (mb_substr($name, 0, 3) === 'get') {
            
                        $object = $this->getObject(mb_substr($name, 3, strlen($name)));
                    
                    return $object; 
                }
            }
            return null;
        }

        /**
         * zwraca obiekt ApplicationHelper
         * @return Component\ApplicationHelper
         */
        public function getApplicationHelper() : Component\ApplicationHelper {
            if (is_null($this->AH)) {
                return ($this->AH = new Component\ApplicationHelper());
            }
            return $this->AH;
        }
        
        //------------------------- END Component(Komponenty) ---------------------------//
        //------------------------------------ Debug ------------------------------------//

        public function isDebug() : bool {          
            if (isset($this->getAllParams("global")['debug'])) {
                return $this->getAllParams("global")['debug'];
            } else {
                return false;
            }
        }

        /**
         * Zbiera dane dla debugera i je zwraca
         * @return array
         */
        public function getDebug() : array {
            if ($this->isDebug()) {
                $this->debug['Registry'] = $this->getObjects();
                $this->debug['Get'] = $_GET;
                $this->debug['Formularze'] = ['$_POST' => $_POST, 'post' => $this->getRequest()->getPost()];
                $this->debug['Session'] = $_SESSION;
                $this->debug['Cookie'] = $_COOKIE;
                return $this->debug;
            } else {
                return array();
            }
        }
        //------------------------------------ END Debug ------------------------------------//

        /**
         * pobieranie wartości z tablicy AllParams
         * @param string $key
         * @return mixed
         */ 
        public function getAllParams(string $key) : array {
            return $this->AH->getSettingsArray($key);
        }

        public function getSettings($name) : Config {
            $class = "";
            if (is_object($name)) {
                $x = explode('\\',get_class($name));
                 $class = end($x);
            } elseif (\is_string($name)&&(preg_match('/^[a-zA-Z0-9\.\\\-_]{3,}$/', $name))) {            
                $x = explode('\\',$name);
                $class = end($x);
            }
            if ($class !== "") {
                if (isset($this->getAllParams("settings")[$class])) {
                    return new Config($this->getAllParams("settings")[$class]);
                }
            }
            return new Config(array());
        }

        /**
         * zwraca tablicę ze zmiennymi globalnymi ustawionymi w pliku konfiguracyjnym
         * @param string $key
         * @return void
         */
        public function getGlobals(string $key) {
            if (isset($this->getAllParams("global")[$key])) {
                return $this->getAllParams("global")[$key];
            } else {
                return null;
            }
        }
    }
}

?>