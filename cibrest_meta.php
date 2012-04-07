<?
require_once dirname(__FILE__).'/class/base_classes.php';

class CIB_REST_Meta extends Ciblenet {

	public function get(){
		return $this->get_request('meta.json') ;
	}
}
?>