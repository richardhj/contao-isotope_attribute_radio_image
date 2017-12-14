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

use Isotope\Model\Gallery as IsotopeGallery;


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radioImage']               = str_replace(
    ',includeBlankOption',
    ',includeBlankOption,radioImageGallery',
    $GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radio']
);
$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radioImageoption_variant'] = str_replace(
    ',includeBlankOption',
    ',includeBlankOption,radioImageGallery',
    $GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radiooption_vairant']
);
$GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radioImageproduct']        = str_replace(
    ',includeBlankOption',
    ',includeBlankOption,radioImageGallery',
    $GLOBALS['TL_DCA']['tl_iso_attribute']['palettes']['radioproduct']
);


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_attribute']['fields']['radioImageGallery'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_iso_attribute']['radioImageGallery'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => function () {
        $galleries = IsotopeGallery::findAll();

        return (null === $galleries) ? [] : $galleries->fetchEach('name');
    },
    'eval'             => [
        'mandatory' => true,
        'tl_class'  => 'w50 m12',
        'chosen'    => true,
    ],
    'sql'              => "int(10) NOT NULL default '0'",
];
