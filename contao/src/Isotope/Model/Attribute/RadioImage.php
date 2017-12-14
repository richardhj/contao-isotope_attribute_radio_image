<?php
/**
 * RadioImage extension for Isotope eCommerce provides an attribute that generates the variant's gallery image as label.
 *
 * Copyright (c) 2016 Richard Henkenjohann
 *
 * @package RadioImage
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */


namespace Isotope\Model\Attribute;

use Isotope\Interfaces\IsotopeAttribute;
use Isotope\Interfaces\IsotopeAttributeForVariants;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Model\Gallery;
use Isotope\Model\Product;


/**
 * Class RadioImage
 * @property integer $radioImageGallery The gallery to parse the image label
 * @package Isotope\Model\Attribute
 */
class RadioImage extends AbstractAttributeWithOptions implements IsotopeAttribute, IsotopeAttributeForVariants
{

	/**
	 * {@inheritdoc}
	 */
	public function getBackendWidget()
	{
		return $GLOBALS['BE_FFL']['select'];
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFrontendWidget()
	{
		return $GLOBALS['TL_FFL']['radio'];
	}


	/**
	 * Adjust the options wizard for this attribute
	 *
	 * @param \Widget $objWidget
	 * @param array   $arrColumns
	 *
	 * @return array
	 */
	public function prepareOptionsWizard($objWidget, $arrColumns)
	{
		if (TL_MODE == 'BE')
		{
			// Behave as select menu, this code is copied from the SelectMenu class
			if ($this->isVariantOption())
			{
				unset($arrColumns['default'], $arrColumns['group']);
			}

			return $arrColumns;
		}

		// Behave as a radio button, this code is copied from the RadioButton class
        unset($arrColumns['group']);

        if ($this->isVariantOption())
		{
			unset($arrColumns['default']);
        }

        return $arrColumns;
	}


	/**
	 * Set SQL field for this attribute
	 *
	 * @param array $arrData
	 */
	public function saveToDCA(array &$arrData)
	{
		$this->multiple = false;

		parent::saveToDCA($arrData);

		if ('attribute' === $this->optionsSource)
		{
			$arrData['fields'][$this->field_name]['sql'] = "varchar(255) NOT NULL default ''";
		}
		else
		{
			$arrData['fields'][$this->field_name]['sql'] = "int(10) NOT NULL default '0'";
		}

		if ($this->fe_filter)
		{
			$arrData['config']['sql']['keys'][$this->field_name] = 'index';
		}
	}


	/**
	 * Alter the options and set an image as label
	 *
	 * @param IsotopeProduct|Product\Standard $objProduct
	 *
	 * @return array|mixed
	 *
	 * @throws \InvalidArgumentException when optionsSource=product but product is null
	 * @throws \UnexpectedValueException for unknown optionsSource
	 */
	public function getOptionsForWidget(IsotopeProduct $objProduct = null)
	{
		// Skip in the back end or without a given product
		if (TL_MODE == 'BE' || null === $objProduct)
		{
			return parent::getOptionsForWidget($objProduct);
		}

		// Fetch all product's variants and make the accessible via the attribute value in an array
		$objVariants = Product::findAvailableByIds($objProduct->getVariantIds());

        if (null === $objVariants) {
            return parent::getOptionsForWidget($objProduct);
        }
        
		/** @var IsotopeProduct[] $arrVariants */
		$arrVariants = array_combine($objVariants->fetchEach($this->field_name), $objVariants->getModels());

		// Alter the options
		return array_map(function ($arrOption) use ($arrVariants)
		{
			// Skip if option has no associated product variant
			if (!array_key_exists($arrOption['value'], $arrVariants))
			{
				return $arrOption;
			}

			/** @var Gallery|Gallery\Standard $objGallery */
			$objGallery = Gallery::createForProductAttribute
			(
				$arrVariants[$arrOption['value']], # The variant's Product instance
				'images', # 'images' is the product's attribute containing the images to parse
				array('gallery' => $this->radioImageGallery) # Provide the gallery's id in the config array
			);

			// Wrap label in span with css class
			$arrOption['label'] = sprintf('<span class="attribute-label">%s</span>', $arrOption['label']);

			// Add image to label
			$arrOption['label'] .= PHP_EOL . $objGallery->generateMainImage();

			return $arrOption;

		}, parent::getOptionsForWidget($objProduct));
	}
}
