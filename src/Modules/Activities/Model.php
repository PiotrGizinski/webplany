<?php

namespace Modules\Activities {
    
    use Singleton\Registry;

    class Model extends \Engine\Model {

        public function init() : void {
            $this->setData($this->getDb()->query('SELECT * FROM widokBudynki ORDER BY id'));
            $this->setData($this->getDb()->query('SELECT id, nazwa FROM widokGodzinZajeciowych'));
        }

        /**
         * Zwraca id i numery sal dla budynku o zadanym id
         * @param int $idBuilding - id budynku
         * @return array
         */
        public function getRooms(int $idBuilding) : array {
            $result = $this->getDb()->query('SELECT id, numer FROM sala WHERE idBudynek = :idBudynek', array(':idBudynek' => $idBuilding));
            return $result;
        }

        /**
         * Zwraca zajęcia odbywającego się podanego dnia
         * @param string $date - data
         * @param int $idBuilding - id budynku
         * @param int $idInstructor - id zalogowanego wykładowcy
         * @return array
         */
        public function getActivities(string $date, int $idBuilding, int $idInstructor = null) : array {
            $result = $this->getDb()->query('SELECT id, data, idSala, idGodziny, grupy, wykladowca, przedmiot, IF(idWykladowca = :idWykladowca, true, false) AS instructor FROM widokZajecia 
                WHERE data = :data AND idSala IN (SELECT id FROM widokSale WHERE idBudynek = :idBudynek)',
                array(':data' => $date, ':idBudynek' => $idBuilding, ':idWykladowca' => $idInstructor));
            return $result;
        }

        /**
         * Zwraca propozycje przeniesień dla podanego dnia
         * @param string $date - data
         * @param int $idBuilding - id budynku
         * @param int $idInstructor - id zalogowanego wykładowcy
         * @return array
         */
        public function getTransfers(string $date, int $idBuilding, int $idInstructor = null) {
            $result = $this->getDb()->query('SELECT id, zData, zSala, zGodzina, data, idSala, sala, idGodzina, godzina, grupy, wykladowca, przedmiot, zBudynek, IF(idWykladowca = 
                :idWykladowca, true, false) AS instructor FROM widokPrzeniesien WHERE data = :data AND idSala IN (SELECT id FROM widokSale WHERE idBudynek = :idBudynek)',
                array(':data' => $date, ':idBudynek' => $idBuilding, ':idWykladowca' => $idInstructor));
            return $result;
        }

        /**
         * Zwraca dane wybranych zajęć
         * @param int $idActivitie - tabilca id wybranych zajęć
         * @return array
         */
        public function getActivitie(int $idActivitie) {
            $result = $this->getDb()->query('SELECT id, DATE_FORMAT(data, "%d.%m.%Y") AS data, idSala, sala, idGodziny, godzina, grupy, wykladowca FROM widokZajecia 
                WHERE id = (:idActivitie)', array(':idActivitie' => $idActivitie))[0];
            return $result;
        }

        /**
         * Umieszcza propozycję przeniesienia zajęć w bazie danych
         * @param string $date - data na którą mają zostać przeniesione zajęcia
         * @param int $idActivitie - id zajęć do przeniesienia
         * @param int $idRoom - id sali
         * @param int $idHour - id godziny
         * @param int $idInstructor - id wykładowcy
         * @return array
         */
        public function putTransfer(string $date, int $idActivitie, int $idRoom, int $idHour, int $idInstructor) {
            $result = null;
            $validInstructor = $this->getDb()->query('SELECT id FROM widokZajecia WHERE id = :idZajecia AND idWykladowca = :idWykladowca', 
                array(':idZajecia' => $idActivitie, ':idWykladowca' => $idInstructor));
            $validActivitie = $this->getDb()->query('SELECT id FROM widokPrzeniesien WHERE idZajecia = :idZajecia', 
                array(':idZajecia' => $idActivitie));
            if (!empty($validInstructor)) {
                if (empty($validActivitie)) {
                    $result = $this->getDb()->query('INSERT INTO przeniesienia (data, idZajecia, idSala, idGodzinaZajec) VALUES (:data, :idZajecia, :idSala, :idGodzinaZajec)',
                        array(':data' => $date, ':idZajecia' => $idActivitie, ':idSala' => $idRoom, ':idGodzinaZajec' => $idHour)); 
                } else {
                    $result = 'Zajęcia mają już przypisaną propozycję przeniesienia';
                }
            } else {
                $result = 'Te zajęcia nie są przez ciebie prowadzone';
            }
            return $result;
        }
    }
}