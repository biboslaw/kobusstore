<?php
/**
 * Template name: Homepage
 */

 get_header();



// main group
if( have_rows( 'homepage_group', 'option' ) ) {

    while( have_rows( 'homepage_group', 'option' ) ) {

        the_row();

        // books group
        if( have_rows( 'books_group', 'option' ) ) {

            while( have_rows( 'books_group', 'option' ) ) {

                the_row();

                $section_title = get_sub_field( 'books_title' );

                // choose book section
                if( have_rows( 'choose_book', 'option' ) ) {

                    ?>
                        <section id="ksiazki" class="section">
                            <div class="container">
                                <h2 class="heading heading_top"><?php echo $section_title ?></h2>
                                <div class="swiper js-swiper" data-swiper-slides="3">
                                    <div class="swiper-wrapper">
                                        <?php
                                            while( have_rows( 'choose_book', 'option' ) ){

                                                the_row();

                                                $product_id = get_sub_field('book')->ID;
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
                                                                    <?php echo get_sub_field('book')->post_title ?>
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
                            </div>
                        </section>
                    <?php
                }// choose book ends
            }
        } //books group end

        // features group
        if( have_rows( 'features', 'option' ) ) {
            ?>
                <section class="feature">
                    <div class="container">
                        <div class="feature__body">
                            <?php
                                while( have_rows( 'features', 'option' ) ) {

                                    the_row();
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
                            ?>
                        </div>
                    </div>
                </section>
            <?php
        }// features group

        // e-book group
        if( have_rows( 'e-book', 'option' ) ) {

            while( have_rows( 'e-book', 'option' ) ) {

                the_row();

                $section_title = get_sub_field( 'e-book_title' );

                // choose e-book section
                if( have_rows( 'choose_e-book', 'option' ) ) {
                    ?>
                    <section id="e-book" class="section">
                        <div class="container">
                            <h3 class="heading heading_top"><?php echo $section_title ?></h3>
                            <div class="swiper js-swiper" data-swiper-slides="4">
                                <div class="swiper-wrapper">
                                    <?php
                                        while( have_rows( 'choose_e-book', 'option' ) ){

                                            the_row();

                                            $product_id = get_sub_field('e-book')->ID;
                                            $product = wc_get_product( $product_id );
                                            ?>
                                                <div class="swiper-slide">
                                                    <a href="<?php echo get_permalink( $product_id ) ?>" class="card">
                                                        <div class="card__thumb">
                                                            <img src="<?php echo get_the_post_thumbnail_url($product_id) ?>" class="img-fluid" 
                                                                width="625" height="821" alt="">
                                                            <div class="label">Promocja!</div>
                                                        </div>
                                                        <div class="card__body">
                                                            <div class="card__title">
                                                                <?php echo get_sub_field('e-book')->post_title ?>
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
                        </div>
                    </section>
                    <?php
                    
                }// choose e-book ends
            }
        } //e-book group end

        // sets group
        if( have_rows( 'sets', 'option' ) ) {

            while( have_rows( 'sets', 'option' ) ) {

                the_row();

                $section_title = get_sub_field( 'sets_title' );

                // choose sets section
                if( have_rows( 'choose_sets', 'option' ) ) {
                    ?>
                        <section id="pakiety-ksiazek" class="section">
                            <div class="container">
                                <h3 class="heading heading_top"><?php echo $section_title ?></h3>
                                <div class="swiper js-swiper" data-swiper-slides-mobile="1">
                                    <div class="swiper-wrapper">
                                        <?php
                                            while( have_rows( 'choose_sets', 'option' ) ){

                                                the_row();

                                                $product_id = get_sub_field('set')->ID;
                                                $product = wc_get_product( $product_id );
                                                ?>
                                                    <div class="swiper-slide">
                                                        <div class="promo">
                                                            <div class="promo__body">
                                                                <a href="<?php echo get_permalink( $product_id ) ?>" class="promo__title">
                                                                    <?php echo get_sub_field('set_title') ?>
                                                                </a>
                                                                <div class="promo__desc">
                                                                    <?php echo get_sub_field('set_description') ?>
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
                            </div>
                        </section>
                    <?php
                }// choose sets ends
            }
        } //sets group end

        // others
        if( have_rows( 'others', 'option' ) ) {

            while( have_rows( 'others', 'option' ) ) {

                the_row();

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
                            if( have_rows( 'choose_games', 'option' ) ) {
                                ?>
                                    <div class="swiper js-swiper" data-swiper-slides-mobile="1">
                                        <div class="swiper-wrapper">
                                            <?php
                                                while( have_rows( 'choose_games', 'option' ) ){

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
        } //others group end
        // sets group
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
} // main group end

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

get_footer();