<?php
class Cammino_Apijson_StockController extends Mage_Core_Controller_Front_Action{
	
	private $helper;
	private $model;

	protected function _initAction() {
		$this->helper = Mage::helper('apijson');
		$this->model = Mage::getModel('apijson/stock');
		$this->helper->validateRequest();
	}

	protected function editAction() {
		$this->_initAction();
		$stocks = $this->helper->getRequestData();
		$this->model->editStock($stocks);
	}
}