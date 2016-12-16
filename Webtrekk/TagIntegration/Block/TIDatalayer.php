<?php
/**
 * @author Webtrekk Team
 * @copyright Copyright (c) 2016 Webtrekk GmbH (https://www.webtrekk.com)
 * @package Webtrekk_TagIntegration
 */
namespace Webtrekk\TagIntegration\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \Webtrekk\TagIntegration\Helper\Data;
use \Webtrekk\TagIntegration\Model\DataLayer;

class TIDatalayer extends Template
{

    /**
     * @var Data
     */
    protected $_tiHelper = null;
    /**
     * @var DataLayer
     */
    protected $_dataLayerModel = null;


    /**
     * @param Context $context
     * @param Data $tiHelper
	 * @param DataLayer $dataLayer
     * @param array $data
     */
    public function __construct(Context $context, Data $tiHelper, DataLayer $dataLayer, array $data = [])
	{
        $this->_tiHelper = $tiHelper;
        $this->_dataLayerModel = $dataLayer;
		
        parent::__construct($context, $data);
    }

	
	private function removeParameterByBacklist(array $data = [])
	{
		$blacklist = $this->_tiHelper->getAttributeBlacklist();
		for($i = 0, $l = count($blacklist); $i < $l; $i++) {
			$key = $blacklist[$i];
			
			if(strpos($key, '*') !== false) {
				$keyRegExp = implode('.*', explode('*', $key));
				$matches = preg_grep('/' . $keyRegExp . '/', array_keys($data));
				foreach($matches as $k => $v) {
					unset($data[$v]);
				}
			}
			else {
				if(array_key_exists($key, $data)) {
					unset($data[$key]);
				}
			}
		}
		
		$data['blacklist'] = $blacklist;
		return $data;
	}
	
	
	
    /**
     * @return string
     */
    protected function _toHtml()
	{
        if (!$this->_tiHelper->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
	
	
    /**
     * @return array
     */
    public function getDataLayer()
	{
		$this->_dataLayerModel->setPageDataLayer();
		$this->_dataLayerModel->setProductDataLayer();
		$this->_dataLayerModel->setCustomerDataLayer();
        $this->_dataLayerModel->setCartDataLayer();
		$this->_dataLayerModel->setOrderDataLayer();
        
		$data = $this->removeParameterByBacklist($this->_dataLayerModel->getVariables());

        return json_encode($data, JSON_PRETTY_PRINT);
    }

}
