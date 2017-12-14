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


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(
    [
        'Isotope',
    ]
);


/**
 * Register the classes
 */
ClassLoader::addClasses(
    [
        // Src
        'Isotope\Model\Attribute\RadioImage' => 'system/modules/isotope_attribute_radio_image/src/Isotope/Model/Attribute/RadioImage.php',
    ]
);
