<?php  

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Register shortcode and render product data as per shortcode configuration. 
 */ 
if ( ! class_exists( 'tabularPricePaneWidget' ) ) {
 
	class tabularPricePaneWidget extends tabularPricePaneLib {
	 
	   /**
		* PHP5 constructor method.
		*
		* Run the following methods when this class is loaded
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function __construct() {
		
			add_action( 'init', array( &$this, 'tpp_init' ) ); 
			parent::__construct();
			
		}  
		
	   /**
		* Load required methods on wordpress init action 
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function tpp_init() {
		
			add_action( 'wp_ajax_getTotalProducts',array( &$this, 'tpp_getTotalProducts' ) );
			add_action( 'wp_ajax_getProducts',array( &$this, 'tpp_getProducts' ) ); 
			add_action( 'wp_ajax_getMoreProducts',array( &$this, 'tpp_getMoreProducts' ) );
			
			add_action( 'wp_ajax_nopriv_getTotalProducts', array( &$this, 'tpp_getTotalProducts' ) );
			add_action( 'wp_ajax_nopriv_getProducts', array( &$this, 'tpp_getProducts' ) ); 
			add_action( 'wp_ajax_nopriv_getMoreProducts', array( &$this, 'tpp_getMoreProducts' ) ); 
			
			add_shortcode( 'tabularpricepane', array( &$this, 'tpp_tabularPricePane' ) ); 
			
		} 
		
	   /**
		* Get the total numbers of products
		*
		* @access  public
		* @since   1.0
		*
		* @param   float  $_from  				Set starting price
		* @param   float  $_to  				Set ending price
		* @param   int    $category_id  		Category ID 
		* @param   int    $c_flg  				Whether to fetch whether products by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  To check settings of default category If it's value is '1'. Default value is '0'
		* @return  int	  Total number of products  	
		*/  
		public function tpp_getTotalProducts( $_from=0, $_to=0, $category_id, $c_flg, $is_default_category_with_hidden ) { 
		
			global $wpdb;   
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer( $this->_config["security_key"], 'security' );

		   /**
			* Fetch products as per search filter
			*/	
			$_res_total = $this->tpp_getSqlResult( $_from, $_to, $category_id, 0, 0, $c_flg, $is_default_category_with_hidden, 1 );
			
			return $_res_total[0]->total_val;
			 
		}	

		 
	   /**
		* Render tabular price pane shortcode
		*
		* @access  public
		* @since   1.0
		*
		* @param   array   $params  Shortcode configuration options from admin settings
		* @return  string  Render tabular price pane HTML
		*/
		public function tpp_tabularPricePane( $params = array() ) { 	
		
			$tabularpricepane_id = $params["id"]; 
			$tpp_shortcode = get_post_meta( $tabularpricepane_id ); 
			
			foreach ( $tpp_shortcode as $sc_key => $sc_val ) {			
				$tpp_shortcode[$sc_key] = $sc_val[0];			
			}
			
			if(!isset($tpp_shortcode["price_from"]))	
				$tpp_shortcode["price_from"] = 0;
			if(!isset($tpp_shortcode["price_to"]))	
				$tpp_shortcode["price_to"] = 0;
			if(!isset($tpp_shortcode["price_difference"]))
				$tpp_shortcode["price_difference"] = 0;
			if(!isset($tpp_shortcode["number_of_product_display"]))	
				$tpp_shortcode["number_of_product_display"] = 0;
			if(!isset($tpp_shortcode["category_id"]))	
				$tpp_shortcode["category_id"] = 0;
			 	
			$this->_config = shortcode_atts( $this->_config, $tpp_shortcode ); 
			
		   /**
			* Load template according to admin settings
			*/
			ob_start();
			
			require( $this->tpp_getTabularPricePaneTemplate( "template_" . $this->_config["template"] . ".php" ) ); 
			
			return ob_get_clean();
		
		}   
		
	   /**
		* Load more product via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  void Displays searched products HTML to load more pagination
		*/	
		public function tpp_getMoreProducts() {
		
			global $wpdb, $wp_query; 
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer($this->_config["security_key"], 'security' );
			
			$_from = ( isset( $_REQUEST["from"] )?esc_attr( $_REQUEST["from"] ):0 );
			$_to = ( isset( $_REQUEST["to"] )?esc_attr( $_REQUEST["to"] ):0 );
			$_total = ( isset( $_REQUEST["total"] )?esc_attr( $_REQUEST["total"] ):0 );
			$category_id = ( isset( $_REQUEST["category_id"] )?esc_attr( $_REQUEST["category_id"] ):0 );
			$_limit_start = ( isset( $_REQUEST["limit_start"])?esc_attr( $_REQUEST["limit_start"] ):0 );
			$_limit_end = ( isset( $_REQUEST["limit_end"])?esc_attr( $_REQUEST["limit_end"] ):tpp_number_of_product_display ); 
			
		   /**
			* Fetch products as per search filter
			*/	
			$_result_items = $this->tpp_getSqlResult( $_from, $_to, $category_id, $_limit_start, $_limit_end );
		  
			require( $this->tpp_getTabularPricePaneTemplate( 'ajax_load_more_products.php' ) );	
			
			wp_die();
		}    
		
	   /**
		* Load more products via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  object Displays searched products HTML
		*/
		public function tpp_getProducts() {
		
		   global $wpdb; 
			
		   /**
			* Check security token from ajax request
			*/	
		   check_ajax_referer( $this->_config["security_key"], 'security' );	   
		   
		   require( $this->tpp_getTabularPricePaneTemplate( 'ajax_load_products.php' ) );	
		   
  		   wp_die();
		
		}
		 
	   /**
		* Get product list with specified limit and filtered by price, category and search text
		*
		* @access  public
		* @since   1.0 
		*
		* @param   int     $category_id 		 Selected category ID  
		* @param   float   $from				 Set starting price
		* @param   float   $to					 Set ending price  
		* @param   int     $_limit_end			 Limit to fetch product ending to given position
		* @return  object  Set of searched product data
		*/
		public function tpp_getProductList( $category_id, $from, $to, $_limit_end ) {
			
		   /**
			* Check security token from ajax request
			*/	
			check_ajax_referer( $this->_config["security_key"], 'security' );		
			
		   /**
			* Fetch data from database
			*/
			return $this->tpp_getSqlResult( $from, $to, $category_id, 0, $_limit_end );
			 
		}
		 
	   /**
		* Fetch product data from database by from-price, to-price, category, search text and item limit
		*
		* @access  public
		* @since   1.0 
		*
		* @param   float  $from  				Set starting price
		* @param   float  $to    				Set ending price
		* @param   int    $category_id  		Category ID 
		* @param   int    $_limit_start  		Limit to fetch product starting from given position
		* @param   int    $_limit_end  			Limit to fetch product ending to given position
		* @param   int    $category_flg  		Whether to fetch whether products by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  To check settings of default category If it's value is '1'. Default value is '0'
		* @param   int    $is_count  			Whether to fetch only number of products from database as count of items 
		* @return  object Set of searched product data
		*/
		private function tpp_getSqlResult( $from, $to, $category_id, $_limit_start, $_limit_end, $category_flg = 0, $is_default_category_with_hidden = 0, $is_count = 0 ) {
			
			global $wpdb; 
			$_category_filter_query = "";
			$_product_text_filter_query = "";
			$_fetch_fields = "";
			$_limit = "";
			
			
		   /**
			* Prepare safe mysql database query
			*/
			if( $is_count == 1 ) {
				if( $category_id > 0 && ( $category_flg == 1 || $is_default_category_with_hidden == 1 ) ) {
					$_category_filter_query .= $wpdb->prepare( " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id = %d and wtr.object_id = wp.ID ", $category_id );
				} 
				$_fetch_fields = " count(*) as total_val ";
			} else { 
				if( $category_id > 0 ) {
					$_category_filter_query .= $wpdb->prepare( " INNER JOIN {$wpdb->prefix}term_relationships as wtr on wtr.term_taxonomy_id = %d and wtr.object_id = wp.ID ", $category_id );
				} 
				$_fetch_fields = " wp.post_type, pm_image.meta_value as product_image, wp.ID as product_id, pm.meta_value as sale_price, wp.post_title as product_name ";
				$_limit = $wpdb->prepare( " order by CONVERT(sale_price, UNSIGNED INTEGER) ASC limit  %d, %d ", $_limit_start, $_limit_end );
			}  
			
			$_price_range = $wpdb->prepare( " BETWEEN %f and %f ", $from, $to );  
			
			
		   /**
			* Fetch product data from database
			*/
			$_result_items = $wpdb->get_results( " select $_fetch_fields from {$wpdb->prefix}posts as wp
				INNER JOIN {$wpdb->prefix}postmeta as pm on pm.post_id = wp.ID and pm.meta_key = '_price' and pm.meta_value $_price_range 
				INNER JOIN {$wpdb->prefix}postmeta as pm_stock on pm_stock.post_id = wp.ID and pm_stock.meta_key = '_stock_status' 
				INNER JOIN {$wpdb->prefix}postmeta as pm_backorders on pm_backorders.post_id = wp.ID and pm_backorders.meta_key = '_backorders' 
				INNER JOIN {$wpdb->prefix}postmeta as pm_visible on pm_visible.post_id = wp.ID and pm_visible.meta_key = '_visibility' and pm_visible.meta_value in ('visible', 'catalog')
				$_category_filter_query LEFT JOIN {$wpdb->prefix}postmeta as pm_image on pm_image.post_id = wp.ID and pm_image.meta_key = '_thumbnail_id'
				where (pm_stock.meta_value = 'instock' or pm_backorders.meta_value in ('notify','yes')) and wp.post_status = 'publish' $_limit " );
				  
			return $_result_items;
		
		}
		
	}
	
}
new tabularPricePaneWidget();