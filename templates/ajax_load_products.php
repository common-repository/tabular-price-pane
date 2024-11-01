<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
  
	 $params = $_REQUEST;  
	 $_from = ( isset( $params["from"] ) ? intval( $params["from"] ) : 0 );
	 $_to =( isset( $params["to"] ) ? intval( $params["to"] ) : 0 ); 
	 $category_id =( isset( $params["category_id"] ) ? intval( $params["category_id"] ) : 0 ); 
	 $_limit_start =( isset( $params["limit_start"] ) ? intval( $params["limit_start"] ) : 0 );
	 $_limit_end = intval( $params["number_of_product_display"] );
	 $is_default_category_with_hidden = 1; 
	 
	?><script language='javascript'>
		var request_obj_<?php echo esc_js( $params["vcode"] ); ?> = {
			category_id:'<?php echo esc_js( $category_id ); ?>',
			hide_product_price:'<?php echo esc_js( $params["hide_product_price"] ); ?>', 
			hide_product_title:'<?php echo esc_js( $params["hide_product_title"] ); ?>', 
			product_price_color:'<?php echo esc_js( $params["product_price_color"] ); ?>',
			product_title_color:'<?php echo esc_js( $params["product_title_color"] ); ?>', 
			price_tab_text_color:'<?php echo esc_js( $params["price_tab_text_color"] ); ?>',
			price_tab_background_color:'<?php echo esc_js( $params["price_tab_background_color"] ); ?>', 
			header_text_color:'<?php echo esc_js( $params["header_text_color"] ); ?>', 
			header_background_color:'<?php echo esc_js( $params["header_background_color"] ); ?>',
			display_title_price_over_image:'<?php echo esc_js( $params["display_title_price_over_image"] ); ?>', 
			number_of_product_display:'<?php echo esc_js( $params["number_of_product_display"] ); ?>',
			vcode:'<?php echo esc_js( $params["vcode"] ); ?>'
		}
	</script> 
	<?php     
	$_total_products = $this->tpp_getTotalProducts( $_from, $_to, $category_id, 1, $is_default_category_with_hidden );
	if( $_total_products <= 0 ) {
		?><div class="ik-product-no-items"><?php echo __( 'No products found.', 'tabular_price_pane' ); ?></div><?php
		die();
	} 
	$product_list = $this->tpp_getProductList( $category_id, $_from, $_to, $_limit_end );	 
	 
	foreach ( $product_list as $_product ) { 
		$image  = $this->tpp_getProductImage( $_product->product_image );
		?>
		<div class='ik-product-item pid-<?php echo esc_attr( $_product->product_id ); ?>'> 
			<div class='ik-product-image' onmouseout="pr_item_image_mouseout(this)" onmouseover="pr_item_image_mousehover(this)">
					<a href="<?php echo esc_url( get_permalink($_product->product_id) ); ?>">
					<div class="ov-layer" > 
						 <?php if( $params["display_title_price_over_image"] == 'yes' ) { ?> 
								<div class='ik-overlay-product-content'>
									<?php if( $params["hide_product_title"] == 'no' ) { ?> 
										<div class='ik-product-name' style="color:<?php echo esc_attr( $params["product_title_color"] ); ?>" >
											 <?php echo esc_html( $_product->product_name ); ?>
										</div>
									<?php } ?> 
									<?php if( $params["hide_product_price"] == 'no' ) { ?> 
										<div class='ik-product-sale-price' style="color:<?php echo esc_attr( $params["product_price_color"] ); ?>" >
											<?php echo get_woocommerce_currency_symbol().$_product->sale_price; ?>
										</div>
									<?php } ?> 
									<?php  if($_product->post_type == "product" ){ ?>
										<?php echo do_shortcode("[add_to_cart show_price='false' style='' id = '".$_product->product_id."']"); ?>
									<?php  } ?>
									<div class="clr"></div>
								</div>
								<div class="clr"></div>
						<?php } ?>
					</div>
					<div class="clr"></div>
				</a>
				<div class="clr"></div>
				<a href="<?php echo esc_url( get_permalink( $_product->product_id ) ); ?>"> 
					<?php echo $image; ?>
				</a>   
			</div>  
			<?php if( $params["display_title_price_over_image"] == 'no' ) { ?> 
				<div class='ik-product-content'>
					<?php if($params["hide_product_title"]=='no'){ ?> 
						<div class='ik-product-name'>
							<a href="<?php echo esc_url( get_permalink( $_product->product_id ) ); ?>" style="color:<?php echo esc_attr( $params["product_title_color"] ); ?>" >
								<?php echo esc_html( $_product->product_name ); ?>
							</a>	
						</div>
					<?php } ?>	
					
					<?php if($params["hide_product_price"]=='no') { ?> 
						<div class='ik-product-sale-price' style="color:<?php echo esc_attr( $params["product_price_color"] ); ?>">
							<?php echo get_woocommerce_currency_symbol().$_product->sale_price; ?>
						</div>
					<?php } ?>	 
					<?php  if($_product->post_type == "product" ) { ?>
						<?php echo do_shortcode("[add_to_cart show_price='false' style='' id = '".$_product->product_id."']"); ?>
					<?php  } ?>
				</div>	
			<?php } ?> 
		</div> 
		<?php 
	}
	
	if( $_total_products > $params["number_of_product_display"] ) { ?>
			<div class="clr"></div>
			<div class='ik-product-load-more'  align="center" onclick='loadMoreProducts( "<?php echo esc_js( $params["from"] ); ?>", "<?php echo esc_js( $params["to"] ); ?>", "<?php echo $_limit_start + $_limit_end; ?>", "<?php echo esc_js( $params["vcode"]."-".$params["from"]."-".$params["to"] ); ?>", "<?php echo esc_js( $_total_products ); ?>", request_obj_<?php echo esc_js( $params["vcode"] ); ?> )'>
				<?php echo __('Load More', 'tabular_price_pane' ); ?>
			</div>
		<?php  
	} else {
		?><div class="clr"></div><?php
	}