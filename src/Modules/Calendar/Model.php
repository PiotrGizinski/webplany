<?php

namespace Modules\Calendar {
    
    use Singleton\Registry;
    use DateTimeImmutable;
	use DateInterval;

    class Model extends \Engine\Model {

        public function init() : void {
            $result = $this->getDb()->query('SELECT min(godzinaRoz) AS min, max(godzinaZak) AS max FROM widokGodzinZajeciowych');
            (!isset($result[0])) ?: $this->setData($result[0]);
        }

        /**
		 * Generuje święta
		 * @param string $year - rok
		 * @return array
		 */
		public function getHolidays(string $year) : array {
			//Pobieranie świąt stałych
			$result = $this->getDb()->query('SELECT CONCAT(:rok,"-",data) AS data, nazwa FROM swieta WHERE wolne = 1', array(':rok' => $year));

			//Wyznaczanie daty Wielkanocy
			$holidays = array();
			$easterDate = new DateTimeImmutable(date("Y-m-d", easter_date($year)));

			//Wyznaczanie daty świąt ruchomych
			$tmp = $easterDate->format('Y-m-d');
			array_push($holidays, array('data' => $tmp, 'nazwa' => 'Wielkanoc'));

			$tmp = $easterDate->add(new DateInterval('P1D'))->format('Y-m-d');
			array_push($holidays, array('data' => $tmp, 'nazwa' => 'Poniedziałek Wielkanocny'));

			$tmp = $easterDate->add(new DateInterval('P49D'))->format('Y-m-d');
			array_push($holidays, array('data' => $tmp, 'nazwa' => 'Zielone Świątki'));

			$tmp = $easterDate->add(new DateInterval('P60D'))->format('Y-m-d');
			array_push($holidays, array('data' => $tmp, 'nazwa' => 'Boże Ciało'));

			return array_merge($result, $holidays);
		}

        /**
         * Pobiera i zwraca zajęcia
         * @param string $date - data
         * @return array
         */
        public function getCongresses($date) : array {
            return $this->getDb()->query('SELECT id, dataRoz, dataZak, wydzial, liczbaDni FROM widokZjazdy WHERE DATE_FORMAT(dataRoz,"%Y%m") = 
                DATE_FORMAT(:dataRoz,"%Y%m") OR DATE_FORMAT(dataZak,"%Y%m") = DATE_FORMAT(:dataZak,"%Y%m")', array(':dataRoz' => $date, 
                ':dataZak' => $date));
        }

        /**
         * Pobiera i zwraca terminy dostępności danego wykładowcy
         * @param string $date - data
         * @param int $idInstructor - id wykładowcy
         * @return array
         */
        public function getTerms(string $date, int $idInstructor) : array {
            return $this->getDb()->query('SELECT id, dostepnosc, liczbaDni, dataOd, godzinaOd, dataDo, godzinaDo, wykladowca FROM widokTerminy 
                WHERE (DATE_FORMAT(dataOd,"%Y%m") = DATE_FORMAT(:dataOd,"%Y%m") OR DATE_FORMAT(dataDo,"%Y%m") = DATE_FORMAT(:dataDo,"%Y%m")) AND 
                idWykladowca = :idWykladowca', array(':dataOd' => $date, ':dataDo' => $date, ':idWykladowca' => $idInstructor));
        }

        /**
         * Umieszcza termin dostępności
         * @param string $availability - dostępność (true/false)
         * @param string $fromDate - od kiedy 
         * @param string $toDate - do kiedy
         * @param int $idInstructor - id wykładowcy
         * @param string $fromHour - od godziny
         * @param string $toHour - do godziny
         * @return mixed ???
         */
        public function putTerm(string $availability, string $fromDate, string $toDate, int $idInstructor, string $fromHour, string $toHour) {
            //Sprawdzenie czy termin znajduje się w dowolnym semestrze
            $idSemester = $this->getDb()->query('SELECT id FROM semestr WHERE (:dataOd between dataRoz AND dataZak) AND (:dataDo between dataRoz 
                AND dataZak) ', array(':dataOd' => $fromDate, ':dataDo' => $toDate));

            //Sprawdzenie czy nie znajduje się już termin dostępności w wybranym terminie
            $isSet = $this->getDb()->query('SELECT id FROM terminyWykladowcow WHERE idWykladowca = :idWykladowca AND ((:dataOd between dataOd AND dataDo) 
                OR (:dataDo between dataOd AND dataDo)) ', array(':dataOd' => $fromDate, ':dataDo' => $toDate, ':idWykladowca' => $idInstructor));
            
            if (!isset($idSemester[0]['id'])) {
                return 'Zakres nie znajduję się w semestrze';
            } else if (isset($isSet[0]['id'])) {
                return 'W tym zakresie okręśliłeś już dostępność';
            } else {
                $availability = ($availability == 'true') ? 1 : 0;
                //Sprawdzanie czy ustawiono zakres godzin i czy jest prawidłowy
                if (empty($fromHour) || empty($toHour)) {
                    $fromHour = null;
                    $toHour = null;
                } else if ($fromHour > $toHour) {
                    return 'Zakres godzin jest nieprawidłowy';
                }
                //Umieszczanie terminu w bazie danych
                return $this->getDb()->query('INSERT INTO terminyWykladowcow (dostepnosc, dataOd, godzinaOd, dataDo, godzinaDo, idWykladowca, idSemestr) VALUES (:dostepnosc, :dataOd, 
                    :godzinaOd, :dataDo, :godzinaDo, :idWykladowca, :idSemestr)', array(':dostepnosc' => $availability, ':dataOd' => $fromDate, ':godzinaOd' 
                    => $fromHour, ':godzinaDo' => $toHour, ':dataDo' => $toDate, ':idWykladowca' => $idInstructor, ':idSemestr' => $idSemester[0]['id'])); 
            } 
        }
    }
}