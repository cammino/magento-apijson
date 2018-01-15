<?php
class Cammino_Apijson_Model_Stock extends Mage_Core_Model_Abstract{

	private $helper;

	public function __construct(){
		$this->helper = Mage::helper('apijson');
	}

	public function editStock($stocks){
		$hasWarning = false;
		$hasWarningMessage = array();
		
		try{
			foreach ($stocks as $stock) {
				$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $stock->sku);
				
				if(!$product){
					$hasWarning = true;
					$hasWarningMessage[] = 'invalid sku: ' . $stock->sku;
					continue;
				}

				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
				if ($stockItem->getId() > 0 and $stockItem->getManageStock()) {
					$qty = $stock->qty;
					$stockItem->setQty($qty);
					$stockItem->setIsInStock((int)($qty > 0));
					$stockItem->save();
				}else{
					$hasWarning = true;
					$hasWarningMessage[] = 'invalid stock item for sku: ' . $stock->sku;
					continue;
				}
			}

			if(!$hasWarning){
				$this->helper->returnRequest('success');
			}else{
				$this->helper->returnRequest('warning', $hasWarningMessage);
			}

		}catch(Exception $e) {
			$this->helper->returnRequest('error', $e->getMessage());
		}
	}
}