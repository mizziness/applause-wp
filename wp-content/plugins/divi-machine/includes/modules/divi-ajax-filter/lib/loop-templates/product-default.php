<?php

if ( ! defined( 'ABSPATH' ) ) exit;


$is_product = (class_exists('woocommerce') && get_post_type() == "product");

if (!$is_product) {
    return;
}
?>
    <?php
    /**
     * Hook: woocommerce_shop_loop.
     *
     * @hooked WC_Structured_Data::generate_product_data() - 10
     */

    do_action('woocommerce_shop_loop');
    // wc_get_template_part('content', 'product');
    global $product;

    // Ensure visibility.
    if ( empty( $product ) || ! $product->is_visible() ) {
        return;
    }
    ?>
    <li <?php wc_product_class( 'daf-template-loop daf-product-template daf-product-template-default grid-item', $product ); ?>>
      <div class="grid-item-cont">
      <?php
        /**
         * Hook: woocommerce_before_shop_loop_item.
         *
         * @hooked woocommerce_template_loop_product_link_open - 10
         */
        do_action( 'woocommerce_before_shop_loop_item' );
    
        /**
         * Hook: woocommerce_before_shop_loop_item_title.
         *
         * @hooked woocommerce_show_product_loop_sale_flash - 10
         * @hooked woocommerce_template_loop_product_thumbnail - 10
         */
        do_action( 'woocommerce_before_shop_loop_item_title' );
    
        /**
         * Hook: woocommerce_shop_loop_item_title.
         *
         * @hooked woocommerce_template_loop_product_title - 10
         */
        do_action( 'woocommerce_shop_loop_item_title' );
    
        /**
         * Hook: woocommerce_after_shop_loop_item_title.
         *
         * @hooked woocommerce_template_loop_rating - 5
         * @hooked woocommerce_template_loop_price - 10
         */
        do_action( 'woocommerce_after_shop_loop_item_title' );

        // if $fullwidth is not set, set it as ''
        if (!isset($fullwidth)) {
            $fullwidth = '';
        }        

        if ($fullwidth == 'grid_list' && $show_excerpt == 'on') {
            // get the product excerpt
            $excerpt = $product->get_short_description();
            // echo the excerpt
            echo '<div class="product-short-description">' . wp_kses_post($excerpt) . '</div>';
            
        }
    
        /**
         * Hook: woocommerce_after_shop_loop_item.
         *
         * @hooked woocommerce_template_loop_product_link_close - 5
         * @hooked woocommerce_template_loop_add_to_cart - 10
         */
        // do_action( 'woocommerce_after_shop_loop_item' );
        woocommerce_template_loop_product_link_close();
        if ($show_variations == 'on') {
            woocommerce_template_single_add_to_cart();
        } else {
            woocommerce_template_loop_add_to_cart();
        }
        ?>
        </div>
    </li>
<?php 
