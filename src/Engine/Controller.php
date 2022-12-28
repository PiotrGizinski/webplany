<?php

namespace Engine {

use Throwable;

    use Exception;
    use Singleton\Registry;
    use Smarty;
    use lessc;
    
    /**
     * Klasa wzorcowa, zawierająca metody dla kontrolerów
     * @abstract
     */
    abstract class Controller {
        
        /**
         * Przechowuje dane, która mają być ładowane do każdego widoku
         * @var array
         */
        private $basicConfig = array();

        /**
         * Przechowuje konfiguracje dla pliku .tpl
         * @var array
         */
        protected $config = array();

        /**
         * Przechowuje obiekt klasy Model danego MVC
         * @var Model
         */
        protected $model;
     
        /**
         * Przechowuje informację zwrotne z obsługi formularzy http         CZY POTRZEBNE??
         * @var undefined
         */
        private $resultForm;

        /**
         * Zmienna przechowująca obiekt klasy Smarty
         * @var Smarty
         */
        protected $tpl;

        /**
         * Zmienna przechowująca obiekt klasy lessc
         * @var lessc
         */
        protected $less;

        /**
         * Zmienna przechowująca ścięzkę do szablonu strony
         * @var string
         */
        protected $path = 'template/';

        /**
         * Przechowuje szablony stron
         * @var array
         */
        private $templates = array();

        /**
         * Przechowuje nazwę pliku tpl, który ma zostać załadowany
         */
        private $fileName = 'view';

        /**
         * Przechowuje nazwy plików css, które są załadowane do contentu
         */
        private $cssName = array();

        /**
         * Konstruktor tworzy obiekt klasy Model
         */
        final public function __construct() {
            $model = '\\'.str_replace('Controller', 'Model', get_class($this));
            $this->model = new $model;

            $this->tpl = new Smarty;
            $this->tpl -> setTemplateDir(array(
                'file' => APP.str_replace('Controller','',get_class($this)),
                'temp' => TEMPLATES
            ));
            $this->tpl -> compile_dir = TEMPLATES_C;
            $this->less = new lessc;
        }

        /**
         * Sprawdza czy zapytanie do aplikacji jest typu HTTP, czy AJAX i wywołuje metodę dla odpowiedniego typu
         * @param string $type
         * @param array $post
         * @return void
         */
        final public function execute(string $type, array $post) : void {
            if ($type === "AJAX") {
                if (!empty($post)) {
                    $this->ajaxRequest($post, Registry::getInstance()->getAuthorization()->getRole());
                }
            } else
            if ($type === 'HTTP') {
                if (!empty($post)) {
                    $this->resultForm = $this->postRequest($post, Registry::getInstance()->getAuthorization()->getRole());
                }
                $this->model->init();
                                 
                $this->default();
                $this->loadView();
            }
        }

        /**
         * Tworzy obiekt klasy View danego modelu MVC i przekazuje do niego dane do wyświetlenia
         * @return void
         */
        private function loadView() : void {
            $reg = Registry::getInstance();
            $this->basicConfig = [
                'logged' => $reg->getAuthorization()->getLogged(),
                'access' => $reg->getAuthorization()->getRole(),
                'exception' => $reg->getExceptionHandler()->getError(),
                'resultForm' => $this->resultForm,
                'debug' => $reg->getDebug(),
                'menu' => $reg->getRequest()->getMenu(),
                //'url' => $reg->getRequest()->getUrl(),
                'title' => $reg->getRequest()->getParameter('name')
            ];
            $this->load();
        } 

        /**
         * Metoda uruchamiana dla zwykłego zapytania HTTP
         * Dla zapytania z formularzem uruchamiana po wykonaniu metody postRequest
         * @return void
         */
        abstract protected function default() : void;

        /**
         * Funkcja do obsługi formularzy Ajax metodą POST
         * @param array $post - dane z wysłanego formularza
         * @param array $access - role zalogowanego użytkownika
         * @return void
         */
        abstract protected function ajaxRequest(array $post, array $access) : void;

        /**
         * Funkcja do obsługi formularzy metodą POST
         * @param array $post - dane z wysłanego formularza
         * @param array $access - role zalogowanego użytkownika
         * @return mixed
         */
        abstract protected function postRequest(array $post, array $access);

        /**
         * ta metoda w orginalnej wersji nie robi nic, ale w konkretnej wersji widoku ma 
         * poprawiać lub znieniać wartości parametru wysłanego do widoku tak żeby
         * nie było potrzeby nadmiernego przetważania w templacie, który ma tylko 
         * poskładać dane i je wyświetlić         *????
         * @param array $param
         * @return array
         */
        protected function tools(array $param):array{
            return $param;
        }

        /**
         * Funkcja ładująca dane do templata i wyswietlajaca go
         * @param array $basicConfig => Zawiera dane ładowane do każdego widoku MVC
         * @param array $param => Zawiera dane ładowane dla aktualnego widoku MVC
         * @return void
         */
        final public function load() {
            $this->tpl -> assign(array_merge($this->tools($this->config),$this->basicConfig));
            $this->loadCssToContent(strtolower($this->basicConfig['title']));
            //Zabezpieczyć poniższy kod
            $this->tpl -> assign('cssFilesName',$this->getCssNames());
            $this->tpl -> assign('view',$this->tpl->getTemplateDir('file').$this->fileName.'.tpl');
            if (!empty($this->config)) {
                $compare = array_intersect_key($this->basicConfig, $this->config);
                if ($compare) throw new Exception('Ostrzeżenie! Zmienna o nazwie "'.array_key_first($compare).'" jest już używana w widoku');
            }
            if (!file_exists($this->tpl->getTemplateDir('file').$this->fileName.'.tpl')) {
                throw new Exception('Nie znaleziono pliku szablonu o nazwie: '.$this->fileName.'.tpl');
            }
            $this->addTemplate($this->tpl->getTemplateDir('temp').'template.tpl');
            $this->displayTemplate();
        }

        /**
         * Przelicza czas poświecony na wygenerowanie strony
         * @return float
         */
        private function getTime() : float {
            $endTime = microtime(TRUE);
            $timeTaken = $endTime - TIME;
            $timeTaken = round($timeTaken,3);
            return $timeTaken;
        }

        /**
         * do przemyślenia????
         * @param array $param
         * @return void
         */
        public function addValue(array $param){
            if(!is_null($this->tpl)){
                foreach($param as $key=>$value){
                    $this->tpl -> assign($key, $value);
                }
            }
        }

        /**
         * Dodaje nazwę pliku css do tablicy nazw plików css ładowanych w widoku
         * @param string $fileName
         * @return void
         */
        private function addCssName(string $fileName) {
            (in_array($fileName, $this->cssName)) ?: array_push($this->cssName, $fileName);
        }

        /**
         * Zwraca nazwy plików css załadowane do contentu
         * @return array
         */
        private function getCssNames() : array {
            return $this->cssName;
        }

        /**
         * Metoda generująca plik css z plików less, kiedy czas utworzenia/edycji pliku less jest inny niż czas utworzenia/edycji plików less
         * Ładuje pliki css do widoku
         * @param string $name
         * @return void
         */
        private function loadCssToContent(string $name) {
            if (file_exists(STYLE.'main.less')) {
                if ($this->less->checkedCompile(STYLE.'main.less', STYLE.'main.css')) {
                    $cached = $this->less->cachedCompile(STYLE.'main.less');
                    file_put_contents(STYLE.'main.css', $cached['compiled']);
                }
                $this->addCssName('main');
            }
            if (file_exists($this->tpl->getTemplateDir('file').'styles.less')) {
                if ($this->less->checkedCompile($this->tpl->getTemplateDir('file').'styles.less', STYLE.$name.'.css')) {
                    $cached = $this->less->cachedCompile($this->tpl->getTemplateDir('file').'styles.less');
                    file_put_contents(STYLE.$name.'.css', $cached['compiled']);
                }
                $this->addCssName($name);
            }
        }

        /**
         * Metoda dodawania szablonu do zmiennej przechowującej szablony do załadowania
         * @var string $template
         * @return void
         */
        private function addTemplate(string $template) {
            (in_array($template, $this->templates)) ?: array_push($this->templates, $template);
        }

        /**
         * Metoda wyświetlająca załadowane szablony
         * @return void
         */
        private function displayTemplate() {
            $this->tpl -> assign('timeGenerate',$this->getTime());
            foreach($this->templates as $temp) {
                $this->tpl -> display($temp);
            }
        }
    }
}