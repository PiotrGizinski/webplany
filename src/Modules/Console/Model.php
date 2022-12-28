<?php

namespace Modules\Console {

use Singleton\Registry;

class Model extends \Engine\Model {

        public function init() : void { }

        public function getUsers() : array {
            return $this->getDb()->query('SELECT konto, rola FROM widokKont');
        }

        public function getRoles() : array {
            return $this->getDb()->query('SELECT nazwa FROM rola');
        }
    }
}