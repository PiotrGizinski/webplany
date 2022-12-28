<?php

namespace Engine {

    use Singleton\Registry;
    use Smarty;
    use lessc;
    use Exception;

    /**
     * Klasa includująca metody dla widoków
     * @abstract
     */
    abstract class View {
        
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
         * Konstruktor klasy bazowej View
         * @return void
         */
        final public function __construct() {
            $this->tpl = new Smarty;
            $this->tpl -> setTemplateDir(array(
                'file' => APP.str_replace('View','',get_class($this)),
                'temp' => TEMPLATES
            ));
            $this->tpl -> compile_dir = TEMPLATES_C;
            $this->less = new lessc;
        }

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
        final public function load(array $basicConfig, $param = array()) {
            $this->tpl -> assign(array_merge($this->tools($param),$basicConfig));
            //echo "<pre>";
           // print_r(array_merge($this->tools($param),$basicConfig));
            //echo "</pre>";
            //try {
                $this->loadCssToContent(strtolower($basicConfig['title']));
                //Zabezpieczyć poniższy kod
                $this->tpl -> assign('cssFilesName',$this->getCssNames());
                $this->tpl -> assign('view',$this->tpl->getTemplateDir('file').$this->fileName.'.tpl');
                if (!empty($param)) {
                    $compare = array_intersect_key($basicConfig, $param);
                    if ($compare) throw new Exception('Ostrzeżenie! Zmienna o nazwie "'.array_key_first($compare).'" jest już używana w widoku');
                }
                if (!file_exists($this->tpl->getTemplateDir('file').$this->fileName.'.tpl')) {
                    throw new Exception('Nie znaleziono pliku szablonu o nazwie: '.$this->fileName.'.tpl');
                }
                $this->addTemplate($this->tpl->getTemplateDir('temp').'template.tpl');
                $this->displayTemplate();
            /*} catch (Exception $e) {
                Registry::getInstance()->getExceptionHandler()->catchException($e);
                //Registry::getInstance()->getExceptionHandler()->loadDefault
                //$this->tpl -> assign('exception',Registry::getInstance()->getExceptionHandler()->getError());
                //$this->addTemplate($this->tpl->getTemplateDir('temp').'template.tpl');
                $this->displayTemplate();
            }*/
        }

        /**
         * Przelicza czas poświecony na wygenerowanie strony
         * @return float
         */
        final private function getTime() : float {
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
         * Dodaje do konfiguracji szablony, składają się na nie sekcja head, content modułu, sekcja foot
         * @return void
         */
        /*final private function loadPage() {

        }*/

        /**
         * Dodaje nazwę pliku css do tablicy nazw plików css ładowanych w widoku
         * @param string $fileName
         * @return void
         */
        final private function addCssName(string $fileName) {
            (in_array($fileName, $this->cssName)) ?: array_push($this->cssName, $fileName);
        }

        /**
         * Zwraca nazwy plików css załadowane do contentu
         * @return array
         */
        final private function getCssNames() : array {
            return $this->cssName;
        }

        /**
         * Metoda generująca plik css z plików less, kiedy czas utworzenia/edycji pliku less jest inny niż czas utworzenia/edycji plików less
         * Ładuje pliki css do widoku
         * @param string $name
         * @return void
         */
        final private function loadCssToContent(string $name) {
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
        final private function addTemplate(string $template) {
            (in_array($template, $this->templates)) ?: array_push($this->templates, $template);
        }

        /**
         * Metoda wyświetlająca załadowane szablony
         * @return void
         */
        final private function displayTemplate() {
            $this->tpl -> assign('timeGenerate',$this->getTime());
            foreach($this->templates as $temp) {
                $this->tpl -> display($temp);
            }
        }
    }
}