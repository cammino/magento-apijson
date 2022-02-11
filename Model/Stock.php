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

				$price = $stock->price;
				$special_price = $stock->special_price;

				if (!empty(strval($qty))) {
					$product->setPrice($price);
				}

				if (!empty(strval($special_price))) {
					$product->setSpecialPrice($special_price);
				}

				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());

				if (($stockItem->getId() > 0) && $stockItem->getManageStock()) {

					$qty = $stock->qty;
					
					if (!empty(strval($qty))) {
						$stockItem->setQty($qty);
						$stockItem->setIsInStock((int)($qty > 0));
					}
					
					$stockItem->save();

				} else {
					$hasWarning = true;
					$hasWarningMessage[] = 'invalid stock for sku: ' . $stock->sku;
					continue;
				}

				$product->save();

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