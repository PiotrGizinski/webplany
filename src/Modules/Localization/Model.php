<?php

namespace Modules\Localization;

class Model extends \Engine\Model {

    public function init() : void {
        $this->setData($this->getBuldingsWithRooms());
        $this->setData($this->getDb()->query('SELECT id, nazwa FROM typSali'));
        $this->setData($this->getDb()->query('SELECT id, nazwa FROM sprzet'));
        $this->setData($this->getDb()->query('SELECT id, CONCAT(nazwa,", ",kod) AS nazwa FROM miasto'));
    }

    /**
     * Zwraca dane budynków oraz dane sal do nich przypisane
     * @return array
     */
    private function getBuldingsWithRooms() : array {
        $result = $this->getDb()->query('SELECT * FROM widokBudynki ORDER BY id');
        foreach ($result as $key=>$row) {
            $records = $this->getDb()->query('SELECT id, Numer, Typ_sali, Ilosc_miejsc, Opis FROM widokSale WHERE 
                idBudynek = :idBudynek', array(':idBudynek' => $row['id']));
            $result[$key]['Sale'] = $records;
            foreach ($result[$key]['Sale'] as $subKey=>$subRow) {
                $subRecords = $this->getDb()->query('SELECT id, Nazwa, Ilosc, Opis FROM widokSaleSprzet WHERE idSala = :idSala', array(':idSala' => $subRow['id']));
                $result[$key]['Sale'][$subKey]['Sprzet'] = $subRecords;
            }
        };
        return $result;
    }

    /**
     * Zwraca dane budynku
     * @param int $id - id budynku
     * @return array
     */
    public function getBulding(int $id) : array {
        return $this->getDb()->query('SELECT nazwa, ulica, numer, idMiasto, opis FROM budynek WHERE id = :id', array(':id' => $id))[0];
    }

    /**
     * Zwraca dane sali i przypisane im sprzęty dla zadanego id
     * @param int $id - id sali
     * @return array
     */
    public function getRoom(int $id) : array {
        $result = $this->getDb()->query('SELECT numer, idTypSali as idTyp, IloscMiejsc as miejsca, opis FROM sala WHERE id = :id', array(':id' => $id))[0];
        $result['sprzet'] = $this->getDb()->query('SELECT idSprzet, ilosc FROM wyposazenie WHERE idSala = :idSala', array(':idSala' => $id));
        return $result;
    }
    

    /**
     * Metoda dodające budynek
     * @param string $name - nazwa budynku
     * @param int $idCity - id miasta
     * @param string $street - nazwa ulicy
     * @param string $addressNumber - numer adresu
     * @param string $description - opis budynku
     * @return void - powinna zwracać
     */
    public function addBulding(string $name, int $idCity, string $street, string $addressNumber, string $description) : void {
        $result = $this->getDb()->query('INSERT INTO budynek (nazwa, ulica, numer, idMiasto, opis) VALUES (:nazwa, :ulica, :numer, :idMiasto, :opis)',
            array(':nazwa' => $name, ':ulica' => $street, ':numer' => $addressNumber, ':idMiasto' => $idCity, ':opis' => $description));
    }

    /**
     * Metoda edytująca budynek o zadanym id
     * @param int $id - id budynku
     * @param string $name - nazwa budynku
     * @param int $idCity - id miasta
     * @param string $street - nazwa ulicy
     * @param string $addressNumber - numer adresu
     * @param string $description - opis budynku
     * @return void - powinna zwracać
     */
    public function editBulding(int $id, string $name, int $idCity, string $street, string $addressNumber, string $description) : void {
        $result = $this->getDb()->query('UPDATE budynek SET nazwa = :nazwa, ulica = :ulica, numer = :numer, idMiasto = :idMiasto, opis = :opis WHERE id = :id',
            array(':id' => $id, ':nazwa' => $name, ':ulica' => $street, ':numer' => $addressNumber, ':idMiasto' => $idCity, ':opis' => $description));
    }

    /**
     * Metoda usuwająca budynek o zadanym id
     * Rozwarzyć ukrywanie sali, aby zajęcia w usunietych salach się nie posypały
     * @param int $id - id budynku
     * @return void - powinna zwracać efekt usunięcia
     */
    public function deleteBulding(int $id) : void {
        $this->getDb()->query('DELETE FROM budynek WHERE id = :id', array(':id' => $id));
        foreach ($this->getDb()->query('SELECT id FROM sala WHERE idBudynek = :idBudynek', array(':idBudynek' => $id)) as $row) {
            $this->getDb()->query('DELETE FROM wyposazenie WHERE idSala = :idSala', array(':idSala' => $row['id']));
        }
        $this->getDb()->query('DELETE FROM sala WHERE idBudynek = :idBudynek', array(':idBudynek' => $id));
    }

    /**
     * Metoda dodająca salę
     * @param int $idBulding - id budynku
     * @param string $number - numer sali
     * @param int $idType - typ sali
     * @param int $numberSeats - liczba miejsc w sali
     * @param string $description - opis sali
     * @param array $equipment - id-ki wyposazenia
     * @return void - powinna zwracać
     */
    public function addRoom(int $idBulding, string $number, int $idType, int $numberSeats, string $description, array $equipment) : void {
        //$this->db->query('INSERT INTO sala (idBudynek, numer, idTypSali, iloscMiejsc, opis) VALUES (:idBudynek, :numer, :idTypSali, :iloscMiejsc, :opis)',
        //    array(':idBudynek' => $idBulding, ':numer' => $number, ':idTypSali' => $idType, ':iloscMiejsc' => $numberSeats, ':opis' => $description));
        $result = $this->getDb()->query('INSERT INTO sala (idBudynek, numer, idTypSali, iloscMiejsc, opis) VALUES (:idBudynek, :numer, :idTypSali, :iloscMiejsc, :opis)',
            array(':idBudynek' => $idBulding, ':numer' => $number, ':idTypSali' => $idType, ':iloscMiejsc' => $numberSeats, ':opis' => $description));
        $id = $this->getDb()->query('SELECT id FROM sala WHERE idBudynek = :idBudynek AND numer = :numer', 
            array(':idBudynek' => $idBulding, ':numer' => $number))[0]['id'];
        foreach ($equipment as $row) {
            $result = $this->getDb()->query('INSERT INTO wyposazenie (idSala, idSprzet, ilosc) VALUES (:idSala, :idSprzet, ilosc)',
                array(':idSala' => $id, 'idSprzet' => $row, 'ilosc' => $row['number'], 'ilosc' => $row['number']));
        }
    }

    /**
     * Metoda edytująca salę
     * @param int $id - id sali
     * @param int $idBulding - id budynku
     * @param string $number - numer sali
     * @param int $idType - typ sali
     * @param int $numberSeats - liczba miejsc w sali
     * @param string $description - opis sali
     * @param array $equipment - id-ki wyposazenia
     * @return void - powinna zwracać
     */
    public function editRoom(int $id, string $number, int $idType, int $numberSeats, string $description, array $equipment) : void {
        $result = $this->getDb()->query('UPDATE sala SET numer=:numer, idTypSali=:idTypSali, iloscMiejsc=:iloscMiejsc, opis=:opis WHERE id = :id',
            array(':id' => $id, ':numer' => $number, ':idTypSali' => $idType, ':iloscMiejsc' => $numberSeats, ':opis' => $description));
        $this->getDb()->query('DELETE FROM wyposazenie WHERE idSala = :idSala', array(':idSala' => $id));
        foreach ($equipment as $row) {
            $result = $this->getDb()->query('INSERT INTO wyposazenie (idSala, idSprzet, ilosc) VALUES (:idSala, :idSprzet, ilosc)',
                array(':idSala' => $id, ':idSprzet' => $row['id'], 'ilosc' => $row['number']));
        }
    }

    /**
     * Metoda usuwająca sale o zadanym id
     * Rozwarzyć ukrywanie sali, aby zajęcia w usunietych salach się nie posypały
     * @return void - powinna zwracać
     */
    public function deleteRoom(int $id) : void {
        $this->getDb()->query('DELETE FROM sala WHERE id = :id', array(':id' => $id));
        $this->getDb()->query('DELETE FROM wyposazenie WHERE idSala = :idSala', array(':idSala' => $id));
    }
}