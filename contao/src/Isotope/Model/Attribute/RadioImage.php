<?php

/**
 * This file is part of richardhj/contao-isotope_attribute_radio_image.
 *
 * Copyright (c) 2016-2017 Richard Henkenjohann
 *
 * @package   richardhj/contao-isotope_attribute_radio_image
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2016-2017 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-isotope_attribute_radio_image/blob/master/LICENSE LGPL-3.0
 */

namespace Isotope\Model\Attribute;

use Isotope\Interfaces\IsotopeAttribute;
use Isotope\Interfaces\IsotopeAttributeForVariants;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Model\Gallery;
use Isotope\Model\Product;


/**
 * Class RadioImage
 *
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
     * @param \Widget $widget
     * @param array   $columns
     *
     * @return array
     */
    public function prepareOptionsWizard($widget, $columns)
    {
        if ('BE' === TL_MODE) {
            // Behave as select menu, this code is copied from the SelectMenu class
            if ($this->isVariantOption()) {
                unset($columns['default'], $columns['group']);
            }

            return $columns;
        }

        // Behave as a radio button, this code is copied from the RadioButton class
        if ($this->isVariantOption()) {
            unset($columns['default']);
        }
        unset($columns['group']);

        return $columns;
    }


    /**
     * Set SQL field for this attribute
     *
     * @param array $data
     */
    public function saveToDCA(array &$data)
    {
        $this->multiple = false;

        parent::saveToDCA($data);

        if ('attribute' === $this->optionsSource) {
            $data['fields'][$this->field_name]['sql'] = "varchar(255) NOT NULL default ''";
        } else {
            $data['fields'][$this->field_name]['sql'] = "int(10) NOT NULL default '0'";
        }

        if ($this->fe_filter) {
            $data['config']['sql']['keys'][$this->field_name] = 'index';
        }
    }


    /**
     * Alter the options and set an image as label
     *
     * @param IsotopeProduct|Product\Standard $product
     *
     * @return array|mixed
     *
     * @throws \InvalidArgumentException when optionsSource=product but product is null
     * @throws \UnexpectedValueException for unknown optionsSource
     */
    public function getOptionsForWidget(IsotopeProduct $product = null)
    {
        // Skip in the back end or without a given product
        if ('BE' === TL_MODE || null === $product) {
            return parent::getOptionsForWidget($product);
        }

        // Fetch all product's variants and make the accessible via the attribute value in an array
        $variants = Product::findAvailableByIds($product->getVariantIds());

        if (null === $variants) {
            return parent::getOptionsForWidget($product);
        }

        /** @var IsotopeProduct[] $variantProducts */
        $variantProducts = array_combine($variants->fetchEach($this->field_name), $variants->getModels());

        // Alter the options
        return array_map(
            function ($option) use ($variantProducts) {
                // Skip if option has no associated product variant
                if (!array_key_exists($option['value'], $variantProducts)) {
                    return $option;
                }

                /** @var Gallery|Gallery\Standard $gallery */
                $gallery = Gallery::createForProductAttribute
                (
                    $variantProducts[$option['value']], # The variant's Product instance
                    'images', # 'images' is the product's attribute containing the images to parse
                    ['gallery' => $this->radioImageGallery] # Provide the gallery's id in the config array
                );

                // Wrap label in span with css class
                $option['label'] = sprintf('<span class="attribute-label">%s</span>', $option['label']);

                // Add image to label
                $option['label'] .= PHP_EOL.$gallery->generateMainImage();

                return $option;

            },
            parent::getOptionsForWidget($product)
        );
    }
}
