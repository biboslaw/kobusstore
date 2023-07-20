<?php

namespace WowCode\Views;

use WowCode\Controllers\WooController as WooController;
use WowCode\Misc\Helper as Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

class SingleProduct {

    public function __construct()
    {
        remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
        remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
        add_action( 'wp', [ $this, 'moveBreadcrumbs' ] );
        add_action( 'wow_woocommerce_single_product_summary', [ $this, 'add_custom_single_product_display' ] );
        add_action( 'wow_add_quantity_form', 'woocommerce_template_single_add_to_cart' );
        add_action( 'wow-woocommerce_before_main_content', 'woocommerce_breadcrumb' );
        add_filter( 'woocommerce_breadcrumb_defaults', [ $this, 'woocommerce_filter_breadcrumb' ] );
    }

    public function add_custom_single_product_display() {

        $product = wc_get_product( get_the_ID() );

        $images_ids = $product-> get_gallery_image_ids();

        $featured_image = wp_get_attachment_image_src($product->image_id, 'large')[0];
        $featured_image_thumbnail = wp_get_attachment_image_src($product->image_id)[0];

        ob_start();
        ?>
        <section class="section section_product">
            <div class="container">
                <div class="wow-product">
                    <div class="product__label">
                        <svg class="svg" width="18" height="18">
                            <use xlink:href="spritemap.svg#sprite-search"></use>
                        </svg>
                    </div>
                    <div class="product__thumb">
                        <div class="swiper swiper-thumb js-swiper-thumb">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="swiper-zoom-container">
                                        <img src="<?php echo $featured_image ?>" />
                                    </div>
                                </div>
                                <?php
                                    foreach( $images_ids as $image ) {
                                        ?>
                                            <div class="swiper-slide">
                                                <div class="swiper-zoom-container">
                                                    <img src="<?php echo wp_get_attachment_image_src( $image, 'large' )[0] ?>" />
                                                </div>
                                            </div>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                        <div thumbsSlider="" class="swiper swiper-thumbs js-swiper-thumbs">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <img src="<?php echo $featured_image_thumbnail ?>" class="img-fluid" />
                                </div>
                                <?php
                                    foreach( $images_ids as $image ) {
                                        ?>
                                            <div class="swiper-slide">
                                                <div class="swiper-zoom-container">
                                                    <img src="<?php echo wp_get_attachment_image_src( $image )[0] ?>" />
                                                </div>
                                            </div>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                        $this->renderProdutDetails();
                    ?>
                </div>
            </div>
        </section>
        <?php

        // comments section start

        if( have_rows( 'commets' ) ) {
            ?>
                <section id="opinions" class="section">
                    <div class="swiper swiper-opinions">
                        <div class="swiper-wrapper">
                            <?php
                                while( have_rows( 'commets' ) ) {
                                    the_row();
                                    ?>
                                        <div class="swiper-slide">
                                            <div class="d-grid gap-4">
                                                <div class="opinion">
                                                    <div class="opinion-thumb">
                                                        <img src="<?php echo get_sub_field( 'first_comment_image' ) ?  get_sub_field( 'first_comment_image' ) : '' ?>" class="img-fluid rounded-circle" 
                                                            width="44" height="44" alt="">
                                                    </div>
                                                    <div class="opinion-body">
                                                        <?php echo get_sub_field( 'first_comment_content' ) ?  get_sub_field( 'first_comment_content' ) : '' ?>
                                                    </div>
                                                </div>
                                                <div class="opinion">
                                                    <div class="opinion-thumb">
                                                        <img src="<?php echo get_sub_field( 'second_comment_image' ) ?  get_sub_field( 'second_comment_image' ) : '' ?>" class="img-fluid rounded-circle" 
                                                            width="44" height="44" alt="">
                                                    </div>
                                                    <div class="opinion-body">
                                                        <?php echo get_sub_field( 'second_comment_content' ) ?  get_sub_field( 'second_comment_content' ) : '' ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                }
                            ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </section>
            <?php
        }
        // comments section end

        // features group
        if( have_rows( 'features_single' ) ) {
            ?>
                <section class="feature">
                    <div class="container">
                        <div class="feature__body">
                            <?php
                                while( have_rows( 'features_single' ) ) {
                                    the_row();
                                    $this->renderSingleFeatures();
                                }
                            ?>
                        </div>
                    </div>
                </section>
            <?php
        } else // main group
        if( have_rows( 'homepage_group', 'option' ) ) {
        
            while( have_rows( 'homepage_group', 'option' ) ) {
        
                the_row();
        
                if( have_rows( 'features', 'option' )) {
            
                    ?>
                    <section class="feature">
                        <div class="container">
                            <div class="feature__body">
                                <?php
                                    while( have_rows( 'features', 'option' ) ) {
                                        the_row();
                                        $this->renderSingleFeatures();
                                    }
                                ?>
                            </div>
                        </div>
                    </section>
                <?php
                }
            }
        }
        // features group

        // extra detail section start
        if( have_rows( 'extra_detail_section' ) ) {
            while( have_rows( 'extra_detail_section' ) ) {
                the_row();
                $this->renderExtraDetailSection();
            }
        }
        // extra detail section end

        // section with icon start
        if( have_rows( 'section_with_icons' ) ) {
            while( have_rows( 'section_with_icons' ) ) {
                the_row();
                $this->renderSectionWithIcon();
            }
        }
        // section with icon end

        // section with buttons start
        if( have_rows( 'section_with_buttons' ) ) {
            while( have_rows( 'section_with_buttons' ) ) {
                the_row();
                $this->renderSectionWithButtons();
            }
        }
        // section with buttons end

        // section with author image start
        if( have_rows( 'section_with_author_image' ) ) {
            while( have_rows( 'section_with_author_image' ) ) {
                the_row();
                $this->renderSectionWithAuthorImage();
            }
        }
        // section with author image end

        // red section start

        if( get_field( 'red_section_heading' ) && get_field( 'red_section_icon' ) ) {
            ?>
                <section class="section section-gift">
                    <div class="container">
                        <div class="gift">
                            <img src="<?php echo get_field( 'red_section_icon' ) ? get_field( 'red_section_icon' ) : '' ?>" alt="" class="svg" width="61" height="61">
                            <h3 class="gift-title">
                                <?php echo get_field( 'red_section_heading' ) ? get_field( 'red_section_heading' ) : '' ?>
                            </h3>
                        </div>
                    </div>
                </section>
            <?php
        }

        // red section ends

        // last additional section start
        if( have_rows( 'last_additional_section' ) ) {
            while( have_rows( 'last_additional_section' ) ) {
                the_row();
                $this->renderLastAdditionalSection();
            }
        }
        // last additional section end

        ?>
            <section class="section">
                <div class="container">
                    <?php
                        if( have_rows( 'sets_single' ) ) {

                            while( have_rows( 'sets_single' ) ){

                                the_row();

                                $feature_title = get_sub_field( 'sets_title' );
                                $feature_title = explode( ' ', $feature_title );
                                $fist_word = array_shift( $feature_title );
                                $second_word = array_shift( $feature_title );

                                ?>
                                    <h2 class="heading heading_top text-center">
                                        <span class="fw-normal"><?php echo $fist_word . ' ' . $second_word ?></span> <br>
                                        <?php echo implode( ' ', $feature_title ) ?>
                                    </h2>
                                    <p class="heading-paragraph mt-4 pt-lg-3 text-center">
                                        <?php echo get_sub_field( 'sets_subtitle' ); ?> <br class="d-none d-md-block">
                                        <?php echo get_sub_field( 'sets_subtitle_2' ); ?>
                                    </p>
                                    <?php
                                        if( have_rows( 'choose_sets' ) ) {
                                            ?>
                                                <div class="swiper js-swiper" data-swiper-slides="3">
                                                    <div class="swiper-wrapper">
                                                        <?php
                                                            while( have_rows( 'choose_sets' ) ){

                                                                the_row();

                                                                $product_id = get_sub_field('set')->ID;
                                                                $product = wc_get_product( $product_id );

                                                                ?>
                                                                    <div class="swiper-slide">
                                                                        <a href="<?php echo get_permalink( $product_id ) ?>" class="card">
                                                                            <div class="card__thumb">
                                                                                <img src="<?php echo get_the_post_thumbnail_url($product_id) ?>" class="img-fluid" 
                                                                                width="1227" height="1025" alt="">
                                                                                <?php
                                                                                    if( $product->is_on_sale() ) {
                                                                                        ?>
                                                                                            <div class="label">Promocja!</div>
                                                                                        <?php
                                                                                    }
                                                                                ?>

                                                                            </div>
                                                                            <div class="card__body">
                                                                                <div class="card__title">
                                                                                    <?php echo get_sub_field('set')->post_title ?>
                                                                                </div>
                                                                                <div class="amount">
                                                                                    <?php 
                                                                                    if( $product->get_sale_price() ) {
                                                                                        ?>
                                                                                        <div class="amount__current">
                                                                                            <?php echo $product->get_sale_price() . ' zł'; ?> 
                                                                                        </div>
                                                                                        <div class="amount__old">
                                                                                            <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                                                        </div>
                                                                                        <?php
                                                                                    } else {
                                                                                        ?>
                                                                                        <div class="amount__current">
                                                                                            <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                                                        </div>
                                                                                        <?php
                                                                                    } 
                                                                                    ?>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                <?php
                                                            }
                                                        ?>
                                                    </div>
                                                    <div class="swiper-pagination"></div>
                                                </div>
                                            <?php
                                        }
                                    ?>
                                <?php
                            }
                        }// choose book ends
                    ?>
                </div>
            </section>
        <?php
        if( have_rows( 'others_singlew_product' ) ) {

            while( have_rows( 'others_singlew_product' ) ) {

                the_row();
                $section_title = get_sub_field( 'others_title' );

                ?>
                    <section id="inne" class="section">
                        <div class="container">
                            <h2 class="heading heading_top"><?php echo $section_title ?></h2>
                            <?php
                            // others section
                                if( have_rows( 'choose_others' ) ) {
                                    ?>
                                        <div class="swiper js-swiper" data-swiper-slides="3">
                                            <div class="swiper-wrapper">
                                                <?php
                                                    while( have_rows( 'choose_others' ) ){

                                                        the_row();

                                                        $product_id = get_sub_field('other')->ID;
                                                        $product = wc_get_product( $product_id );

                                                        ?>
                                                            <div class="swiper-slide">
                                                                <a href="<?php echo get_permalink( $product_id ) ?>" class="card">
                                                                    <div class="card__thumb">
                                                                        <img src="<?php echo get_the_post_thumbnail_url($product_id) ?>" class="img-fluid" 
                                                                                width="625" height="821" alt="">
                                                                        <?php
                                                                            if( $product->is_on_sale() ) {
                                                                                ?>
                                                                                    <div class="label">Promocja!</div>
                                                                                <?php
                                                                            }
                                                                        ?>

                                                                    </div>
                                                                    <div class="card__body">
                                                                        <div class="card__title">
                                                                            <?php echo get_sub_field('other')->post_title ?>
                                                                        </div>
                                                                        <div class="amount">
                                                                            <?php 
                                                                            if( $product->get_sale_price() ) {
                                                                                ?>
                                                                                <div class="amount__current">
                                                                                    <?php echo $product->get_sale_price() . ' zł'; ?> 
                                                                                </div>
                                                                                <div class="amount__old">
                                                                                    <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                                                </div>
                                                                                <?php
                                                                            } else {
                                                                                ?>
                                                                                <div class="amount__current">
                                                                                    <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                                                </div>
                                                                                <?php
                                                                            } 
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                            <div class="swiper-pagination"></div>
                                        </div>
                                    <?php
                                }// choose book ends
                                if( have_rows( 'choose_games') ) {
                                    ?>
                                        <div class="swiper js-swiper" data-swiper-slides-mobile="1">
                                            <div class="swiper-wrapper">
                                                <?php
                                                    while( have_rows( 'choose_games') ){
    
                                                        the_row();
    
                                                        $product_id = get_sub_field('game')->ID;
                                                        $product = wc_get_product( $product_id );
                                                        ?>
                                                            <div class="swiper-slide">
                                                                <div class="promo">
                                                                    <div class="promo__body">
                                                                        <a href="<?php echo get_permalink( $product_id ) ?>" class="promo__title">
                                                                            <?php echo get_sub_field('game_title') ?>
                                                                        </a>
                                                                        <div class="promo__desc">
                                                                            <?php echo get_sub_field('game_description') ?>
                                                                        </div>
                                                                        <div class="amount">
                                                                        <?php 
                                                                            if( $product->get_sale_price() ) {
                                                                                ?>
                                                                                <div class="amount__current">
                                                                                    <?php echo $product->get_sale_price() . ' zł'; ?> 
                                                                                </div>
                                                                                <div class="amount__old">
                                                                                    <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                                                </div>
                                                                                <?php
                                                                            } else {
                                                                                ?>
                                                                                <div class="amount__current">
                                                                                    <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                                                </div>
                                                                                <?php
                                                                            } 
                                                                        ?>
                                                                    </div>
                                                                        <a href="#" class="wow-button">
                                                                            <?php echo __( 'Kup teraz', 'astra-child' ); ?>
                                                                        </a>
                                                                    </div>
                                                                    <div class="promo__thumb">
                                                                        <img src="<?php echo get_the_post_thumbnail_url( $product_id, 'full' ) ?>" class="img-fluid" 
                                                                            width="585" height="496" alt="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                            <div class="swiper-pagination"></div>
                                        </div>
                                    <?php
                                }// choose games ends
                            ?>
                        </div>
                    </section>
                <?php
            }
        }

        if( have_rows( 'homepage_group', 'option' ) ) {
        
            while( have_rows( 'homepage_group', 'option' ) ) {
        
                the_row();

                if( have_rows( 'faq', 'option' ) ) {

                    while( have_rows( 'faq', 'option' ) ) {
        
                        the_row();
        
                        $section_title = get_sub_field( 'sets_title' );
                        $rows_to_second_col = ceil( count( get_sub_field( 'add_faq', 'option' ) ) / 2 ) ;
                        if( have_rows( 'add_faq', 'option' ) ) {
                            ?>
                                <section id="faq" class="section">
                                    <div class="container">
                                        <h3 class="heading text-center">
                                            Najczęściej zadawane pytania
                                        </h3>
                                        <div class="faq toggle-wrapper js-toggle-wrapper">
                                            <div class="faq__col">
                                                <?php
                                                    
                                                    while( have_rows( 'add_faq', 'option' ) ){
                                                        
                                                        the_row();
        
                                                        if( get_row_index() == $rows_to_second_col + 1 ) {
                                                            ?>
                                                                </div>
                                                                <div class="faq__col">
                                                            <?php
                                                        }
                                                        ?>
                                                            <div class="toggle">
                                                                <div class="toggle__button js-toggle-button">
                                                                    <?php echo get_sub_field('question') ?>
                                                                    <svg class="svg" width="14" height="8" viewBox="0 0 13.625 8.298">
                                                                        <path d="M6.281,8.048L0.219,1.985C0.073,1.84,0,1.663,0,1.454c0-0.208,0.073-0.385,0.219-0.531l0.688-0.688
                                                                            C1.052,0.09,1.229,0.012,1.438,0.001c0.208-0.01,0.385,0.058,0.531,0.203l4.844,4.844l4.844-4.844
                                                                            c0.146-0.146,0.323-0.213,0.531-0.203c0.208,0.011,0.386,0.089,0.531,0.234l0.688,0.688c0.146,0.146,0.219,0.323,0.219,0.531
                                                                            c0,0.208-0.073,0.386-0.219,0.531L7.344,8.048c-0.146,0.167-0.323,0.25-0.531,0.25S6.427,8.215,6.281,8.048z"/>
                                                                    </svg>
                                                                </div>
                                                                <div class="toggle__content">
                                                                    <?php echo get_sub_field('answer') ?>
                                                                </div>
                                                            </div>
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            <?php
                        }
                    }
                }

            }
        }

        ?>
            <section id="kontakt" class="section">
                <div class="container">
                    <div class="contact">
                        <div class="avatar">
                            <img src="<?php echo ASSETS . '/images/kobus.jpg' ?>" class="avatar-img" width="256" height="256" alt="">
                        </div>
                        <h3 class="heading">Masz inne pytania?</h3>
                        <p class="paragraph">
                            Zapraszamy do bezpośredniego kontaktu z nami
                        </p>
                        <a href="mailto:sklep@pankobus.pl" class="contact__mail">
                            sklep@pankobus.pl
                        </a>
                    </div>
                    
                </div>
            </section>
        <?php

        ob_get_contents();

    }

    public function renderExtraDetailSection() {
        ?>
            <section class="section">
                <div class="container">
                    <div class="info-1">
                        <div class="info-1-thumb">
                            <img src="<?php echo get_sub_field( 'image' ) ? get_sub_field( 'image' ) : '' ?>" class="img-fluid" 
                                width="217" height="640" alt="">
                        </div>
                        <div class="info-1-body">
                            <h3 class="info-1-title heading">
                                <?php echo get_sub_field( 'title_bold' ) ? get_sub_field( 'title_bold' ) : '' ?>
                                <span class="fw-normal">
                                    <?php echo get_sub_field( 'title_normal' ) ? get_sub_field( 'title_normal' ) : '' ?>
                                </span>
                            </h3>
                            <div class="info-1-desc paragraph fw-600">
                                <?php echo get_sub_field( 'content' ) ? get_sub_field( 'content' ) : '' ?>
                            </div>
                            <div class="info-1-feature-row">
                                <?php
                                    if( have_rows( 'counters' ) ) {
                                        while( have_rows( 'counters' ) ) {
                                            the_row();
                                            ?>
                                                <div class="info-1-feature">
                                                    <strong class="info-1-feature-title heading">
                                                        <?php echo get_sub_field( 'counter_heading' ) ? get_sub_field( 'counter_heading' ) : '' ?>
                                                    </strong>
                                                    <div class="info-1-feature-desc paragraph fw-600">
                                                        <?php echo get_sub_field( 'counter_description' ) ? get_sub_field( 'counter_description' ) : '' ?>
                                                    </div>
                                                </div>
                                            <?php
                                        }
                                    }
                                ?>
                            </div>   
                        </div>
                    </div>
                </div>
            </section>
        <?php
    }

    public function renderSectionWithIcon() {
        ?>
            <section class="section">
                <div class="container">
                    <div class="info-2 text-center">
                        <h3 class="info-2-title heading heading_sm">
                            <?php
                                if( have_rows( 'headings' ) ) {
                                    while( have_rows( 'headings' ) ) {
                                        the_row();
                                            ?>
                                                <?php echo get_sub_field( 'bold_heading' ) ? get_sub_field( 'bold_heading' ) : '' ?> <span class="fw-normal"> <?php echo get_sub_field( 'normal_heading' ) ? get_sub_field( 'normal_heading' ) : '' ?></span> <br>
                                        <?php
                                    }
                                }
                            ?>
                        </h3>
                        <p class="paragraph fw-600 mt-4">
                            <?php echo get_sub_field( 'subheading' ) ? get_sub_field( 'subheading' ) : '' ?>
                        </p>
                        <div class="info-2-icon-list">
                            <?php
                                if( have_rows( 'icons' ) ) {
                                    while( have_rows( 'icons' ) ) {
                                        the_row();
                                        ?>
                                            <div class="info-2-icon">
                                                <div class="info-2-icon-thumb">
                                                    <img src="<?php echo get_sub_field( 'icon_image' ) ? get_sub_field( 'icon_image' ) : '' ?>" class="img-fluid" width="77" height="77" alt="">
                                                </div>
                                                <div class="info-2-icon-title">
                                                    <?php echo get_sub_field( 'icon_description' ) ? get_sub_field( 'icon_description' ) : '' ?>
                                                </div>
                                            </div>
                                        <?php
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php
    }

    public function renderSectionWithButtons() {
        ?>
            <section class="section section-info-3" style="background-color: <?php echo get_sub_field( 'background_color' ) ? get_sub_field( 'background_color' ) : '#f8f8f8' ?>;">
                <div class="info-3-bg">
                    <img src="<?php echo get_sub_field( 'desktop_image' ) ? get_sub_field( 'desktop_image' ) : '' ?>" class="img-fit info-3-bg__img-lg" alt="">
                    <img src="<?php echo get_sub_field( 'mobile_image' ) ? get_sub_field( 'mobile_image' ) : '' ?>" class="info-3-bg__img" alt="">
                </div>
                <div class="container">
                    <div class="info-3">
                        <div class="info-3-col">
                            <h3 class="heading heading_sm">
                                <?php echo get_sub_field( 'heading' ) ? get_sub_field( 'heading' ) : '' ?>
                            </h3>
                            <p class="paragraph fw-600 mt-4">
                                <?php echo get_sub_field( 'description_1' ) ? get_sub_field( 'description_1' ) : '' ?>
                            </p>
                            <div class="mt-4 pt-3">
                                <a href="<?php echo get_site_url() . '/?add-to-cart=' . get_the_ID() ?>" class="wow-button wow-button_primary">
                                    <?php echo __( 'Zamów teraz', 'astra-child'); ?>
                                </a>
                            </div>
                            <div class="d-flex flex-column align-items-center align-items-lg-start gap-3 mt-4">
                                <a href="<?php echo get_sub_field( 'button_1' )['url'] ? get_sub_field( 'button_1' )['url'] : '' ?>" class="wow-button">
                                    <?php echo get_sub_field( 'button_1' )['title'] ? get_sub_field( 'button_1' )['title'] : '' ?>
                                </a>
                                <a href="<?php echo get_sub_field( 'button_2' )['url'] ? get_sub_field( 'button_2' )['url'] : '' ?>" class="wow-button">
                                    <?php echo get_sub_field( 'button_2' )['title'] ? get_sub_field( 'button_2' )['title'] : '' ?>
                                </a>
                            </div>
                        </div>
                        <div class="info-3-col">
                            <p class="paragraph fw-600 mt-4">
                                <?php echo get_sub_field( 'description_2' ) ? get_sub_field( 'description_2' ) : '' ?>
                            </p>
                            <h3 class="heading heading_sm mt-4 pt-2">
                                <?php echo get_sub_field( 'heading_2' ) ? get_sub_field( 'heading_2' ) : '' ?>
                            </h3>
                            <p class="paragraph fw-600 mt-4">
                                <?php echo get_sub_field( 'description_3' ) ? get_sub_field( 'description_3' ) : '' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        <?php
    }

    public function renderSectionWithAuthorImage() {
        ?>
            <section class="section section-author">
                <div class="container">
                    <div class="author">
                        <div class="author-col">
                            <h3 class="heading heading_sm">
                                <?php echo get_sub_field( 'heading' ) ? get_sub_field( 'heading' ) : '' ?> 
                            </h3>
                            <div class="text-center text-lg-end pe-lg-4 pt-2">
                                <img src="<?php echo get_sub_field( 'autograph_image' ) ? get_sub_field( 'autograph_image' ) : '' ?> " class="img-fluid me-lg-4" 
                                    width="122" height="53" alt="">
                            </div>
                            <div class="d-flex align-items-center flex-wrap align-items-lg-start justify-content-center justify-content-lg-start flex-lg-column gap-4 mt-5">
                            <?php
                                if( have_rows( 'counters' ) ) {
                                    while( have_rows( 'counters' ) ) {
                                        the_row();
                                        ?>
                                            <div class="info-1-feature">
                                                <strong class="info-1-feature-title heading">
                                                    <?php echo get_sub_field( 'counter_heading' ) ? get_sub_field( 'counter_heading' ) : '' ?>
                                                </strong>
                                                <div class="info-1-feature-desc paragraph fw-600">
                                                    <?php echo get_sub_field( 'counter_description' ) ? get_sub_field( 'counter_description' ) : '' ?>
                                                </div>
                                            </div>
                                        <?php
                                    }
                                }
                            ?>
                            </div>
                        </div>
                        <div class="author-col">
                            <p class="paragraph fw-600 mt-4 mb-0">
                                <?php echo get_sub_field( 'description' ) ? get_sub_field( 'description' ) : '' ?>
                            </p>
                        </div>
                        <img src="<?php echo get_sub_field( 'main_image' ) ? get_sub_field( 'main_image' ) : '' ?>" class="author-img img-fluid mt-5 mt-lg-0 px-5 px-lg-0" 
                            width="444" height="660" alt="">
                    </div>
                </div>
            </section>
        <?php
    }

    public function renderLastAdditionalSection() {
        $product_id = get_sub_field('product');
        $product = wc_get_product( $product_id );
        ?>
            <section class="section section-package">
                <div class="container">
                    <div class="package">
                        <div class="package-thumb">
                            <img src="<?php echo get_sub_field( 'image' ) ? get_sub_field( 'image' ) : '' ?>" class="img-fluid" 
                                width="490" height="620" alt="">
                        </div>
                        <div class="package-body mt-3 mt-lg-0">
                            <p class="paragraph fw-600 mt-4">
                                <?php echo get_sub_field( 'subheading' ) ? get_sub_field( 'subheading' ) : '' ?>
                            </p>
                            <h3 class="heading mt-4 py-2">
                                <?php echo get_sub_field( 'heading' ) ? get_sub_field( 'heading' ) : '' ?>
                            </h3>
                            <p class="paragraph fw-600 mt-4">
                                <?php echo get_sub_field( 'description' ) ? get_sub_field( 'description' ) : '' ?>
                            </p>
                            <a href="<?php echo get_permalink( $product_id ) ?>" class="wow-button wow-button_primary mt-3 mt-lg-4">
                                <?php echo __( 'Kup w pakiecie', 'astra-child' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        <?php
    }

    public function moveBreadcrumbs(){
        if ( is_product() ) {
            
            add_action( 'woocommerce_before_single_product_summary', 'woocommerce_breadcrumb', 4 );
        }
    }

    public function renderProdutDetails() {

        $product = wc_get_product( get_the_ID() );

        //ob_start();
        ?>
            <div class="product__body">
                <?php if( $product->get_sale_price() ) { 
                    ?>
                        <div class="amount">
                            <div class="amount__label">
                                Promocja!
                            </div>
                            <div class="amount__current">
                                <?php echo $product->get_sale_price() . ' zł'; ?> 
                            </div>
                            <div class="amount__old">
                                <?php echo $product->get_regular_price() . ' zł'; ?> 
                            </div>
                        </div>
                    <?php
                } else {
                    ?>
                        <div class="amount">
                            <div class="amount__current">
                                <?php echo $product->get_regular_price() . ' zł'; ?> 
                            </div>
                        </div>
                    <?php
                } ?>
                <h1 class="product__title">
                    <?php echo get_the_title(); ?>
                </h1>
                <div class="product__desc">
                    <p>
                        <?php echo strlen( get_the_content() ) > 300 ? substr( get_the_content() ,0,300)."..." : get_the_content(); ?>
                    </p>
                    <a href="#opinions" class="link"><?php echo __( 'Dowiedz się więcej...', 'astra-child' ); ?></a>
                </div>
                <div class="product__subtitle">
                    <?php 
                        if( have_rows( 'custom_product_attributes' ) ){
                            while( have_rows( 'custom_product_attributes' ) ){
                                the_row();
                                ?>
                                    <p>
                                        <?php echo get_sub_field( 'attribute' ) ? get_sub_field( 'attribute' ) : ''; ?>
                                    </p>
                                <?php
                            }
                        }
                    ?>
                </div>
                <div class="product__action">
                    <div class="wow-quantity">
                        <button type="wow-button" class="quantity__btn wow-single-qty-btn wow-single-qty-btn-minus" data-type="minus">
                            -
                        </button>
                        <input type="number" step="1" min="1" value="<?php echo isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity() ?>" name="quantity" inputmode="numeric" class="wow-quantity__field wow-single-product-form-input">
                        <button type="wow-button" class="quantity__btn wow-single-qty-btn wow-single-qty-btn-plus" data-type="plus">
                            +
                        </button>
                    </div>
                    <button type="submit" class="wow-button wow-button_primary button_lg wow-single-product-add-btn">
                    <svg class="svg svg-cart-2" width="20" height="22" viewBox="0 0 159 176">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M159,38c0,39,0,78,0,117c-0.318,0.305-0.907,0.603-0.916,0.916  c-0.289,10.328-12.154,20.301-21.965,19.134c-37.746,0.001-75.493,0.001-113.24,0.001c-2.057-0.437-4.101-0.109-6.273-0.702
    C7.866,171.967,3.426,165.581,0,158c0-40.333,0-80.667,0-121c5.125-6.938,9.894-14.18,15.483-20.719C20.338,10.602,23.06,2.759,31,0
    c32.333,0,64.666,0,97,0c3.086,1.172,5.439,3.425,7.322,5.918C143.328,16.526,151.126,27.292,159,38z M79.723,47.968
    c-20.323,0-40.646,0.073-60.966-0.078c-3.096-0.022-3.842,0.759-3.831,3.843c0.122,33.148,0.065,66.297,0.085,99.445
    c0.004,6.147,2.779,8.833,9.005,8.834c36.646,0.007,73.293,0.006,109.939,0.001c6.372-0.001,10.031-3.563,10.035-9.876
    c0.019-32.648-0.055-65.298,0.099-97.946c0.017-3.573-0.966-4.337-4.399-4.308C119.701,48.056,99.711,47.968,79.723,47.968z
    M22.879,32.044c2.21-0.704,4.239,0.006,6.159,0.002c25.138-0.047,50.276-0.063,75.414-0.015c9.784,0.018,19.569,0.184,29.351,0.398
    c0.687,0.016,1.188,0.201,1.637-0.398c0.812-1.084-0.414-1.332-0.811-1.865c-2.516-3.382-5.285-6.595-7.561-10.127
    c-1.875-2.91-4.018-4.168-7.598-4.149c-26.635,0.143-53.272,0.177-79.907-0.041c-3.526-0.029-5.746,0.985-7.641,3.682
    C28.966,23.741,25.9,27.876,22.879,32.044z M136,176c-0.029-0.325,0.011-0.642,0.119-0.95c-37.746,0.001-75.493,0.001-113.24,0.001
    c0.107,0.308,0.148,0.624,0.121,0.949C60.667,176,98.334,176,136,176z M47.083,94.945c7.787,11.214,19.215,16.52,32.44,16.674
    c8.853,0.103,17.403-2.868,24.576-8.646c9.884-7.962,14.278-18.642,14.806-30.99c0.173-4.05-4.08-7.805-7.615-7.693
    c-4.471,0.14-7.215,3.008-7.221,8.013c-0.002,1.881-0.629,3.49-1.063,5.208c-2.708,10.713-11.842,20.399-27.604,18.468
    c-9.794-1.201-20.536-11.016-20.433-22.921c0.031-3.584-1.254-6.85-4.947-7.968c-6.1-1.847-11.044,1.552-10.158,8.13
    C40.079,81.917,42.819,88.806,47.083,94.945z"/>
                        </svg>
                        Dodaj do koszyka
                    </button>
                    <div class="wow-woocommerce-quantity-form">
                        <?php do_action( 'wow_add_quantity_form' ); ?>
                    </div>
                </div>
                <div class="product__extra">
                    <div class="product__extra-heading">
                        Uszlachetnij swój egzemplarz
                    </div>
                    <div class="extra-order">
                        <?php
                            if( have_rows('refine_products_single' ) ):

                                while( have_rows('refine_products_single' ) ) : the_row();

                                    $this->renderrefineSingleProduct();

                                endwhile;

                            elseif( have_rows('refine_products', 'option') ):

                                while( have_rows('refine_products', 'option') ) : the_row();

                                    $this->renderrefineSingleProduct();

                                endwhile;

                            endif;
                        ?>
                    </div>
                </div>
            </div>
        <?php

        //ob_get_contents();
    }

    public function renderrefineSingleProduct() {

        // Load sub field value.
        $product_id = get_sub_field('product');
        $product = wc_get_product( $product_id );
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );

        ?>
            <div class="extra-order__item">
                <div class="extra">
                    <div class="extra__body">
                        <div class="extra__thumb">
                            <img src="<?php  echo $image[0]; ?>" 
                        width="58" height="58" alt="">
                        </div>
                        <div class="extra__title">
                            <?php echo $product->get_name(); ?>
                        </div>
                        <a href="#" class="extra__link" data-bs-toggle="modal" data-bs-target="#<?php echo $product->get_slug() ?>">Jak wygląda?</a>
                    </div>
                    <div class="extra__foot">
                        <div class="extra__price">
                            <?php echo $product->get_price() . ' zł'; ?> 
                        </div>
                        <a href="<?php echo get_site_url() . '/?add-to-cart=' . $product_id ?>" class="wow-button">Dodaj</a>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="<?php echo $product->get_slug() ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo $product->get_name(); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&#10006;</button>
                        </div>
                        <div class="modal-body">
                            <img src="<?php echo get_sub_field('additional_image') ? get_sub_field('additional_image') :  ASSETS . '/images/photo-gift.jpg' ?>" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    public function renderSingleFeatures() {

        ?>
            <div class="icon-text">
                <div class="icon-text__thumb">
                    <img  width="59" height="59" src="<?php echo get_sub_field( 'feature_image' ) ?>" alt="clock">
                </div>
                <div class="icon-text__body">
                    <?php
                        
                        $feature_title = get_sub_field( 'feature_title' );
                        $feature_title = explode( ' ', $feature_title );
                        $fist_word = array_shift( $feature_title );
                        
                    ?>
                    <?php echo $fist_word ?> <br>
                    <strong><?php echo implode( ' ', $feature_title ); ?></strong>
                </div>
            </div>
        <?php
    }

    public function renderSingleOthers() {

        $section_title = get_sub_field( 'others_title' );

        ?>
             <section id="inne" class="section">
                <div class="container">
                    <h2 class="heading heading_top"><?php echo $section_title ?></h2>
                    <?php
                    // others section
                    if( have_rows( 'choose_others', 'option' ) ) {
                        ?>
                            <div class="swiper js-swiper" data-swiper-slides="3">
                                <div class="swiper-wrapper">
                                    <?php
                                        while( have_rows( 'choose_others', 'option' ) ){

                                            the_row();

                                            $product_id = get_sub_field('other')->ID;
                                            $product = wc_get_product( $product_id );

                                            ?>
                                                <div class="swiper-slide">
                                                    <a href="<?php echo get_permalink( $product_id ) ?>" class="card">
                                                        <div class="card__thumb">
                                                            <img src="<?php echo get_the_post_thumbnail_url($product_id) ?>" class="img-fluid" 
                                                                    width="625" height="821" alt="">
                                                            <?php
                                                                if( $product->is_on_sale() ) {
                                                                    ?>
                                                                        <div class="label">Promocja!</div>
                                                                    <?php
                                                                }
                                                            ?>

                                                        </div>
                                                        <div class="card__body">
                                                            <div class="card__title">
                                                                <?php echo get_sub_field('other')->post_title ?>
                                                            </div>
                                                            <div class="amount">
                                                                <?php 
                                                                if( $product->get_sale_price() ) {
                                                                    ?>
                                                                    <div class="amount__current">
                                                                        <?php echo $product->get_sale_price() . ' zł'; ?> 
                                                                    </div>
                                                                    <div class="amount__old">
                                                                        <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                                    </div>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <div class="amount__current">
                                                                        <?php echo $product->get_regular_price() . ' zł'; ?> 
                                                                    </div>
                                                                    <?php
                                                                } 
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php
                                        }
                                    ?>
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        <?php
                    }// choose book ends
                    ?>
                </div>
            </section>
        <?php
    }

    public function woocommerce_filter_breadcrumb( $args ) {

        $args['delimiter'] = '&nbsp;-&nbsp;';
        return $args;
        
    }

}