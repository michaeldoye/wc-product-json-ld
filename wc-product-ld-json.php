<?php
/*
Plugin Name: WC Product LD JSON
Plugin URI: https://webseo.co.za
Description: Adds relevant LD JSON to prodcut pages
Author: Web SEO Online (PTY) LTD
Author URI: https://webseo.co.za
Version: 0.0.1

	Copyright: © 2016 Web SEO Online (PTY) LTD (email : michael@webseo.co.za)
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/



if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { 

	/**
	* Make sure class doesn't already exist
	*/

	if ( ! class_exists( 'WC_Product_ld' ) ) {
		
		/**
		* Localisation
		**/
		load_plugin_textdomain( 'WC_Product_ld', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

		class WC_Product_ld {

			/**
			* constructor
			*/
			public function __construct() {
				add_filter( 'wp_head', array( $this, 'add_ld_script') );	            			
			}

			/**
			* add_ld_script
			* Checks post type and injects JSON LD data.
			**/
			public function add_ld_script() {
				global $woocommerce, $post;

				$product = wc_get_product( $post->ID );
				$terms = get_the_terms( $post->ID, 'product_cat' );
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );

				if ( is_product() ) { ?>
					<script type="application/ld+json">
					{
						"@context": "http://schema.org/",
						"@type": "Product",
						"name": "<?php echo $product->get_title() ?>",
						"image": "<?php echo $image[0] ?>",
						"description": "<?php echo get_post( $post->ID )->post_content ?>",
						"mpn": "<?php echo $product->get_sku() ?>",
						"brand": {
							"@type": "Thing",
							"name": "<?php echo $terms[0]->name ?>"
						},
						"aggregateRating": {
							"@type": "AggregateRating",
							"ratingValue": "<?php echo $product->get_average_rating() ?>",
							"reviewCount": "<?php echo $product->get_review_count() ?>"
						},
						"offers": {
							"@type": "Offer",
							"priceCurrency": "ZAR",
							"price": "<?php echo $product->get_price() ?>",
							"itemCondition": "http://schema.org/UsedCondition",
							"availability": "http://schema.org/InStock",
							"seller": {
							"@type": "Organization",
							"name": "Executive Objects"
							}
						}
					}
					</script>
				<?php } 
			}

		}
		
		// finally instantiate our plugin class and add it to the set of globals
		$GLOBALS['WC_Product_ld'] = new WC_Product_ld();
	}

}