<?php
class Cammino_Apijson_Helper_Data extends Mage_Core_Helper_Abstract{

	public function getRequestData(){
		$request = file_get_contents('php://input');
		$request = preg_replace('/("(.*?)"|(\w+))(\s*:\s*)\+?(0+(?=\d))?(".*?"|.)/s', '"$2$3"$4$6', $request);
		return json_decode($request);
	}

	public function returnRequest($status, $message = ""){
		if($status == 'success'){
			$data = array('status' => 'success');
		}else{
			$data = array('status' => $status, 'message' => $message);
		}
		echo json_encode($data);
		die();
	}

	public function validateRequest(){
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header('WWW-Authenticate: Basic realm="My Realm"');
			header('HTTP/1.0 401 Unauthorized');
			$this->returnRequest('error','username/password not informed');
		} else {
			$user = $_SERVER['PHP_AUTH_USER'];
			$pass = $_SERVER['PHP_AUTH_PW'];

			$api = Mage::getModel('api/user');
			if(!$api->authenticate($user, $pass)){
				$this->returnRequest('error','unauthorized user');
			}
		}
	}
}