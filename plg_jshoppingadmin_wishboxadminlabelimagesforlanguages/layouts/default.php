<?php
	// 
	defined('JPATH_BASE') or die;
	
	// 
	use \Joomla\CMS\Language\Text;
	
	// 
	// 
	$languages = $displayData['languages'];
	// 
	// 
	$row = $displayData['row'];
	// 
	// 
	$config = $displayData['config'];
	// 
	// 
	$multilang = $displayData['multilang'];
?>
<?php foreach ($languages as $language) { ?>
<?php if ($config->defaultLanguage == $language->language) { continue; } ?>
<?php $image = 'image_'.$language->language; ?>
<tr>
	<td class="key">
		<?php echo Text::_('JSHOP_IMAGE'); ?><?php if ($multilang) echo '('.$language->lang.')'; ?>
	</td>
	<td>
		<?php if ($row->$image) { ?>
		<div id="image_<?php echo $language->language; ?>_block">
			<div>
				<img src="<?php echo $config->image_labels_live_path.'/'.$row->$image; ?>" alt=""/>
			</div>
			<div style="padding-bottom:5px;" class="link_delete_foto">
				<a
					class="btn btn-micro btn-danger"
					href="#"
					onclick="if (confirm('<?php echo Text::_('JSHOP_DELETE_IMAGE'); ?>')) wishboxadminlabelimagesforlanguages_deleteImage('<?php echo $row->id; ?>', '<?php echo $language->language; ?>'); return false;"
				>
					<img src="components/com_jshopping/images/publish_r.png"> <?php echo Text::_('JSHOP_DELETE_IMAGE'); ?>
				</a>
			</div>
		</div>
		<?php } ?>
		<input type="file" name="<?php echo $image; ?>" />
		<input type="hidden" name="old_<?php echo $image; ?>" value="<?php echo $row->$image; ?>" />
	</td>
</tr>
<?php } ?>