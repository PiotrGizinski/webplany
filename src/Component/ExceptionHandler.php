<?php

namespace Component {

    use Throwable;
    use ErrorException;
    use Singleton\Registry;

    final class ExceptionHandler extends \Engine\Component {

        /**
         * Tablica przechowujące błedy, które występiły w trakcie wykonywania skryptu
         * @var array
         */
        private $error = array();

        /**
         * Przechowuje wartość 
         * @var
         */
        private $loadDefault = false;

        protected function init() : void {
            @set_error_handler(array($this,"catchError"));
            @set_exception_handler(array($this,"catchException"));
            register_shutdown_function(array($this,"fatalHandler"));
            ini_set( "display_errors", $this->getConfig('displayErrors'));
            error_reporting( E_ALL );
        }

        /**
         * Funkcja wywoływania w momencie zakończenia wykonywania skryptu
         * Sprawdza czy wystąpił jakiś bład krytyczny w działaniu aplikacji jeśli wystąpił, wywołuje ładowanie modułu domyślnego aplikacji
         * Wywołuje umieszczenie błedów w logach (metoda putLog)
         * @return void
         */
        public function fatalHandler() : void {
            chdir(MAIN);
            $e = error_get_last();
            if ($e != null) {
                $ex = new ErrorException($e['message'], 0, $e['type'], $e['file'], $e['line']);
                $this->catchException($ex);
            }    
            if ($e != null || $this->loadDefault) {
                $this->putLog($this->getDetailsError());
                Registry::getInstance()->getRequest()->loadDefault();
                Registry::getInstance()->getRequest()->execute();
            }
            if (!empty($this->error) && !Registry::getInstance()->isDebug()) {
                $this->putLog($this->getDetailsError());
            }
        }

        /**
         * Przetwarza błąd aplikacji typu Error na ErrorException i wywołuje metodę catchException
         * @param $num
         * @param $str
         * @param $file
         * @param $line
         * @return void
         */
        public function catchError($num, $str, $file, $line) {
            $e = new ErrorException($str, 0, $num, $file, $line);
            $this->catchException($e);
        }

        /**
         * Dodaj błędy do tablicy i sprawdza czy wyświetlić
         * @param Throwable $e
         * @return void
         */
        public function catchException(Throwable $e) : void {
            if (in_array(get_class($e), $this->getConfig("fatalTypes"))) $this->loadDefault = true;
            array_push($this->error, $e);
            error_clear_last();
        }

        /**
         * Funkcja zwracająca szczegółowe dane błędów
         * @return array
         */
        private function getDetailsError() : array {
            $result = array();
            foreach ($this->error as $e) {
                $message = 
                    "Type: " . get_class($e) . " " .
                    "Message: " . $e->getMessage() . " " .
                    "File: " . $e->getFile() . " " .
                    "Line: " . $e->getLine() . " "; 
                (!$this->getConfig('trace')) ?: $message.= "Trace: " . $e->getTraceAsString();
                array_push($result, $message);
            }
            return $result;
        }

        /**
         * Zwraca informacje o błędach w postaci tablicy do interfejsu
         * @return array
         */
        public function getError() : array {
            $result = array();
            if (Registry::getInstance()->isDebug()) {
                $result = $this->getDetailsError();
            } else if ($this->error) {
                $result = array($this->getConfig('simpleMessage'));
            }
            return $result;
        }
    }
}

?>