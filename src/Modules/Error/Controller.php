<?php

namespace Modules\Error {

    use Singleton\Registry;


    class Controller extends \Engine\Controller {

        protected function default() : void {
            $this->config = array(
                'info' => Registry::getInstance()->getRequest()->getFeedback(),         //co z tym zrobić???
                'errorCode' => Registry::getInstance()->getRequest()->getParameter('errorCode')    //co z tym zrobić???
            );
        }

        protected function postRequest($post, $access) : void {

        }

        protected function ajaxRequest($post, $access) : void {
        
        }
    }
}