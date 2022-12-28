<?php

namespace Modules\Calendar {

	class Controller extends \Engine\Controller { 

		private $date;

		protected function default() : void {
			(isset($this->date)) ?: $this->date = date('Y-m-d');
			$this->config = array(
				"date" => $this->date,
				"hourRange" => $this->model->getData(0)
			);
		}

		protected function ajaxRequest(array $post, array $access) : void {
			switch ($post['data']) {
				case 'holidays': {
					if (!empty(array_key_exists('year',$post))) {
						echo (json_encode($this->model->getHolidays($post['year'])));
					}
					break;
				}
				case 'congresses' : {
					if (!empty(array_key_exists('date', $post))) {
						$result = $this->model->getCongresses($post['date']);
						echo(json_encode($result));
					}
					break;
				}
				case 'terms' : {
					if (in_array('wykładowca',$access)) {
						if (!empty(array_key_exists('date', $post))) {
							$result = $this->model->getTerms($post['date'], $_SESSION['idWykladowcy']);
							echo(json_encode($result));
						}
						break;
					}
				}
			}
		}

		protected function postRequest(array $post, array $access) {
			if (in_array('wykładowca',$access)) {
				switch ($post['data']) {
					case 'putTerm' : {
						(!isset($post['fromDate'])) ?: $this->date = $post['fromDate'];
						if (!empty(array_key_exists('availability',$post)) && !empty(array_key_exists('fromDate',$post)) && 
							!empty(array_key_exists('toDate',$post))) {
							$result = $this->model->putTerm($post['availability'], $post['fromDate'], $post['toDate'], $_SESSION['idWykladowcy'], $post['fromHour'], $post['toHour']);
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