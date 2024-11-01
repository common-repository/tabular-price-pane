<?php 
/*
  Plugin Name: Tabular Price Pane
  Description: Displays products
  Author: Bhavesh Patel
  Plugin URI: http://www.ikhodal.com/wp-tabular-price-pane/
  Author URI: http://www.ikhodal.com
  Version: 1.0
  License: GNU General Public License v2.0
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/ 


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
  
//////////////////////////////////////////////////////
// Defines the constants for use within the plugin. //
////////////////////////////////////////////////////// 

/**
* Widget/Block Title
*/
define( 'tpp_widget_title', 'Price List' );

/**
* Starting price in pane
*/
define( 'tpp_price_from', '0' );

/**
* Ending price in pane
*/
define( 'tpp_price_to', '1000' );

/**
* Price difference between starting and ending price for pane
*/
define( 'tpp_price_difference', '100' );

/**
* Default category selection for fist product load in widget
*/
define( 'tpp_category', '0' );

/**
* Number of products per next loading result
*/
define( 'tpp_number_of_product_display', '2' );

/**
* Product price text color
*/
define( 'tpp_price_text_color', '#000' );

/**
* Product title text color
*/
define( 'tpp_title_text_color', '#000' );

/**
* Price text color for 'From-To' price 
*/
define( 'tpp_price_tab_text_color', '#000' );

/**
* Price text background color for 'From-To' price 
*/
define( 'tpp_price_tab_background_color', '#f7f7f7' );

/**
* Widget/block header text color
*/
define( 'tpp_header_text_color', '#fff' );

/**
* Widget/block header text background color
*/
define( 'tpp_header_background_color', '#00bc65' );

/**
* Display product title and text over product image
*/
define( 'tpp_display_title_price_over_image', 'no' );

/**
* Widget/block width
*/
define( 'tpp_widget_width', '100%' );  

/**
* Hide/Show widget title
*/
define( 'tpp_hide_widget_title', 'no' ); 

/**
* Template for widget/block
*/
define( 'tpp_template', 'pane_style_1' ); 

/**
* Hide/Show product price
*/
define( 'tpp_hide_product_price', 'no' ); 

/**
* Hide/Show product title
*/
define( 'tpp_hide_product_title', 'no' );  

/**
* Security key for block id
*/
define( 'tpp_security_key', 'TPP_#s@R$@ASI#TA(!@@21M3' );
 
 
/**
 * Include abstract class for common methods
 */
require_once 'include/abstract.php';

/**
*  Assets for tabular price pane
*/
$tpp_plugins_url = plugins_url( "/assets/", __FILE__ );

define( 'TPP_MEDIA', $tpp_plugins_url ); 

///////////////////////////////////////////////////////
// Include files for widget and shortcode management //
///////////////////////////////////////////////////////

/**
 * Admin panel widget configuration
 */ 
require_once 'include/admin.php';

/**
 * Load Tabular Price Pane on frontent pages
 */
require_once 'include/tabularpricepane.php';  
