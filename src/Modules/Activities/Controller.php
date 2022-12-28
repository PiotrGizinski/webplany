<?php

namespace Modules\Activities {

    class Controller extends \Engine\Controller { 

        /**
         * Przechowuje date
         */
        private $date;

        protected function default() : void {
            (isset($this->date)) ?: $this->date = date('Y-m-d');
            $this->config = array(
                "date" => $this->date,
                "buildings" => $this->model->getData(0),
                "hours" => $this->model->getData(1)
            );
        }

        protected function ajaxRequest(array $post, array $access) : void {
            $instructor = (isset($_SESSION['idWykladowcy'])) ? $_SESSION['idWykladowcy'] : null ;
            switch ($post['data']) {
                //Zwraca listę sal dla wybranego budynku oraz zajęcia i propozycje przeniesienia dla wybranej daty 
                case 'rooms' : {
                    if (!empty(array_key_exists('idBuilding',$post)) && !empty(array_key_exists('date',$post))) {
                        $result['rooms'] = $this->model->getRooms($post['idBuilding']);
                        $result['activities'] = $this->model->getActivities($post['date'], $post['idBuilding'], $instructor);
                        $result['transfers'] = $this->model->getTransfers($post['date'], $post['idBuilding'], $instructor);
                        echo (json_encode($result));
                    } else if (!empty(array_key_exists('idBuilding',$post))) {
                        $result = $this->model->getRooms($post['idBuilding']);
                        echo (json_encode($result));
                    }
                    break;
                }
                //Pobieranie danych zajęć dla aktualnie wybranego dnia i budynku
                case 'activities' : {
                    if (!empty(array_key_exists('date',$post)) && !empty(array_key_exists('idBuilding',$post))) {
                        $result['activities'] = $this->model->getActivities($post['date'], $post['idBuilding'], $instructor);
                        $result['transfers'] = $this->model->getTransfers($post['date'], $post['idBuilding'], $instructor);
                        echo (json_encode($result));
                    }  
                    break;
                }
                //Pobieranie danych wybranych zajęć
                case 'selectActivitie' : {
                    if (!empty(array_key_exists('idActivitie',$post))) {
                        $result = $this->model->getActivitie($post['idActivitie']);
                        echo (json_encode($result));
                    }
                    break;
                }
            }
        }

        protected function postRequest(array $post, array $access) {
            if (in_array('wykładowca',$access)) {
                switch ($post['data']) {
                    case 'transferActivitie' : {
                        (!isset($post['selectDate'])) ?: $this->date = $post['selectDate'];
                        if (!empty(array_key_exists('idActivitie',$post)) && !empty(array_key_exists('selectDate',$post)) && 
                        !empty(array_key_exists('selectRoom',$post)) && !empty(array_key_exists('selectHour',$post))) {
                            $result = $this->model->putTransfer($post['selectDate'], $post['idActivitie'], $post['selectRoom'], 
                                $post['selectHour'], $_SESSION['idWykladowcy']);
                            return $result;
                        }
                        break;
                    }
                }
            }
        }
    }
}

?>