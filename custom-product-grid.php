<?php
/*
Plugin Name: Custom Product Grid
Description: WooCommerce product grid with hover effect, per-product brand logo (media uploader), and optional slider view.
Version: 1.4
Author: Sayyid
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -------------------------
   Front-end assets
   ------------------------- */
function cpg_enqueue_frontend_assets() {
    // Bootstrap CSS
    wp_enqueue_style( 'cpg-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css', array(), '4.6.2' );

    // Font Awesome for arrows
    wp_enqueue_style( 'cpg-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2' );

    // Plugin CSS
    wp_enqueue_style( 'cpg-style', plugin_dir_url(__FILE__) . 'style.css', array('cpg-bootstrap'), '1.0' );

    // Bootstrap JS
    wp_enqueue_script( 'cpg-bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js', array('jquery'), '4.6.2', true );

    // Slick slider CSS & JS
    wp_enqueue_style( 'slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css' );
    wp_enqueue_style( 'slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css' );
    wp_enqueue_script( 'slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true );

    // Custom init
    wp_enqueue_script( 'cpg-slider-init', plugin_dir_url(__FILE__) . 'js/cpg-slider-init.js', array('jquery','slick-js'), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'cpg_enqueue_frontend_assets' );


/* -------------------------
   Shortcode: [product_grid layout="grid" columns="3" per_page="12" category="slug"]
   layout="grid" (default) or "slider"
   ------------------------- */
function cpg_display_product_grid( $atts ) {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return '<p>WooCommerce is not activated.</p>';
    }

    $atts = shortcode_atts( array(
        'layout'   => 'grid', // grid (default) or slider
        'columns'  => 3,
        'per_page' => 12,
        'category' => '',
    ), $atts, 'product_grid' );

    $layout   = $atts['layout'];
    $columns  = max(1, intval($atts['columns']));
    $per_page = max(1, intval($atts['per_page']));
    $category = sanitize_text_field( $atts['category'] );

    // Bootstrap col classes for grid
    switch ( $columns ) {
        case 1: $col_class = 'col-12'; break;
        case 2: $col_class = 'col-12 col-md-6 col-xl-6'; break;
        case 4: $col_class = 'col-12 col-md-3 col-xl-3'; break;
        case 3:
        default: $col_class = 'col-12 col-md-4 col-xl-4'; break;
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $per_page,
        'post_status'    => 'publish',
    );

    if ( ! empty( $category ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $loop = new WP_Query( $args );

    ob_start();

    if ( $loop->have_posts() ) {
        echo '<div class="product-section">';

        // Slider layout wrapper
       if ( $layout === 'slider' ) {
    echo '<div class="cpg-carousel-wrapper">';
    echo '<div class="cpg-slider">';
} else {
    echo '<div class="row">';
}


        while ( $loop->have_posts() ) : $loop->the_post();
            global $product;
            if ( ! $product ) continue;

            $product_id  = $product->get_id();
            $title       = get_the_title();
            $link        = get_the_permalink();
            $image       = get_the_post_thumbnail_url( $product_id, 'large' );
            $short_desc  = $product->get_short_description();
            if ( empty( $short_desc ) ) {
                $short_desc = wp_trim_words( wp_strip_all_tags( $product->get_description() ), 20, '...' );
            }

            $brand_img = get_post_meta( $product_id, '_brand_logo', true );
            if ( ! $image ) {
                $image = wc_placeholder_img_src();
            }

            ?>
            <div class="<?php echo $layout === 'grid' ? esc_attr($col_class) : 'cpg-slide'; ?>">
                <a href="<?php echo esc_url( $link ); ?>">
                    <figure>
                        <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>">
                        <span><?php echo esc_html( $title ); ?></span>
                        <figcaption>
                            <?php if ( $brand_img ) : ?>
                                <img src="<?php echo esc_url( $brand_img ); ?>" style="width:120px;height:70px" alt="<?php echo esc_attr( $title . ' brand' ); ?>">
                            <?php endif; ?>
                            <span><?php echo esc_html( $title ); ?></span>
                            <p><?php echo esc_html( wp_strip_all_tags( $short_desc ) ); ?></p>
                            <div class="arrow-btn">
                                <span>Learn More</span>
                             
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </figcaption>
                    </figure>
                </a>
            </div>
            <?php
        endwhile;

        echo '</div></div>'; // close row/slider + section
    } else {
        echo '<p>No products found</p>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'product_grid', 'cpg_display_product_grid' );


/* -------------------------
   Add Brand Logo field to product edit (media uploader)
   ------------------------- */
add_action( 'woocommerce_product_options_general_product_data', 'cpg_add_brand_logo_field' );
function cpg_add_brand_logo_field() {
    global $post;
    $brand_logo = get_post_meta( $post->ID, '_brand_logo', true );
    ?>
    <div class="options_group">
        <p class="form-field">
            <label for="_brand_logo"><?php _e( 'Brand Logo', 'cpg' ); ?></label><br/>
            <input type="text" id="_brand_logo" name="_brand_logo" value="<?php echo esc_attr( $brand_logo ); ?>" style="width:60%;" />
            <button type="button" class="button cpg-upload-logo">Upload Logo</button>
            <button type="button" class="button cpg-remove-logo">Remove</button>
            <span class="description">Upload or paste image URL for brand logo (shows on hover).</span>
        </p>
    </div>
    <?php
}

add_action( 'woocommerce_process_product_meta', 'cpg_save_brand_logo_field' );
function cpg_save_brand_logo_field( $post_id ) {
    if ( isset( $_POST['_brand_logo'] ) ) {
        update_post_meta( $post_id, '_brand_logo', esc_url_raw( $_POST['_brand_logo'] ) );
    }
}

/* -------------------------
   Admin: media uploader
   ------------------------- */
function cpg_admin_enqueue_scripts( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) return;
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'product' ) return;

    wp_enqueue_media();
    wp_enqueue_script( 'cpg-admin-js', plugin_dir_url(__FILE__) . 'js/cpg-admin.js', array('jquery'), '1.0', true );
    wp_add_inline_script( 'cpg-admin-js', "
        jQuery(function($){
            $(document).on('click', '.cpg-remove-logo', function(e){
                e.preventDefault();
                $('#_brand_logo').val('');
            });
        });
    " );
}
add_action( 'admin_enqueue_scripts', 'cpg_admin_enqueue_scripts' );
