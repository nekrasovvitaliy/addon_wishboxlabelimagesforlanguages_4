<?php
	// 
	defined('_JEXEC') or die;
	
	
	// 
	// 
	use Joomla\CMS\Plugin\CMSPlugin;
	
	/**
	 *
	 */
	class plgJShoppingProductsWishBoxProductsLabelImagesForLanguages extends CMSPlugin
	{
		/**
		 *
		 */
		protected $autoloadLanguage = true;
		
		
		/**
		 *
		 */
		public function __construct(&$subject, $config)
		{
			// 
			parent::__construct($subject, $config);
		}
		
		
		
		/**
		 *
		 */
		public function onListProductUpdateData(&$products)
		{
			// 
			// 
			$jshopConfig = JSFactory::getConfig();
			// 
			// 
			$lang = JSFactory::getLang();
			// 
			// 
			if ($jshopConfig->defaultLanguage == $lang->lang)
			{
				// 
				// 
				return;
			}
			// 
			// 
			$jshopConfig = JSFactory::getConfig();
			// 
			// 
			if (is_array($products) && count($products))
			{
				// 
				// 
				foreach ($products as $key => $value)
				{
					// 
					// 
					if ($products[$key]->label_id)
					{
						// 
						// 
						$image = self::getNameImageLabel($products[$key]->label_id);
						// 
						// 
						if ($image)
						{
							// 
							// 
							$products[$key]->_label_image = $jshopConfig->image_labels_live_path.'/'.$image;
						}
					}
				}
			}
		}
		
		
		/**
		 *
		 */
		public function onBeforeDisplayProductView(&$view)
		{
			// 
			// 
			$jshopConfig = JSFactory::getConfig();
			// 
			// 
			$lang = JSFactory::getLang();
			// 
			// 
			if ($jshopConfig->defaultLanguage == $lang->lang)
			{
				// 
				// 
				return;
			}
			// 
			// 
			if ($view->product->label_id)
			{
				// 
				// 
				$image = self::getNameImageLabel($view->product->label_id);
				// 
				// 
				if ($image)
				{
					// 
					// 
					$view->product->_label_image = $jshopConfig->image_labels_live_path.'/'.$image;
				}
			}
		}
		
		
		/**
		 *
		 */
		private static function getNameImageLabel($id)
		{
			// 
			// 
			static $listLabels;
			// 
			// 
			$jshopConfig = JSFactory::getConfig();
			// 
			// 
			if (!$jshopConfig->admin_show_product_labels)
			{
				// 
				// 
				return '';
			}
			// 
			// 
			if (!is_array($listLabels))
			{
				// 
				// 
				$listLabels = self::getListLabels();
			}
			// 
			// 
			$obj = $listLabels[$id];
			// 
			// 
			return $obj->image;
		}
		
		
		/**
		 *
		 */
		private static function getListLabels()
		{
			// 
			// 
			$lang = JSFactory::getLang();
			// 
			// 
			$db = JFactory::getDBO();
			// 
			// 
			$query = "SELECT id, `".$lang->get("image")."` as image FROM `#__jshopping_product_labels` ORDER BY name";
			// 
			// 
			$db->setQuery($query);
			// 
			// 
			$list = $db->loadObjectList();
			// 
			// 
			$rows = [];
			// 
			// 
			foreach($list as $row)
			{
				// 
				// 
				$rows[$row->id] = $row;
			}
			// 
			// 
			return $rows;
		}
	}