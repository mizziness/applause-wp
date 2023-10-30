<?php

if ( ! defined( 'ABSPATH' ) ) exit;


$is_product = (class_exists('woocommerce') && get_post_type() == "product");

if (!$is_product) {
    return;
}

if (!isset($link_title)) {
    $link_title = 'yes';
}

?>
<div class="daf-template-loop daf-product-template daf-product-template-simple grid-item">
    <div class="grid-item-cont">
    <div <?php wc_product_class( '', $product ); ?>>
    
        <?php

        /**
        * Hook: woocommerce_before_shop_loop_item_title.
        *
        * @hooked woocommerce_show_product_loop_sale_flash - 10
        * @hooked woocommerce_template_loop_product_thumbnail - 10
        */
        do_action( 'woocommerce_before_shop_loop_item_title' );

    
        // Product title
        echo '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        echo "yere";

	    /**
	     * Hook: woocommerce_after_shop_loop_item_title.
	     *
	     * @hooked woocommerce_template_loop_rating - 5
	     * @hooked woocommerce_template_loop_price - 10
	     */
	    do_action( 'woocommerce_after_shop_loop_item_title' );

	    /**
	     * Hook: woocommerce_template_loop_add_to_cart.
	     *
	     */
        do_action( 'woocommerce_template_loop_add_to_cart' );

        ?>

    </div> <!-- product -->
</div>
</div> <!-- daf-template-loop -->
<?php

?>