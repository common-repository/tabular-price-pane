<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 $vcode = $this->_config["vcode"];  ?>
 <script type='text/javascript' language='javascript'>
	var default_category_id_<?php echo $vcode; ?> = '<?php echo $this->_config["category_id"]; ?>';
	var request_obj_<?php echo $vcode; ?> = {
			category_id:'<?php echo $this->_config["category_id"]; ?>',
			hide_product_price:'<?php echo $this->_config["hide_product_price"]; ?>',
			price_difference:'<?php echo $this->_config["price_difference"]; ?>',
			hide_product_title:'<?php echo $this->_config["hide_product_title"]; ?>',
			product_price_color:'<?php echo $this->_config["price_text_color"]; ?>',
			product_title_color:'<?php echo $this->_config["title_text_color"]; ?>',
			price_tab_text_color:'<?php echo $this->_config["price_tab_text_color"]; ?>', 
			price_tab_background_color:'<?php echo $this->_config["price_tab_background_color"]; ?>',
			header_text_color:'<?php echo $this->_config["header_text_color"]; ?>', 
			header_background_color:'<?php echo $this->_config["header_background_color"]; ?>',
			display_title_price_over_image:'<?php echo $this->_config["display_title_price_over_image"]; ?>', 
			number_of_product_display:'<?php echo $this->_config["number_of_product_display"]; ?>', 
			vcode:'<?php echo $vcode; ?>'
		}
 </script> 
 <?php $_price_list = $this->tpp_getPriceTabArray( $this->_config["price_from"], $this->_config["price_to"], $this->_config["price_difference"] );  ?>
 <div id="tabularpricepane" style="width:<?php echo $this->_config["tp_widget_width"]; ?>"  class="pane_style_3 <?php echo ( ( trim( $this->_config["display_title_price_over_image"] ) == "yes" ) ? "disp_title_over_img" : "" ); ?>">
	<?php if($this->_config["hide_widget_title"]=="no"){ ?>
		<div class="ik-price-tab-title-head" style="background-color:<?php echo $this->_config["header_background_color"]; ?>;color:<?php echo $this->_config["header_text_color"]; ?>"  >
			<?php echo $this->_config["widget_title"]; ?>   
		</div>
	<?php } ?> 
	<span class='wp-load-icon'>
		<img width="18px" height="18px" src="<?php echo TPP_MEDIA.'images/loader.gif'; ?>" />
	</span>
	<div class="wea_content lt-grid">
		<?php 
			if( count( $_price_list ) > 0 ) {
				foreach( $_price_list as $_price_list_item ) {
				  ?>
					<div class="item-price-list">
						<div class="price-item"  onmouseout="price_tab_ms_out( this )" onmouseover="price_tab_ms_hover( this )" id="<?php echo $vcode.'-'.$_price_list_item["from"].'-'.$_price_list_item["to"]; ?>" onclick="fillProducts( this.id, '<?php echo $_price_list_item["from"];?>', '<?php echo $_price_list_item["to"]; ?>', request_obj_<?php echo $vcode; ?>, 1 )"  style="color:<?php echo $this->_config["price_tab_text_color"]; ?>;background-color:<?php echo $this->_config["price_tab_background_color"]; ?>;" >
							<div class="price-item-text"  onmouseout="price_tab_ms_out( this.parentNode )" onmouseover="price_tab_ms_hover( this.parentNode )">
								<?php echo get_woocommerce_currency_symbol().$_price_list_item["from"].' - '.get_woocommerce_currency_symbol().$_price_list_item["to"]; ?>
							</div>
							<div class="ld-price-item-text"></div>
							<div class="clr"></div>
						</div>						
						<div class="item-products"></div>
						<div class="clr"></div>
					 </div> 
					 <div class="clr"></div>
				   <?php
				}
			}			
		?>
		<div class="clr"></div>
	</div>
</div>