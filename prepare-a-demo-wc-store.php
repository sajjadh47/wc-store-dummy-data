<?php
/**
 * Plugin Name: Prepare A Demo WooCommerce Store
 * Plugin URI: https://sajjadhsagor.com/
 * Description: A Plugin to spin off a demo wc store with least necessary data.
 * Version: 1.0.0
 * Author: Sajjad Hossain Sagor
 * Author URI: https://sajjadhsagor.com
 * Text Domain: prepare-a-demo-wc-store
 * Domain Path: /languages/
 * Requires at least: 6.6
 * Requires PHP: 8.0
 *
 * @package PrepareADemoWooCommerceStore
 */

/**
 * Plugin Setup Script for WooCommerce.
 *
 * Performs:
 * - Preventing setup wizard redirect
 * - Custom permalink structure
 * - Adds flat rate shipping method if not present
 * - Enables Cash on Delivery payment gateway
 * - Import demo products
 */

defined( 'ABSPATH' ) || exit;

// Prevent WooCommerce from automatically launching the Setup Wizard on activation.
add_filter( 'woocommerce_prevent_automatic_wizard_redirect', '__return_true' );

/**
 * Configure permalink structure and clean activation redirect.
 */
function padwcs_wc_custom_setup_configure_permalink_and_redirect() {
	global $wp_rewrite;

	// Set permalink structure to post name.
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	$wp_rewrite->flush_rules();

	// Remove WooCommerce activation redirect flag.
	delete_transient( '_wc_activation_redirect' );
}
add_action( 'after_setup_theme', 'padwcs_wc_custom_setup_configure_permalink_and_redirect' );

/**
 * Register Flat Rate shipping method if not already set.
 */
function padwcs_wc_custom_setup_register_flat_rate_shipping() {
	// Ensure WooCommerce shipping classes are loaded.
	if ( ! class_exists( 'WC_Shipping_Zone' ) ) {
		return;
	}

	// Use zone ID 0 which refers to the 'Everywhere' shipping zone.
	$zone             = new WC_Shipping_Zone( 0 );
	$shipping_methods = $zone->get_shipping_methods();
	$method_exists    = false;

	// Check if flat rate method already exists.
	foreach ( $shipping_methods as $method ) {
		if ( 'flat_rate' === $method->id ) {
			$method_exists = true;
			break;
		}
	}

	if ( $method_exists ) {
		return;
	}

	// Add flat rate shipping method to the zone.
	$instance_id = $zone->add_shipping_method( 'flat_rate' );

	if ( ! $instance_id ) {
		return;
	}

	// Configure flat rate shipping settings.
	$settings = array(
		'title'      => __( 'Flat Rate', 'woocommerce' ),
		'cost'       => 10,
		'tax_status' => 'none',
	);

	// Save settings to the corresponding option.
	update_option( 'woocommerce_flat_rate_' . $instance_id . '_settings', $settings );
}
add_action( 'init', 'padwcs_wc_custom_setup_register_flat_rate_shipping' );

/**
 * Enable WooCommerce Cash on Delivery payment gateway.
 */
function padwcs_wc_custom_setup_enable_cod_gateway() {
	// Ensure WooCommerce payment classes are loaded.
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	// Get current Cash on Delivery settings.
	$cod_settings = get_option( 'woocommerce_cod_settings', array() );

	// Enable COD if it's not already enabled.
	if ( ! isset( $cod_settings['enabled'] ) || 'yes' !== $cod_settings['enabled'] ) {
		$cod_settings['enabled'] = 'yes';
		update_option( 'woocommerce_cod_settings', $cod_settings );
	}
}
add_action( 'woocommerce_init', 'padwcs_wc_custom_setup_enable_cod_gateway' );

/**
 * Import content.
 */
function padwcs_import_content() {
	?>
		<div class="demo-importing-container">
			<h3 class="demo-importing-title" id="demo-importing-title">🚀 Hang Tight — Your Demo Site is on Its Way...</h3>
			<div class="progress">
				<div class="demo-importing-progress-bar" id="demo-importing-progress-bar" style="width: 1%;"></div>
			</div>
		</div>
		<style type="text/css">html,body,.demo-importing-container{overflow:hidden;}.demo-importing-container{position:fixed;top:0;left:0;background:#1e1e1e;color:#fff;z-index:9999999999999;display:flex;flex-direction:column;width:100%;height:100%;justify-content:center;align-items:center;opacity:1;transition:opacity ease-in .25s;gap:20px}.demo-importing-progress{position:relative;width:512px;max-width:60vw;height:4px;margin:4px auto;border-radius:10px;background:#32363a}.demo-importing-progress .demo-importing-progress-bar{opacity:0}.demo-importing-progress .demo-importing-progress-bar{opacity:1}.demo-importing-progress-bar{position:absolute;inset:0 100% 0 0;width:0;background:#3858e9;border-radius:2px;transition:opacity linear .2s,width ease-in .2s;}.demo-importing-title{font-weight:400;font-family:-apple-system,BlinkMacSystemFont,sans-serif;font-size:1.1rem;margin:0;color:white;}</style>
		<script type="text/javascript">
		window.addEventListener( 'load', function() {
			var progressInterval;

			function updateProgress( percent )
			{
				const heading 					= document.getElementById( 'demo-importing-title' );

				const progressBar 				= document.getElementById( 'demo-importing-progress-bar' );

				heading.textContent 			= '⚙️ Initiating setup process...';

				const messages 					=
				[
					{ percent: 10, text: '🚀 Hang Tight — Your Demo Site is on Its Way...' },
					{ percent: 20, text: '🔧 Spinning up your site environment...' },
					{ percent: 30, text: '📥 Importing demo content...' },
					{ percent: 70, text: '🧹 Final cleanup and optimization...' },
					{ percent: 90, text: '🎉 Ready! Launching your demo site...' }
				];

				let currentMessageIndex 		= 0;

				progressBar.style.width 		= percent + '%';

				for ( let i = messages.length - 1; i >= 0; i-- )
				{
					if ( percent >= messages[i].percent )
					{
						if ( i !== currentMessageIndex )
						{
							heading.textContent = messages[i].text;

							currentMessageIndex = i;
						}

						break;
					}
				}
			}

			function setErrorMessage( message )
			{
				clearInterval( progressInterval );

				const progressBar 				= document.getElementById( 'demo-importing-progress-bar' );

				const heading 					= document.getElementById( 'demo-importing-title' );

				progressBar.style.width 		= '100%';

				heading.textContent				= message;
			}

			function create_demo()
			{
				var currentProgress				= 1;

				progressInterval 				= setInterval( () =>
				{
					currentProgress 			+= 10;

					if ( currentProgress > 100 )
					{
						clearInterval( progressInterval ); return;
					}

					updateProgress( currentProgress );

				}, 1000 );

				const heading 					= document.getElementById( 'demo-importing-title' );

				fetch( '/wp-admin/admin-ajax.php?action=padwcs_import_content' ).then( response => response.json() ).then( data =>
				{
					if ( data.success )
					{
						setTimeout( () =>
						{
							window.location.href = data.data.site_url;

						}, 1000 );
					}
					else
					{
						setErrorMessage( 'Error: ' + data.data.message );
					}
				} )
				.catch( error =>
				{
					setErrorMessage( 'Something went wrong! Please try again.' );
				} );
			}

			create_demo();
		} );
		</script>
	<?php
}
add_action( 'admin_footer', 'padwcs_import_content' );

add_action( 'wp_ajax_padwcs_import_content', 'padwcs_import_woocommerce_products_from_csv' );

/**
 * Imports sample WooCommerce products from a predefined CSV string.
 *
 * This function creates simple and variable products, their categories, tags,
 * and images.
 *
 * Note: This function assumes WooCommerce classes (WC_Product_Simple, WC_Product_Variable,
 * WC_Product_Variation, WC_Product_Attribute) are available.
 *
 * @global object $wpdb WordPress database abstraction object.
 * @return void
 */
function padwcs_import_woocommerce_products_from_csv() {
	// CSV data as a string. In a real application, this might be read from a file.
	$csv_data = <<<CSV
ID,Type,SKU,Name,Published,Is featured?,Visibility in catalog,Short description,Description,Date sale price starts,Date sale price ends,Tax status,Tax class,In stock?,Stock,Backorders allowed?,Sold individually?,Weight (lbs),Length (in),Width (in),Height (in),Allow customer reviews?,Purchase note,Sale price,Regular price,Categories,Tags,Shipping class,Images,Download limit,Download expiry days,Parent,Grouped products,Upsells,Cross-sells,External URL,Button text,Position,Attribute 1 name,Attribute 1 value(s),Attribute 1 visible,Attribute 1 global,Attribute 2 name,Attribute 2 value(s),Attribute 2 visible,Attribute 2 global,Download 1 name,Download 1 URL,Download 2 name,Download 2 URL
1,simple,woo-hoodie-with-logo,Hoodie with Logo,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,50,0,0,1.5,2.5,3.5,4.5,1,,2,5,Clothing > Hoodies,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-with-logo-2.jpg,,,,,,,,,0,,,,,,,,,,,,
2,simple,woo-tshirt,T-Shirt,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,25,0,0,1.5,2.5,3.5,4.5,1,,5,10,Clothing > Tshirts,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/tshirt-2.jpg,,,,,,,,,0,,,,,,,,,,,,
3,simple,woo-beanie,Beanie,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,16,0,0,1.5,2.5,3.5,4.5,1,,10,15,Clothing > Accessories,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/beanie-2.jpg,,,,,,,,,0,,,,,,,,,,,,
4,simple,woo-belt,Belt,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,50,0,0,1.5,2.5,3.5,4.5,1,,15,20,Clothing > Accessories,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/belt-2.jpg,,,,,,,,,0,,,,,,,,,,,,
5,simple,woo-cap,Cap,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,25,0,0,1.5,2.5,3.5,4.5,1,,20,25,Clothing > Accessories,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/cap-2.jpg,,,,,,,,,0,,,,,,,,,,,,
6,simple,woo-sunglasses,Sunglasses,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,16,0,0,1.5,2.5,3.5,4.5,1,,25,30,Clothing > Accessories,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/sunglasses-2.jpg,,,,,,,,,0,,,,,,,,,,,,
7,simple,woo-long-sleeve-tee,Long Sleeve Tee,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,50,0,0,1.5,2.5,3.5,4.5,1,,30,35,Clothing > Tshirts,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/long-sleeve-tee-2.jpg,,,,,,,,,0,,,,,,,,,,,,
8,simple,woo-polo,Polo,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,25,0,0,1.5,2.5,3.5,4.5,1,,35,40,Clothing > Tshirts,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/polo-2.jpg,,,,,,,,,0,,,,,,,,,,,,
9,simple,woo-album,Album,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,16,0,0,1.5,2.5,3.5,4.5,1,,40,45,Music,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2022/05/album-1.jpg,,,,,,,,,0,,,,,,,,,,,,
10,simple,woo-single,Single,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,50,0,0,1.5,2.5,3.5,4.5,1,,45,50,Music,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/single-1.jpg,,,,,,,,,0,,,,,,,,,,,,
11,simple,Woo-tshirt-logo,T-Shirt with Logo,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,25,0,0,1.5,2.5,3.5,4.5,1,,50,55,Clothing > Tshirts,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/t-shirt-with-logo-1.jpg,,,,,,,,,0,,,,,,,,,,,,
12,simple,Woo-beanie-logo,Beanie with Logo,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,16,0,0,1.5,2.5,3.5,4.5,1,,55,60,Clothing > Accessories,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/beanie-with-logo-1.jpg,,,,,,,,,0,,,,,,,,,,,,
13,simple,logo-collection,Logo Collection,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,50,0,0,1.5,2.5,3.5,4.5,1,,60,65,Clothing,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/logo-1.jpg,,,,,,,,,0,,,,,,,,,,,,
14,simple,wp-pennant,WordPress Pennant,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,25,0,0,1.5,2.5,3.5,4.5,1,,65,70,Decor,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/pennant-1.jpg,,,,,,,,,0,,,,,,,,,,,,
15,variable,woo-hoodie-with-pocket,Hoodie with Pocket,1,0,hidden,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,0,,0,0,1.5,2.5,3.5,4.5,1,,,,Clothing > Hoodies,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-with-pocket-2.jpg,,,,,,,,,0,Color,"Blue, Green, Red",1,1,,,,,,,,
16,variable,woo-hoodie-with-zipper,Hoodie with Zipper,1,0,visible,This is a simple product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,0,,0,0,1.5,2.5,3.5,4.5,1,,,,Clothing > Hoodies,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-with-zipper-2.jpg,,,,,,,,,0,Color,"Blue, Green, Red",1,1,,,,,,,,
17,variable,woo-vneck-tee,V-Neck T-Shirt,1,0,visible,This is a variable product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,0,,0,0,1.5,2.5,3.5,4.5,1,,,,Clothing > Tshirts,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vneck-tee-2.jpg,,,,,,,,,0,Color,"Blue, Green, Red",1,1,,,,,,,,
18,variable,woo-hoodie,Hoodie,1,0,visible,This is a variable product.,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,0,,0,0,1.5,2.5,3.5,4.5,1,,,,Clothing > Hoodies,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-2.jpg,,,,,,,,,0,Color,"Blue, Green, Red",1,1,,,,,,,,
19,variation,woo-vneck-tee-red,V-Neck T-Shirt - Red,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,50,0,0,1.5,2.5,3.5,4.5,0,,70,75,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vneck-tee-2.jpg,,,woo-vneck-tee,,,,,,1,Color,Red,,1,,,,,,,,
20,variation,woo-vneck-tee-green,V-Neck T-Shirt - Green,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,25,0,0,1.5,2.5,3.5,4.5,0,,75,80,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vnech-tee-green-1.jpg,,,woo-vneck-tee,,,,,,2,Color,Green,,1,,,,,,,,
21,variation,woo-vneck-tee-blue,V-Neck T-Shirt - Blue,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,16,0,0,1.5,2.5,3.5,4.5,0,,80,85,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vnech-tee-blue-1.jpg,,,woo-vneck-tee,,,,,,3,Color,Blue,,1,,,,,,,,
22,variation,woo-hoodie-red,Hoodie - Red,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,50,0,0,1.5,2.5,3.5,4.5,0,,85,90,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-2.jpg,,,woo-hoodie,,,,,,1,Color,Red,,1,,,,,,,,
23,variation,woo-hoodie-green,Hoodie - Green,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,25,0,0,1.5,2.5,3.5,4.5,0,,90,95,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-green-1.jpg,,,woo-hoodie,,,,,,2,Color,Green,,1,,,,,,,,
24,variation,woo-hoodie-blue,Hoodie - Blue,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,16,0,0,1.5,2.5,3.5,4.5,0,,95,100,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-blue-1.jpg,,,woo-hoodie,,,,,,3,Color,Blue,,1,,,,,,,,
25,variation,woo-hoodie-with-pocket-red,Hoodie with Pocket - Red,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,50,0,0,1.5,2.5,3.5,4.5,0,,100,105,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vneck-tee-2.jpg,,,woo-hoodie-with-pocket,,,,,,1,Color,Red,,1,,,,,,,,
26,variation,woo-hoodie-with-pocket-green,Hoodie with Pocket - Green,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,25,0,0,1.5,2.5,3.5,4.5,0,,105,110,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vnech-tee-green-1.jpg,,,woo-hoodie-with-pocket,,,,,,2,Color,Green,,1,,,,,,,,
27,variation,woo-hoodie-with-pocket-blue,Hoodie with Pocket - Blue,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,16,0,0,1.5,2.5,3.5,4.5,0,,110,115,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vnech-tee-blue-1.jpg,,,woo-hoodie-with-pocket,,,,,,3,Color,Blue,,1,,,,,,,,
28,variation,woo-hoodie-with-zipper-red,Hoodie with Zipper - Red,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,50,0,0,1.5,2.5,3.5,4.5,0,,115,120,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-2.jpg,,,woo-hoodie-with-zipper,,,,,,1,Color,Red,,1,,,,,,,,
29,variation,woo-hoodie-with-zipper-green,Hoodie with Zipper - Green,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,25,0,0,1.5,2.5,3.5,4.5,0,,120,125,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-green-1.jpg,,,woo-hoodie-with-zipper,,,,,,2,Color,Green,,1,,,,,,,,
30,variation,woo-hoodie-with-zipper-blue,Hoodie with Zipper - Blue,1,0,visible,,"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt.",,,taxable,,1,16,0,0,1.5,2.5,3.5,4.5,0,,125,130,,,,https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-blue-1.jpg,,,woo-hoodie-with-zipper,,,,,,3,Color,Blue,,1,,,,,,,,
CSV;

	// Parse CSV into structured arrays.
	$lines      = explode( "\n", $csv_data );
	$headers    = str_getcsv( (string) array_shift( $lines ) ); // Remove and parse header row.
	$products   = array();
	$variations = array();

	foreach ( $lines as $line ) {
		if ( empty( trim( $line ) ) ) {
			continue;
		}

		$row = str_getcsv( $line );

		// Combine headers with row data.
		$data = array_combine( $headers, $row );

		// Separate products and variations.
		if ( 'variation' === $data['Type'] ) {
			$variations[ $data['Parent'] ][] = $data;
		} else {
			$products[ $data['SKU'] ] = $data;
		}
	}

	// Process each product.
	foreach ( $products as $sku => $product_data ) {
		$product_type = $product_data['Type'];
		$sku          = $product_data['SKU'];

		// Create product object based on type.
		$product = null;

		if ( 'simple' === $product_type ) {
			$product = new WC_Product_Simple();
		} elseif ( 'variable' === $product_type ) {
			$product = new WC_Product_Variable();
		} else {
			continue; // Skip unsupported product types.
		}

		// Set basic product data.
		$product->set_name( sanitize_text_field( $product_data['Name'] ) );
		$product->set_sku( sanitize_text_field( $sku ) );
		$product->set_status( (bool) $product_data['Published'] ? 'publish' : 'draft' );
		$product->set_featured( (bool) $product_data['Is featured?'] );
		$product->set_catalog_visibility( sanitize_text_field( $product_data['Visibility in catalog'] ) );
		$product->set_short_description( wp_kses_post( $product_data['Short description'] ) );
		$product->set_description( wp_kses_post( $product_data['Description'] ) );
		$product->set_regular_price( wc_format_decimal( $product_data['Regular price'] ) );
		// phpcs:ignore Universal.Operators.DisallowShortTernary.Found
		$product->set_sale_price( wc_format_decimal( $product_data['Sale price'] ?: '' ) );
		$product->set_tax_status( sanitize_text_field( $product_data['Tax status'] ) );
		// phpcs:ignore Universal.Operators.DisallowShortTernary.Found
		$product->set_tax_class( sanitize_text_field( $product_data['Tax class'] ?: '' ) );
		$product->set_manage_stock( (bool) $product_data['In stock?'] );
		$product->set_stock_quantity( ( ! empty( $product_data['Stock'] ) ) ? absint( $product_data['Stock'] ) : null );
		$product->set_backorders( (bool) $product_data['Backorders allowed?'] ? 'yes' : 'no' );
		$product->set_sold_individually( (bool) $product_data['Sold individually?'] );
		$product->set_weight( wc_format_decimal( $product_data['Weight (lbs)'] ) );
		$product->set_length( wc_format_decimal( $product_data['Length (in)'] ) );
		$product->set_width( wc_format_decimal( $product_data['Width (in)'] ) );
		$product->set_height( wc_format_decimal( $product_data['Height (in)'] ) );
		$product->set_reviews_allowed( (bool) $product_data['Allow customer reviews?'] );
		$product->set_purchase_note( wp_kses_post( $product_data['Purchase note'] ) );

		// Handle categories.
		if ( ! empty( $product_data['Categories'] ) ) {
			$categories   = array_map( 'trim', explode( '>', $product_data['Categories'] ) );
			$parent_id    = 0;
			$category_ids = array();

			foreach ( $categories as $cat_name ) {
				$term     = term_exists( $cat_name, 'product_cat', $parent_id );

				if ( ! $term ) {
					$term = wp_insert_term( $cat_name, 'product_cat', array( 'parent' => $parent_id ) );
				}

				$term_id  = is_array( $term ) ? $term['term_id'] : $term;

				if ( ! is_wp_error( $term_id ) ) {
					$category_ids[] = absint( $term_id );
					$parent_id      = absint( $term_id );
				}
			}

			$product->set_category_ids( $category_ids );
		}

		// Handle tags.
		if ( ! empty( $product_data['Tags'] ) ) {
			$tags = array_map( 'sanitize_text_field', array_map( 'trim', explode( ',', $product_data['Tags'] ) ) );

			wp_set_object_terms( $product->get_id(), $tags, 'product_tag', false );
		}

		// Handle image.
		if ( ! empty( $product_data['Images'] ) ) {
			$image_url = esc_url_raw( $product_data['Images'] );
			$image_id  = padwcs_upload_image_from_url( $image_url, $product_data['Name'] );

			if ( $image_id ) {
				$product->set_image_id( $image_id );
			}
		}

		// Save product to get an ID.
		$product_id = $product->save();

		// Handle meta fields.
		foreach ( $product_data as $key => $value ) {
			if ( strpos( $key, 'Meta: ' ) === 0 && ! empty( $value ) ) {
				$meta_key = substr( $key, 6 ); // Remove 'Meta: ' prefix.

				update_post_meta( $product_id, sanitize_key( $meta_key ), sanitize_text_field( $value ) );
			}
		}

		// Handle attributes for variable products.
		if ( 'variable' === $product_type && ! empty( $product_data['Attribute 1 name'] ) ) {
			$attributes  = array();
			$attr_name   = sanitize_text_field( $product_data['Attribute 1 name'] );
			$attr_values = array_map( 'sanitize_text_field', array_map( 'trim', explode( ',', $product_data['Attribute 1 value(s)'] ) ) );

			$attribute = new WC_Product_Attribute();
			$attribute->set_name( $attr_name );
			$attribute->set_options( $attr_values );
			$attribute->set_position( 0 );
			$attribute->set_visible( (bool) $product_data['Attribute 1 visible'] );
			$attribute->set_variation( (bool) $product_data['Attribute 1 global'] );

			$attributes[] = $attribute;

			$product->set_attributes( $attributes );
			$product->save(); // Save again to update attributes.
		}

		// Handle variations for variable products.
		if ( 'variable' === $product_type && isset( $variations[ $sku ] ) ) {
			foreach ( $variations[ $sku ] as $variation_data ) {
				$variation = new WC_Product_Variation();
				$variation->set_parent_id( $product_id );
				$variation->set_sku( sanitize_text_field( $variation_data['SKU'] ) );
				$variation->set_name( sanitize_text_field( $variation_data['Name'] ) );
				$variation->set_status( (bool) $variation_data['Published'] ? 'publish' : 'draft' );
				$variation->set_regular_price( wc_format_decimal( $variation_data['Regular price'] ) );
				// phpcs:ignore Universal.Operators.DisallowShortTernary.Found
				$variation->set_sale_price( wc_format_decimal( $variation_data['Sale price'] ?: '' ) );
				$variation->set_manage_stock( (bool) $variation_data['In stock?'] );
				$variation->set_stock_quantity( ( ! empty( $variation_data['Stock'] ) ) ? absint( $variation_data['Stock'] ) : null );
				$variation->set_weight( wc_format_decimal( $variation_data['Weight (lbs)'] ) );
				$variation->set_length( wc_format_decimal( $variation_data['Length (in)'] ) );
				$variation->set_width( wc_format_decimal( $variation_data['Width (in)'] ) );
				$variation->set_height( wc_format_decimal( $variation_data['Height (in)'] ) );

				// Set variation attributes.
				$variation_attributes = array(
					strtolower( sanitize_key( $product_data['Attribute 1 name'] ) ) => sanitize_text_field( $variation_data['Attribute 1 value(s)'] ),
				);

				$variation->set_attributes( $variation_attributes );

				// Handle variation image.
				if ( ! empty( $variation_data['Images'] ) ) {
					$var_image_url = esc_url_raw( $variation_data['Images'] );

					$var_image_id  = padwcs_upload_image_from_url( $var_image_url, $variation_data['Name'] );

					if ( $var_image_id ) {
						$variation->set_image_id( $var_image_id );
					}
				}

				// Save variation and get ID.
				$variation_id = $variation->save();

				// Handle meta fields for variation.
				foreach ( $variation_data as $key => $value ) {
					if ( strpos( $key, 'Meta: ' ) === 0 && ! empty( $value ) ) {
						$meta_key = substr( $key, 6 ); // Remove 'Meta: ' prefix.

						update_post_meta( $variation_id, sanitize_key( $meta_key ), sanitize_text_field( $value ) );
					}
				}
			}
		}
	}

	wp_send_json_success( array( 'site_url' => trailingslashit( site_url() ) ) );

	die();
}

/**
 * Helper function to upload an image to the WordPress media library from a URL.
 *
 * This function downloads an image from a given URL and then uses WordPress's
 * media handling functions to sideload it into the media library.
 *
 * @param string $image_url    The URL of the image to download.
 * @param string $product_name The name of the product, used as title for the attachment.
 * @return int|false           The attachment ID on success, or false on failure.
 */

function padwcs_upload_image_from_url( $image_url, $product_name ) {
	// Ensure WordPress media functions are available.
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// Download the image to a temporary location.
	$tmp = download_url( $image_url );

	if ( is_wp_error( $tmp ) ) {
		return false;
	}

	// Prepare the file array for media_handle_sideload.
	$file_array = array(
		'name'     => basename( $image_url ),
		'tmp_name' => $tmp,
	);

	// Sideload the image into the media library.
	$id = media_handle_sideload( $file_array, 0, sanitize_text_field( $product_name ) );

	// Clean up the temporary file and handle errors.
	if ( is_wp_error( $id ) ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
		@unlink( $tmp ); // Delete the temporary file.

		return false;
	}

	return $id;
}
