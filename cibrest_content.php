<?php
require_once dirname(__FILE__).'/class/base_classes.php';

class CIB_REST_Content extends Ciblenet {

	public function get($name){
		return $this->get_request('content.json?name='.urlencode($name));
	}

}
?>