<?
require_once dirname(__FILE__).'/class/base_classes.php';

class CIB_REST_Page extends Ciblenet {

	var $_page_base_route;
  

	public function get(){
		return $this->get_request('page.json') ;
	}
	
	public function get_active_page(){
		return $this->get_request('page/active.json', array('uri' => $_SERVER['REQUEST_URI'])) ;
	}
}

?>