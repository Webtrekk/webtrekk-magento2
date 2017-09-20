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

class TICode extends Template
{
    /**
     * @var Data
     */
    protected $_tiHelper = null;
	

    /**
     * @param Context $context
     * @param Data $tiHelper
     * @param array $data
     */
    public function __construct(Context $context, Data $tiHelper, array $data = [])
	{
        $this->_tiHelper = $tiHelper;
		
        parent::__construct($context, $data);
    }
	
	
	/**
     * @return array
     */
	public function getTagIntegrationConfig()
	{
		return $this->_tiHelper->getTagIntegrationConfig();
	}
	

    /**
     * @return string
     */
    protected function _toHtml()
	{
        if(!$this->_tiHelper->isEnabled()) {
            return '';
        }
		
        return parent::_toHtml();
    }

}
