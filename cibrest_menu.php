<?
require_once dirname(__FILE__).'/class/base_classes.php';

class CIB_REST_Menu extends Ciblenet {

	public function get(){
		return $this->get_request('menu.json');
	}

}

?>