[![Latest Version on Packagist](http://img.shields.io/packagist/v/richardhj/contao-isotope_attribute_radio_image.svg)](https://packagist.org/packages/richardhj/contao-isotope_attribute_radio_image)
[![Dependency Status](https://www.versioneye.com/php/richardhj:contao-isotope_attribute_radio_image/badge.svg)](https://www.versioneye.com/php/richardhj:contao-isotope_attribute_radio_image)

# RadioImage attribute for Isotope eCommerce

The attribute „RadioImage“ for Isotope eCommerce generates the variant’s gallery image in the front end instead of the label. This attribute is great for variants that handle different colors.

## Usage

1. Install the extension (via composer)
2. Create a gallery in the shop configuration that will be used to parse the widget in the front end
3. Create an attribute or alter an existing attribute in the shop configuration. Use the this attribute as type, define the options and choose the created gallery. In most cases you have to choose „use for variants“ and „mandatory“.
4. Alter or create a product type in the shop configuration. It must handle variants and uses the newly created attribute as well as the attribute „images“ for variants.
4. Define the variants for your product. Upload an image for each product variant.