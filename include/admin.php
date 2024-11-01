<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** 
 * Admin panel widget configuration
 */
if ( ! class_exists( 'tabularPricePaneWidget_Admin' ) ) {
 
	class tabularPricePaneWidget_Admin extends tabularPricePaneLib {    
 
	   /**
		* Update the widget settings.
		* 
		* @access  private
		* @since   1.0
		*
		* @param   array  $new_instance  Set of POST form values
		* @param   array  $old_instance  Set of old form values
		* @return  array Sanitize form data 
		*/ 
		function update( $new_instance, $old_instance ) { 
		
			foreach( $new_instance as $_key => $_value ) {
				$new_instance[$_key] = sanitize_text_field( $new_instance[$_key] );  
			}   
			
			return $new_instance;
		
		} 
 
	   /**
		* Displays the widget settings controls on the widget panel.  
		*
		* @access  private
		* @since   1.0
		*
		* @param   array  $instance  Set of form values
		* @return  void
		*/
		function form( $instance ) { 
		 
			$instance = wp_parse_args( $instance, $this->_config );   
			
			// Filter values
			foreach( $instance as $_key => $_value ) {
				$instance[$_key]  = htmlspecialchars( $instance[$_key], ENT_QUOTES ); 
			}  	
			
			require( $this->tpp_getTabularPricePaneTemplate( 'admin_widget_settings.php' ) );
		
		}

 
	   /**
		* Show the price list panel
		*
		* @access  private
		* @since   1.0
		*
		* @param   array  $args  Set of configuration values
		* @param   array  $instance  Set of configuration values
		* @return  void	  Displays widget html
		*/
		function widget( $args, $instance ) {
		
			// Filter values
			foreach( $instance as $_key => $_value ) {
				$instance[$_key]  = htmlspecialchars( $instance[$_key], ENT_QUOTES ); 
			}  
			
			$this->_config = $instance; 
			
			// Load Layout
			require( $this->tpp_getTabularPricePaneTemplate( 'template_'.$this->_config["template"].'.php' ) );
			
		}
		
	}

}