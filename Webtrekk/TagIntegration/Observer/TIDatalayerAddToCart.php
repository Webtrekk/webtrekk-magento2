<?php
/**
 * @author Webtrekk Team
 * @copyright Copyright (c) 2016 Webtrekk GmbH (https://www.webtrekk.com)
 * @package Webtrekk_TagIntegration
 */
namespace Webtrekk\TagIntegration\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Checkout\Model\Session;
use \Webtrekk\TagIntegration\Helper\Data;
use \Webtrekk\TagIntegration\Model\Data\Product;
use \Webtrekk\TagIntegration\Helper\DataLayer as DataLayerHelper;

class TIDatalayerAddToCart implements ObserverInterface {
	
	/**
     * @var Session
     */
    protected $_checkoutSession;
	/**
     * @var Data
     */
    protected $_tiHelper;
	/**
     * @var Product
     */
	protected $_product;
	
	
	/**
	 * @param Session $checkoutSession
     * @param Data $tiHelper
	 * @param Product $product
     */
    public function __construct(Session $checkoutSession, Data $tiHelper, Product $product)
	{
		$this->_checkoutSession = $checkoutSession;
		$this->_tiHelper = $tiHelper;
		$this->_product = $product;
    }
	
	
	/**
	 * @param Observer $observer
     */
	public function execute(Observer $observer)
	{
		if($this->_tiHelper->isEnabled()) {
			$item = $observer->getEvent()->getData('quote_item');
			// $item = (($item->getParentItem()) ? $item->getParentItem() : $item);
			$product = $observer->getEvent()->getData('product');
				
			if($product) {
				$this->_product->setProduct($product);
				$productData = $this->_product->getDataLayer();
				$productData['qty'] = intval($item->getQtyToAdd());
				$productData['quantity'] = intval($item->getQtyToAdd());
				$productData['status'] = 'add';
			
				if(!$productData['price']) {
					$productData['price'] = $item->getPrice();
					$productData['cost'] = $item->getPrice();
				}
				
				$existingProductData = $this->_checkoutSession->getData('webtrekk_add_product');
				if(!$existingProductData) {
					$existingProductData = [];
				}
				
				$productDataMerge = DataLayerHelper::merge($existingProductData, $productData);
				$this->_checkoutSession->setData('webtrekk_add_product', $productDataMerge);
			}
		}
	}
	
}