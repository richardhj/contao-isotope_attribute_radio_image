<?php
/**
 * RadioImage extension for Isotope eCommerce provides an attribute that generates the variant's gallery image as label.
 *
 * Copyright (c) 2016 Richard Henkenjohann
 *
 * @package RadioImage
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radioImage'] = str_replace
(
	',includeBlankOption',
	',includeBlankOption,radioImageGallery',
	$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radio']
);
$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radioImageoption_variant'] = str_replace
(
	',includeBlankOption',
	',includeBlankOption,radioImageGallery',
	$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radiooption_vairant']
);
$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radioImageproduct'] = str_replace
(
	',includeBlankOption',
	',includeBlankOption,radioImageGallery',
	$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radioproduct']
);


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_attribute']['fields']['radioImageGallery'] = array
(
	'label'            => &$GLOBALS['TL_LANG']['tl_iso_attribute']['radioImageGallery'],
	'exclude'          => true,
	'inputType'        => 'select',
	'options_callback' => function ()
	{
		/** @noinspection PhpUndefinedMethodInspection */
		$objGalleries = \Isotope\Model\Gallery::findAll();

		/** @noinspection PhpUndefinedMethodInspection */
		return (null === $objGalleries) ? array() : $objGalleries->fetchEach('name');
	},
	'eval'             => array
	(
		'mandatory' => true,
		'tl_class'  => 'w50 m12',
		'chosen'    => true
	),
	'sql'              => "int(10) NOT NULL default '0'",
);
