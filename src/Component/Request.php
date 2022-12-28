<?php

namespace Component {

    use Singleton\Registry;
    use ReflectionClass;
    use Exception;
    use Throwable;

final class Request extends \Engine\Component {

        /**
         * Przechowuje parametry dla requesta z pliku konfiguracyjnego, aktualne url)
         * @var array-associative
         */
        private $parameters = array();
        
        /**
         * Przechowuje adres IP z którego idzie zapytanie do strony
         * @var string
         */
        private $ip;


        /**
         * Przechowuje typ zapytania requesta
         * @var string
         */
        private $type = "HTTP";

        /**
         * Przechowuje zmienne typu POST przychodzące z zapytaniem do strony
         * @var array
         */
        private $post = array();

        /**
         * Przechwuje adres URI zapytania
         * @var array
         */
        private $uri = array();

        /**
         * Przechowuje adres URL zapytania
         * @var string
         */
        private $url = "";

        /**
         * Przechowuje wygenerowane menu z pliku konfiguracji na bazie poziomu dostępu użytkownika
         * @var array
         */
        private $menu = array();

        /**
         * Przechowuje informację zwrotną z ładowania modułu MVC
         * @var array
         */
        private $feedback = array();

        /**
         * Przechowuje aktualny obiekt MVC
         * @var object
         */
        private $currentObject;

        /**
         * Przechowuje parametry domyślnego obiektu MVC z konfiguracji
         * @var array-associative
         */
        private $defaultModule = array();

        //------------------------------------ OBSŁUGA ZAPYTANIA REQUEST ------------------------------------//

        protected function init() : void {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $this->ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $this->ip = $_SERVER['REMOTE_ADDR'];
            }
            $this->defaultModule = $this->getConfig('pathMVC').$this->getConfig('default')['class']."\Controller";
            $this->menu = $this->generateMenu($this->getConfig('request'), Registry::getInstance()->getAuthorization()->getRole());
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') $this->type = 'AJAX';
        }

        /**
         * 
         */
        public function handleRequest() {
            $this->url = filter_var($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
            $this->uri = (!empty(str_replace($_SERVER['HTTP_HOST'].ROOT, '', $this->url))) ? explode('/', str_replace($_SERVER['HTTP_HOST'].ROOT, '', $this->url)) : array() ;

            $this->post = $_POST;
            foreach ($this->post as $key=>$value) {
                unset($_POST[$key]);
            }
            
            $this->parameters = $this->checkRequest($this->getConfig('request'), Registry::getInstance()->getAuthorization()->getRole());

            $classMVC = '';
            $classAction = '';
            if (isset($this->parameters['class'])) {
                $classMVC = $this->getConfig('pathMVC').$this->parameters['class'].'\Controller';
                $classAction = $this->getConfig('pathAction').$this->parameters['class'];
            }

            if (!$this->checkClass('Engine\\Controller', $classMVC)) {
                if (!$this->checkClass('Engine\\Action', $classAction)) {
                    $this->loadDefault();
                }
            }
            $this->execute();
        }
    
        /**
         * Funkcja sprawdzająca czy danemu zapytaniu odpowiada obiekt MVC (Modules) lub akcji (Action) i czy są odpowiednie uprawnienia do niego
         * W razie sukcesu - pobiera dane konfiguracyjne obiektu MVC (Modules) lub akcji (Action)
         * @param array $request
         * @param array $access
         * @return array
         */
        private function checkRequest(array $request, array $access) : array {
            $objectName = $request['default'];
            unset($request['default']);
            foreach ($this->getUri() as $row) {
                if (array_key_exists($row, $request)) {
                    if (is_array($request[$row])) {
                        $request = $request[$row];
                        if (isset($request['name']) && isset($request['class']) && isset($request['access'])) {
                            if (array_intersect($access, $request['access'])) {
                                $objectName = $request;
                                unset($errorCode);
                            } else {
                                $objectName = array();
                                $errorCode = 101;
                                break;
                            }
                        } else 
                        if (isset($request['name'])) {
                            $objectName = array();
                            $errorCode = 404;
                        } else {
                            throw new Exception('Błąd konfiguracji! Niepoprawna struktura pliku');
                        }
                    } else {
                        throw new Exception('Błąd konfiguracji! Niepoprawna struktura pliku');
                    }
                } else {
                    $objectName = array();
                    $errorCode = 404;
                    break;
                }
            }
            (!isset($errorCode)) ?: $objectName['errorCode'] = $errorCode;
            return $objectName;
        }

        /**
         * Funkcja sprawdzająca czy istnieje klasa przypisana do zapytania i czy jest klasą dziedzicząca po Engine/Controller lub Engine/Action
         * @param string $pattern
         * @param string $class
         * @return bool
         */
        private function checkClass(string $pattern, string $class) : bool {
            if (empty($class)) {
                $this->addFeedback("nie odnaleziono ścieszki ".$this->getUrl());
                $this->parameters['errorCode'] = 404;
            } else
            if (!class_exists($class)) {
                $this->parameters['errorCode'] = 404;           //tego nie widzi aplikacja, jakiś błąd w przekazywaniu kodu błędu do domyślnego modułu
                $this->addFeedback("nie odnaleziono klasy ".$class." pod adresem ".$this->getUrl());
            } else {
                $refclass = new ReflectionClass($class);
                if (!$refclass->isSubclassOf($pattern)) {
                    $this->addFeedback("polecenie ".$class." nie wchodzi w skład hierarchii MVC");
                } else {
                    try {
                        $this->currentObject = $refclass->newInstance();
                        return true;
                    } catch (Throwable $e) {
                        $this->parameters['errorCode'] = 503;       //dlaczego nie wyświetla tego kodu błędu na stronie?
                        Registry::getInstance()->getExceptionHandler()->catchException($e, true);
                        return false;
                    }
                }
            }
            $this->currentObject = new $this->defaultModule();
            return false;
        }

        /**
         * Ładuje domyślny obiekt MVC (Modules)
         * @return void
         */
        public function loadDefault() : void {
            // Zastępuje wcześniej wygenerowane dane na dane domyślne dla klasy błędu strony
            $old = $this->parameters;
            $this->parameters = $this->getConfig('default');
            $this->parameters['old'] = $old;
            $this->currentObject = new $this->defaultModule();
        }
        
        /**
         * Ładuje obiekt MVC lub obiekt Action
         * @return void
         */
        public function execute() : void {
            $this->currentObject->execute($this->getType(), $this->getPost());    
        }

        /**
         * 
         * @param string $msg
         * @return void
         */
        public function addFeedback(string $msg) : void {
            array_push($this->feedback,$msg);
        }
    
        /**
         * 
         * @return array
         */
        public function getFeedback() : array {
            return $this->feedback;
        }

        /**
         * Zwraca parametr z konfiguracji dla wygenerowanego obiektu MVC
         * @param string $key
         * @return mixed
         */
        public function getParameter(string $key) {
            return (isset($this->parameters[$key])) ? $this->parameters[$key] : null;
        }

        /**
         * Zwraca zmienne typu POST
         * @return array
         */
        public function getPost() : array {
            return $this->post;
        }

        /**
         * Zwraca adres URI zapytania(URL) w formacie array
         * @return array
         */
        public function getUri() : array {
            return $this->uri;
        }

        /**
         * Zwraca adres URL zapytania
         * @return string
         */
        public function getUrl() : string {
            return $this->url;
        }

        /**
         * Zwraca typ zapytania
         * @return string
         */
        public function getType() : string {
            return $this->type;
        }

        /**
         * Zwraca adres ip z którego wysłano zapytanie do strony
         */
        public function getIp() : string {
            return $this->ip;
        }

        /**
         * Funkcja generująca menu na bazie pliku konfiguracyjnego
         * W celu optymilizacji można buforować menu, w momencie logowanie i wylogowania, aby nie generować go przy każdym zapytaniu do strony!!!
         * @param array $request
         * @param array $access
         * @param string $url
         * @return array
         */
        private function generateMenu(array $request, array $access, string $url = ROOT) : array {
            $menu = array();
            foreach ($request as $key=>$row) {
                if (isset($row['name']) && isset($row['class']) && isset($row['access'])) {
                    if (array_intersect($access, $row['access'])) {
                        //Tymczasowe rozwiązanie dla ticketa
                        if (isset($row['link'])) {
                            $menu[$row['name']] = $url.$row['link'];
                        } else {
                            $menu[$row['name']] = ($key === 'default') ? $url : $url.$key;
                        }
                    }
                } else 
                if (isset($row['name'])) {
                    $result = $this->generateMenu($row, $access, $url.$key.'/');
                    (empty($result)) ?: $menu[$row['name']] = $result;
                }
            }
            return $menu;
        }

        /**
         * Zwraca wygenerowane menu
         * @return array
         */
        public function getMenu() : array {
            return $this->menu;
        }        
    }
}
?>