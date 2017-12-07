<?php
class Cammino_Apijson_Model_Stock extends Mage_Core_Model_Abstract{

	private $helper;

	public function __construct(){
		$this->helper = Mage::helper('apijson');
	}

	public function editStock($stocks){
		try{
			foreach ($stocks as $stock) {
				$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $stock->sku);
				
				if(!$product){
					$this->helper->returnRequest('0', 'invalid sku: ' . $stock->sku);
				}

				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
				if ($stockItem->getId() > 0 and $stockItem->getManageStock()) {
					$qty = $stock->qty;
					$stockItem->setQty($qty);
					$stockItem->setIsInStock((int)($qty > 0));
					$stockItem->save();
				}else{
					$this->helper->returnRequest('1', 'invalid stock item for sku: ' . $stock->sku);
				}
			}
			$this->helper->returnRequest('1', 'success');
		}catch(Exception $e) {
			$this->helper->returnRequest('0', $e->getMessage());
		}
	}
}