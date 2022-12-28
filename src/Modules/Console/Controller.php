<?php

namespace Modules\Console {

    use Singleton\Registry;

    class Controller extends \Engine\Controller {

        protected function default() : void {
            $this->config = array(
                'content' => array(
                    'settings' => Registry::getInstance()->getApplicationHelper()->getconfig('global'),
                    'components' => Registry::getInstance()->getApplicationHelper()->getconfig('objects'),
                    'users' => $this->model->getUsers(),
                    'roles' => $this->model->getRoles()
                )
            );
        }

        protected function postRequest($post, $access) : void { }

        protected function ajaxRequest($post, $access) : void { }
    }
}