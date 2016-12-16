<?php
/**
 * @author Webtrekk Team
 * @copyright Copyright (c) 2016 Webtrekk GmbH (https://www.webtrekk.com)
 * @package Webtrekk_TagIntegration
 */
namespace Webtrekk\TagIntegration\Model\Data;

use \Magento\Framework\App\Action\Context;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Catalog\Helper\Data as CatalogHelper;
use \Magento\Framework\Locale\Resolver;
use \Magento\Framework\View\Page\Title;
use \Magento\CatalogSearch\Helper\Data as CatalogSearchHelper;
use \Magento\CatalogSearch\Model\Advanced;

class Page extends AbstractData
{
	
	/**
     * @var Context
     */
    protected $_context;
	/**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
	/**
     * @var CatalogData
     */
    protected $_catalogData;
	/**
     * @var Resolver
     */
    protected $_resolver;
	/**
     * @var \Title
     */
    protected $_title;
	/**
     * @var CatalogSearchData
     */
	protected $_normalSearch;
	/**
     * @var Advanced
     */
	protected $_advancedSearch;
	
	
	/**
	 * @param Context $context
     * @param StoreManagerInterface $storeManager
	 * @param CatalogHelper $catalogData
	 * @param Resolver $resolver
	 * @param Title $title
	 * @param CatalogSearchHelper $normalSearch
	 * @param Advanced $advancedSearch
     */
	public function __construct(Context $context, StoreManagerInterface $storeManager, CatalogHelper $catalogData, Resolver $resolver, Title $title, CatalogSearchHelper $normalSearch, Advanced $advancedSearch)
	{
		$this->_context = $context;
		$this->_storeManager = $storeManager;
		$this->_catalogData = $catalogData;
		$this->_resolver = $resolver;
		$this->_title = $title;
		$this->_normalSearch = $normalSearch;
		$this->_advancedSearch = $advancedSearch;
	}
	
	
	private function setStore()
	{
		$store = $this->_storeManager->getStore();
		$this->set('storeFrontendName', $store->getFrontendName());
		$this->set('storeName', $store->getName());
		$this->set('storeId', $store->getId());
	}
	
	
	private function setBasic()
	{
		$request = $this->_context->getRequest();
		if($request) {
			$this->set('action', $request->getFullActionName());
			$this->set('route', $request->getRouteName());
		}
	}
	
	
	private function setLanguage()
	{
		$locale = $this->_resolver->getLocale();
		if($locale) {
			$this->set('locale', $locale);
			$this->set('language', explode('_', $locale)[0]);
		}
	}
	
	
	private function setPageTitle()
	{
		$this->set('title', $this->_title->getShort());
	}
	
	
	private function setNormalSearch()
	{
		$searchTerm = $this->_normalSearch->getEscapedQueryText();
		if($searchTerm) {
			$this->set('searchType', 'normal');
			$this->set('searchTerm', htmlspecialchars_decode($searchTerm));
		}
	}
	
	
	private function setAdvancedSearch()
	{
		$advancedSearchCriterias = $this->_advancedSearch->getSearchCriterias();
		if($advancedSearchCriterias) {
			$advancedSearchTerm = [];
			foreach($advancedSearchCriterias as $advancedSearchCriteria) {
				$advancedSearchTerm[] = $advancedSearchCriteria['value'];
			}
			
			$this->set('searchType', 'advanced');
			$this->set('searchTerm', implode('.', $advancedSearchTerm));
			$this->set('searchResults', $this->_advancedSearch->getProductCollection()->getSize());	
		}
	}
	
	
	private function setCategory()
	{
		$pageAction = $this->get('action');
		if($pageAction) {
			$pageActions = preg_split('/[_-]/', $pageAction);
			$counter = 1;
			$categories = [
				$this->get('language')
			];
			
			for($i = 0; $i < count($pageActions); $i++) {
				if($pageActions[$i] && $pageActions[$i] !== 'index') {
					$this->set('category' . $counter, ucfirst($pageActions[$i]));
					$categories[] = ucfirst($pageActions[$i]);
					$counter++;
				}
			}
			
			$categories[] = $this->get('title');
			$this->set('contentId', implode('.', $categories));
		}
	}
	
	
	private function setSearch()
	{
		$this->setNormalSearch();
		$this->setAdvancedSearch();
	}
	
	
	private function generate()
	{
		$this->setBasic();
		$this->setStore();
		$this->setLanguage();
		$this->setPageTitle();
		$this->setCategory();
		$this->setSearch();
	}


	/**
	 * @return array
	 */
	public function getDataLayer()
	{
		$this->generate();
		
		return $this->_data;
	}
	
}