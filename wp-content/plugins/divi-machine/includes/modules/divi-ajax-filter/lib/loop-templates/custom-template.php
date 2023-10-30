<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// get the post ID
$post_id = get_the_ID();

?>
<article id="<?php echo $post_id; ?>" <?php post_class( 'et_pb_post clearfix grid-item'); ?>>
<div class="grid-item-cont">
<p>
    Here you can create your own custom template for each post in the loop. To do this you will need to create a child theme if you do not have one.
    <br><br>
    Create the folder /divi-ajax-filter/loop-templates/custom-template.php 
    <br><br>
    Add the content you want for each post there.
</p>
</div>
</article>
<?php