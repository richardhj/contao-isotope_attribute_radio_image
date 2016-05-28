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

use Haste\Input\Input;
use Isotope\Interfaces\IsotopeAttribute;
use Isotope\Interfaces\IsotopeAttributeForVariants;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Model\AttributeOption;
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
			return SelectMenu::prepareOptionsWizard($objWidget, $arrColumns);
		}

		return RadioButton::prepareOptionsWizard($objWidget, $arrColumns);
	}


	/**
	 * Set SQL field for this attribute
	 *
	 * @param array $arrData
	 */
	public function saveToDCA(array &$arrData)
	{
		if (TL_MODE == 'BE')
		{
			SelectMenu::saveToDca($arrData);
		}

		RadioButton::saveToDCA($arrData);
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

			// Replace label with image
			$arrOption['label'] = $objGallery->generateMainImage();

			return $arrOption;

		}, parent::getOptionsForWidget($objProduct));
	}
}
