<?php

namespace Modules\Localization {

    use Singleton\Registry;
    class Controller extends \Engine\Controller {

        protected function default() : void {
            $this->config = array(
                "content" => $this->model->getData(0),
                "types" => $this->model->getData(1),
                "equipment" => $this->model->getData(2),
                "cities" => $this->model->getData(3)
            );
        }

        protected function postRequest(array $post, array $access) {
            if (in_array('admin',$access)) {
                //edycja budynku
                if (!empty($post['bulding']) && !empty($post['name']) && !empty($post['selectCity']) && !empty($post['street']) && !empty($post['addressNumber']) && isset($post['description'])) {
                    $this->model->editBulding($post['bulding'], $post['name'], $post['selectCity'], $post['street'], $post['addressNumber'], $post['description']);
                } else 
                //dodawanie budynku
                if (!empty($post['name']) && !empty($post['selectCity']) && !empty($post['street']) && !empty($post['addressNumber']) && isset($post['description'])) {
                    $this->model->addBulding($post['name'], $post['selectCity'], $post['street'], $post['addressNumber'], $post['description']);
                } else 
                //edytowanie sali
                if (!empty($post['room']) && !empty($post['number']) && !empty($post['selectType']) && !empty($post['numberSeats']) && isset($post['description'])) {
                    $equipment = (isset($post['selectEquipment'])) ? $post['selectEquipment'] : [];
                    $this->model->editRoom($post['room'], $post['number'], $post['selectType'], $post['numberSeats'], $post['description'], $equipment);
                } else 
                //dodawanie sali
                if (!empty($post['bulding']) && !empty($post['number']) && !empty($post['selectType']) && !empty($post['numberSeats']) && isset($post['description'])) {
                    $equipment = (isset($post['selectEquipment'])) ? $post['selectEquipment'] : [];
                    $this->model->addRoom($post['bulding'], $post['number'], $post['selectType'], $post['numberSeats'], $post['description'], $equipment);
                } else 
                //}
                //if (access == 'remove) {
                //usuwanie budynku
                if (!empty($post['bulding'])) {
                    $this->model->deleteBulding($post['bulding']);
                } else 
                //usuwanie sali
                if (!empty($post['room'])) {
                    $this->model->deleteRoom($post['room']);
                }
            }
        }

        protected function ajaxRequest(array $post, array $access) : void {
            if (in_array('admin',$access)) {
                if (!empty($post['idBulding'])) {
                    echo(json_encode($this->model->getBulding($post['idBulding'])));
                } else
                if (!empty($post['idRoom'])) {
                    echo(json_encode($this->model->getRoom($post['idRoom'])));
                }
            }
        }
    }
}