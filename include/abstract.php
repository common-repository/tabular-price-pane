<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** 
 * Abstract class  has been designed to use common functions.
 * This is file is responsible to add custom logic needed by all templates and classes.  
 */
if ( ! class_exists( 'tabularPricePaneLib' ) ) {
 
	abstract class tabularPricePaneLib extends WP_Widget {
		
	   /**
		* Default values can be stored
		*
		* @access    public
		* @since     1.0
		*
		* @var       array
		*/
		public $_config = array();

		/**
		 * PHP5 constructor method.
		 *
		 * Run the following methods when this class is loaded.
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */ 
		public function __construct() {  
		
			/**
			 * Default values configuration 
			 */
			$this->_config = array(
				'widget_title'=>tpp_widget_title,
				'price_from'=>tpp_price_from,
				'price_to'=>tpp_price_to,
				'price_difference'=>tpp_price_difference,
				'number_of_product_display'=>tpp_number_of_product_display,
				'price_text_color'=>tpp_price_text_color,
				'title_text_color'=>tpp_title_text_color,
				'price_tab_text_color'=>tpp_price_tab_text_color,
				'price_tab_background_color'=>tpp_price_tab_background_color,
				'header_text_color'=>tpp_header_text_color,
				'header_background_color'=>tpp_header_background_color,
				'display_title_price_over_image'=>tpp_display_title_price_over_image, 
				'hide_widget_title'=>tpp_hide_widget_title,
				'hide_product_title'=>tpp_hide_product_title,
				'hide_product_price'=>tpp_hide_product_price,
				'template'=>tpp_template, 
				'vcode'=>$this->tpp_getUCode(), 
				'category_id'=>tpp_category,
				'security_key'=>tpp_security_key,
				'tp_widget_width'=>tpp_widget_width
			); 
			
			/**
			 * Load text domain
			 */
			add_action( 'plugins_loaded', array( $this, 'tpp_tabularpricepane_text_domain' ) );
			
			parent::WP_Widget( false, $name = __( 'Tabular Price Pane', 'tabular_price_pane' ) ); 	
			
			/**
			 * Widget initialization for tabular price pane
			 */
			add_action( 'widgets_init', array( &$this, 'tpp_initTabularPricePane' ) ); 
			
			/**
			 * Load the CSS/JS scripts
			 */
			add_action( 'init',  array( $this, 'tpp_tabular_price_pane_scripts' ) );
			
			add_action( 'admin_enqueue_scripts', array( $this, 'tpp_admin_enqueue' ) ); 
			
		}
		
	   /**
		* Register and load JS/CSS for admin widget configuration 
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool|void It returns false if not valid page or display HTML for JS/CSS
		*/  
		public function tpp_admin_enqueue() {

			if ( ! $this->tpp_validate_page() )
				return FALSE;
			
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'admin-tabularpricepane.css', TPP_MEDIA."css/admin-tabularpricepane.css" );
			wp_enqueue_script( 'admin-tabularpricepane.js', TPP_MEDIA."js/admin-tabularpricepane.js" ); 
			
		}	
		
	   /**
		* Validate widget or shortcode post type page
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool It returns true if page is post.php or widget otherwise returns false
		*/ 
		private function tpp_validate_page() {

			if ( ( isset( $_GET['post_type'] )  && $_GET['post_type'] == 'tabularpricepane' ) || strpos($_SERVER["REQUEST_URI"],"widgets.php") > 0  || strpos($_SERVER["REQUEST_URI"],"post.php" ) > 0 )
				return TRUE;
		
		} 
		
		/**
		 * Load the CSS/JS scripts
		 *
		 * @return  void
		 *
		 * @access  public
		 * @since   1.0
		 */
		function tpp_tabular_price_pane_scripts() {

			$dependencies = array( 'jquery' );
			 
			/**
			 * Include Tabular Price Pane JS/CSS 
			 */
			wp_enqueue_style( 'tabularpricepane', TPP_MEDIA."css/tabularpricepane.css" );
			 
			wp_enqueue_script( 'tabularpricepane', TPP_MEDIA."js/tabularpricepane.js" );
			
			/**
			 * Define global javascript variable
			 */
			wp_localize_script( 'tabularpricepane', 'tabularpricepane', array(
				'tpp_ajax_url' => admin_url( 'admin-ajax.php' ),
				'tpp_security'  =>  wp_create_nonce(tpp_security_key),
				'tpp_plugin_url' => plugins_url( '/', __FILE__ ),
			));
		}	
		
		/**
		 * Loads the text domain
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */
		public function tpp_tabularpricepane_text_domain() {

		  /**
		   * Load text domain
		   */
		   load_plugin_textdomain( 'tabular_price_pane', false, plugin_basename(dirname(__FILE__)) . '/languages' );
			
		}
		 
		/**
		 * Load and register widget settings
		 *
		 * @access  private
		 * @since   1.0
		 *
		 * @return  void
		 */ 
		public function tpp_initTabularPricePane() { 
			
		  /**
		   * Widget registration
		   */
		   register_widget( 'tabularPricePaneWidget_Admin' );
			
		}  
		 
		/**
		 * Calculate price list as per 'from-price', 'to-price' and 'price-difference'
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   float  $price_from  Starting price
		 * @param   float  $price_to  Ending price
		 * @param   float  $price_difference  Price difference value to calculate price list
		 * @return  array  An array of price list values based on 'from-price', 'to-price' and 'price-difference' configuration. 
		 */
		public function tpp_getPriceTabArray( $price_from, $price_to, $price_difference ) { 
		
			$_arr_price_list = array();
			
			/**
			 * Calculate the price list
			 */
			for( $i = $price_from + $price_difference; $i <= $price_to; $i = $i + $price_difference ) {
				if( ( ( $i + $price_difference ) > $price_to ) ) {
					$_arr_price_list[] = array( 'from' => ( $i - $price_difference ), 'to' => $i ); 
					if( $i != $price_to )
						$_arr_price_list[] = array( 'from' => $i, 'to' => $price_to ); 
				}
				else   
					$_arr_price_list[] = array( 'from' => ( $i - $price_difference ), 'to' => $i );  
			}
			
			return $_arr_price_list;	
		
		}    
		 
		/**
		 * Get product image by given image attachment id
		 *
 		 * @access  public
		 * @since   1.0
		 *
		 * @param   int   $img  Image attachment ID
		 * @return  string  Returns image html from product attachment
		 */
		 public function tpp_getProductImage( $img ) {
		 
			$image_link = wp_get_attachment_url( $img ); 
			if( $image_link ) {
				$image_title = esc_attr( get_the_title( $img ) );  
				return wp_get_attachment_image( $img , array(180,180), 0, $attr = array(
									'title'	=> $image_title,
									'alt'	=> $image_title
								) );
			}	 
			else
				return wc_placeholder_img( 'shop_catalog' );
		 }
		 
		/**
		 * Get all the categories
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @return  object It contains all the categories for shop
		 */
		public function tpp_getCategories() {
		
			global $wpdb;
			
			/**
			 * Fetch all the categories from database
			 */
			return $wpdb->get_results( "SELECT wtt.term_taxonomy_id as id, wt.name as category FROM `{$wpdb->prefix}terms` as wt INNER JOIN {$wpdb->prefix}term_taxonomy as wtt on wtt.term_id = wt.term_id and wtt.taxonomy = 'product_cat'" );
		
		}
		 
		/**
		 * Get Unique Block ID
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @return  string 
		 */
		public function tpp_getUCode() { 
			
			return 'uid_'.md5( "TABULARPANE32@#RPSDD@SQSITARAM@A$".time() );
		
		} 
		
		/**
		 * Get Tabular Price Pane Template
		 *
		 * @access  public
		 * @since   1.0
		 *
		 * @param   string $file Template file name
		 * @return  string Returns template file path
		 */
		public function tpp_getTabularPricePaneTemplate( $file ) {
			
			// Get template file path
			if( locate_template( $file ) != "" ){
				return locate_template( $file );
			}else{
				return plugin_dir_path( dirname( __FILE__ ) ) . 'templates/' . $file ;
			} 
			
	   }
   }
}