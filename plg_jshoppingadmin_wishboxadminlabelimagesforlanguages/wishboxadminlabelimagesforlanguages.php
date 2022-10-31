<?php
	// 
	defined('_JEXEC') or die;
	
	// 
	// 
	include_once JPATH_SITE.'/components/com_jshopping/bootstrap.php';
	// 
	// 
	use Joomla\CMS\Plugin\CMSPlugin;
	// 
	// 
	use Joomla\Component\Jshopping\Site\Lib\UploadFile;
	
	// 
	// 
	JLoader::registerAlias('JSFactory', 'Joomla\\Component\\Jshopping\\Site\\Lib\\JSFactory');
	
	
	/**
	 *
	 */
	class plgJShoppingAdminWishBoxAdminLabelImagesForLanguages extends CMSPlugin
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
			// 
			parent::__construct($subject, $config);
		}
		
		
		/**
		 *
		 */
		public function onAjaxWishboxadminlabelimagesforlanguages()
		{
			// 
			// 
			$app = JFactory::getApplication();
			// 
			// 
			$task = $app->input->getVar('task', '');
			// 
			// 
			if ($task == 'delete_foto_lang')
			{
				// 
				// 
				$lang = JSFactory::getModel('languages');
				// 
				// 
				$jshopConfig = JSFactory::getConfig();
				// 
				// 
				$id = $app->input->getInt('id', 0);
				// 
				// 
				if ($id <= 0)
				{
					// 
					// 
					throw new InvalidArgumentException('id param must be more than zero', 400);
				}
				// 
				// 
				$lang_tag = $app->input->getVar('lang', '');
				// 
				// 
				if (empty($lang_tag))
				{
					// 
					// 
					throw new InvalidArgumentException('lang_tag must not be empty', 400);
				}
				// 
				// 
				$image = 'image_'.$lang_tag;
				// 
				// 
				$productlabel_table = JSFactory::getTable('productlabel');
				// 
				// 
				$productlabel_table->load($id);
				// 
				// 
				if (empty($productlabel_table->id))
				{
					// 
					// 
					throw new InvalidArgumentException('label not loaded', 400);
				}
				// 
				// 
				if (!is_file($jshopConfig->image_labels_path.'/'.$productlabel_table->$image))
				{
					// 
					// 
					throw new InvalidArgumentException('Image not found', 400);
				}
				// 
				// 
				unlink($jshopConfig->image_labels_path.'/'.$productlabel_table->$image);
				// 
				// 
				$productlabel_table->$image = '';
				// 
				// 
				$productlabel_table->store();
			}
		}
		
		
		/**
		 *
		 */
		public function onBeforeEditProductLabels(&$view)
		{
			// 
			// 
			$document = JFactory::getDocument();
			// 
			// 
			$document->addScriptDeclaration('
			function wishboxadminlabelimagesforlanguages_deleteImage(id, lang)
			{
				var url = \'index.php?option=com_ajax&plugin=wishboxadminlabelimagesforlanguages&group=jshoppingadmin&format=json&task=delete_foto_lang&id=\' + id + \'&lang=\' + lang;
				function showResponse(data)
				{
					jQuery(\'#image_\' + lang + \'_block\').hide();
				}
				jQuery.get(url, showResponse);
			}');
			// 
			// 
			$displayData = [
								'languages'	=> $view->languages,
								'row'		=> $view->productLabel,
								'config'	=> $view->config,
								'multilang' => $view->multilang,
							];
			// 
			// 
			$view->etemplatevar .= $this->getRenderer()->render($displayData);
		}
		
		
		/**
		 *
		 */
		public function onBeforeSaveProductLabel(&$post)
		{
			// 
			// 
			$jshopConfig = JSFactory::getConfig();
			// 
			// 
			$languages_model = JSFactory::getModel('languages');
			// 
			// 
			$languages = $languages_model->getAllLanguages(1);
			// 
			// 
			foreach($languages as $language)
			{
				// 
				// 
				$image = 'image_'.$language->language;
				// 
				// 
				if (isset($_FILES[$image]))
				{
					// 
					// 
					$addon_table = JSFactory::getTable('addon');
					// 
					// 
					$addon_table->addFieldTable('#__jshopping_product_labels', $image, 'VARCHAR(255)');
					// 
					// 
					$upload = new UploadFile($_FILES[$image]);
					// 
					// 
					$upload->setAllowFile(array('jpeg', 'jpg', 'gif', 'png'));
					// 
					// 
					$upload->setDir($jshopConfig->image_labels_path);
					// 
					// 
					$upload->setFileNameMd5(0);
					// 
					// 
					$upload->setFilterName(1);
					// 
					// 
					if ($upload->upload())
					{
						// 
						// 
						if ($post['old_'.$image_name])
						{
							// 
							// 
							unlink($jshopConfig->image_labels_path."/".$post['old_'.$image]);
						}
						// 
						// 
						$post[$image] = $upload->getName();
						// 
						// 
						chmod($jshopConfig->image_labels_path.'/'.$post[$image], 0777);
					}
					else
					{
						// 
						// 
						if ($upload->getError() != 4)
						{
							// 
							// 
							JError::raiseWarning('', _JSHOP_ERROR_UPLOADING_IMAGE);
							// 
							// 
							saveToLog('error.log', 'Label - Error upload image. code: '.$upload->getError());
						}
					}
				}
			}
		}
		
		
		/**
		 *
		 */
		protected function getLayoutPaths()
		{
			// 
			// 
			$template = JFactory::getApplication()->getTemplate();
			// 
			// 
			return [
						JPATH_ADMINISTRATOR.'/templates/'.$template.'/html/layouts/plugins/'.$this->_type.'/'.$this->_name,
						__DIR__ .'/layouts',
					];
		}
		
		
		/**
		 *
		 */
		protected function getRenderer($layoutId = 'default')
		{
			// 
			// 
			$renderer = new JLayoutFile($layoutId);
			// 
			// 
			$renderer->setIncludePaths($this->getLayoutPaths());
			// 
			// 
			return $renderer;
		}
	}