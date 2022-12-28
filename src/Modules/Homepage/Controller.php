<?php

namespace Modules\Homepage {

    use Singleton\Registry;
    
    class Controller extends \Engine\Controller {
    
        protected function default() : void {
            if (Registry::getInstance()->getAuthorization()->getLogged()) {
                $this->config = array(
                    "role" => Registry::getInstance()->getAuthorization()->getRole(),
                    "name" => "Jan",
                    "surname" => "Kowalski"
                );
            }
        }

        protected function postRequest($post, $access) : void { }

        protected function ajaxRequest($post, $access) : void { }
    }
}