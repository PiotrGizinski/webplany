<?php

namespace Component {

	use Singleton\Registry;
	use PDO;
	use PDOException;
    use PDOStatement;

	/**
	 * Klasa do połączenia się z bazą danych i pobierania z niej danych.
	 */
	final class Database extends \Engine\Component{

		/**
		 * Zmienna przechowująca połączenie PDO
		 * @var PDO
		 */
		private $pdo;

		/**
		 * Funkcja tworząca połączenie PDO
		 */
		protected function init() : void {
			try {
				$this->pdo = new PDO("{$this->getConfig('database')}:host={$this->getConfig('host')};port={$this->getConfig('port')};dbname={$this->getConfig('dbname')};
					charset={$this->getConfig('charset')}", $this->getConfig('user'), $this->getConfig('password'), [
						PDO::ATTR_EMULATE_PREPARES => false,
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
				]);
			} catch (PDOException $e) {
				Registry::getInstance()->getExceptionHandler()->catchException($e);
			}
		}	

		/**
		 * Funkcja sprawdzająca typ zapytania i przetwarzajaca wynik zapytania do bazy danych
		 * @param string $sql Zapytanie SQL
		 * @param array $parameters Parametry zapytania SQL
		 * @return mixed Zwraca wynik zapytania SQL
		 */
		public function query($sql, $parameters = array()) {
			try {
                if ($this->pdo) {
                    if (preg_match('/^SELECT/', $sql)) {
                        $query = $this->bindQuery($sql, $parameters);
						$result = $query->fetchAll(PDO::FETCH_ASSOC);
                        return $result;
                    } elseif (preg_match('/^INSERT/', $sql)) {
						$this->bindQuery($sql, $parameters);
						$result = $this->pdo->query('SELECT LAST_INSERT_ID() as id'); 
						return $result->fetch()['id'];
					} elseif (preg_match('/^UPDATE/', $sql)) {
                        return $this->bindQuery($sql, $parameters)->rowCount();
                    } elseif (preg_match('/^DELETE/', $sql)) {
                        return $this->bindQuery($sql, $parameters)->rowCount();
                    } elseif (preg_match('/^SHOW COLUMNS/', $sql)) {
                        $query = $this->bindQuery($sql, $parameters);
                        if ($query->rowCount() > 1) {
                            $result = $query->fetchAll();
                        } else {
							$result = $query->fetch();
                        }
                        return $result;
                    } else {
                        throw new PDOException('Złe zapytanie SQL(Niedozwolone)');
                    }
                } else {
					return array();
				}
			} catch (PDOException $e) {
				Registry::getInstance()->getExceptionHandler()->catchException($e);
			}
		}

		/**
		 * Funkcja wykonująca zapytanie SQL do bazy danych
		 * @param string $sql Zapytanie SQL
		 * @param array $parameters Parametry zapytania SQL
		 * @return PDOStatement Zwraca obiekt zapytania SQL
		 */
		private function bindQuery(string $sql, array $parameters) : PDOStatement {
			if (empty($parameters)) {
				$query = $this->pdo->query($sql);
				return $query;
			} else {				
				$query = $this->pdo->prepare($sql);
				foreach ($parameters as $name => $value) {
					if (gettype($value) === "array") {
						$value = implode(',', $value);
					}
					$query->bindValue($name, $value, PDO::PARAM_STR);
				}
				$query->execute();
				return $query;
			}
		}
	}
}

?>