<?php

namespace WowCode\Controllers;

use PostSMTP\Vendor\phpseclib3\Exception\UnableToConnectException;

if ( ! defined( 'ABSPATH' ) ) exit;

class HeaderController {

    public function __construct() {
        add_action( 'after_setup_theme', [ $this, 'registerCustomMenu' ], 0 );
        add_filter( 'nav_menu_link_attributes', [ $this, 'add_menu_link_class' ], 1, 3 );
        add_action( 'wow-render-custom-header', [ $this, 'renderHeader' ] );
    }

    public function renderHeader() {
        ob_start();
        ?>
            <nav class="navbar">
                <div class="container">
                    <a class="logo" href="/">
                        <img src="<?php echo get_field( 'header_logo','option' ) ?>" class="img-fluid" 
                            width="169" height="82" alt="">
                    </a>
                    <div class="navbar__body">
                        <div class="nav-overlay">
                            <?php 
                                wp_nav_menu( 
                                    array( 
                                        'theme_location' => 'custom_menu',
                                        'menu_class' => 'nav nav_main',
                                        'link_class' => 'wow-link'
                                    ) 
                                ); 
                            ?>
                            <div class="d-block d-lg-none w-100">
                                <div class="newsletter">
                                    <form action="#">
                                        <div class="newsletter-title">
                                            Dołącz do newslettera,
                                        </div>
                                        <div class="newsletter-desc mt-2">
                                            aby otrzymać w przyszłości <br>
                                            nowości, zniżki i ciekawe <br>
                                            Kobusowe artykuły.
                                        </div>
                                        <div class="d-grid mt-3">
                                            <input type="text" class="form-control" placeholder="E-mail*">
                                            <button class="button rounded-top-0" type="submit">Zapisz</button>
                                        </div>
                                        
                                    </form>
                                </div>
                                <ul class="social">
                                    <li class="social__item">
                                        <a href="https://www.facebook.com/pankobuspl" class="social__link"
                                        target="_blank" rel="nofollow noopener">
                                            <svg class="svg" width="18" height="18" viewBox="0 0 24 24">
                                                <path d="M24 12.07a12 12 0 10-13.88 11.85v-8.38H7.08v-3.47h3.05V9.43c0-3.01 1.79-4.67 4.53-4.67 1.31 0 2.68.23 2.68.23v2.95h-1.51c-1.49 0-1.96.93-1.96 1.88v2.25h3.33l-.53 3.47h-2.8v8.38A12 12 0 0024 12.07z"/>
                                            </svg>
                                        </a>
                                    </li>
                                    <li class="social__item">
                                        <a href="https://www.instagram.com/pankobus/" 
                                            class="social__link" target="_blank" rel="nofollow noopener">
                                            <svg class="svg" width="18" height="18" viewBox="0 0 25 25">
                                                <path d="M20.669 5.829a1.503 1.503 0 10-3.003 0 1.502 1.502 0 003.003 0"/><path d="M22.671 17.549c-.059 1.221-.259 1.88-.43 2.319a3.83 3.83 0 01-.938 1.44 3.864 3.864 0 01-1.436.933c-.439.171-1.104.376-2.324.435-1.317.059-1.703.068-5.048.068-3.337 0-3.73-.015-5.048-.068-1.218-.059-1.88-.264-2.322-.435a3.904 3.904 0 01-1.438-.933 3.835 3.835 0 01-.935-1.44c-.168-.439-.376-1.099-.43-2.319-.063-1.317-.076-1.713-.076-5.048 0-3.342.012-3.735.076-5.053.054-1.218.261-1.88.43-2.324.225-.581.496-.996.935-1.433s.852-.708 1.438-.935c.443-.173 1.104-.376 2.323-.432 1.318-.059 1.711-.074 5.048-.074 3.345 0 3.73.015 5.048.073 1.221.059 1.885.259 2.324.432a3.876 3.876 0 011.436.935c.439.437.713.852.938 1.433.171.444.376 1.106.43 2.324.059 1.318.078 1.711.078 5.053-.001 3.336-.016 3.731-.079 5.049zm2.251-10.204c-.063-1.331-.273-2.241-.586-3.032a5.964 5.964 0 00-1.44-2.217 6.051 6.051 0 00-2.212-1.44c-.791-.31-1.699-.522-3.032-.581C16.319.012 15.895 0 12.496 0c-3.391 0-3.82.012-5.153.075-1.328.059-2.237.271-3.034.581-.82.32-1.516.747-2.209 1.44A6.117 6.117 0 00.656 4.311c-.31.793-.52 1.704-.583 3.034C.015 8.678 0 9.105 0 12.501c0 3.394.015 3.818.073 5.15.063 1.328.273 2.236.583 3.032.32.82.745 1.519 1.443 2.212a6.048 6.048 0 002.209 1.445c.798.308 1.704.518 3.034.581 1.334.059 1.763.079 5.154.079 3.398 0 3.823-.02 5.155-.078 1.333-.063 2.236-.269 3.032-.581a5.985 5.985 0 002.212-1.445 5.98 5.98 0 001.44-2.212c.313-.796.522-1.704.586-3.032.059-1.333.079-1.757.079-5.151 0-3.396-.02-3.823-.078-5.156z"/><path d="M12.496 16.666a4.165 4.165 0 01-4.165-4.165 4.166 4.166 0 014.165-4.169 4.17 4.17 0 014.17 4.169c0 2.3-1.868 4.165-4.17 4.165zm0-10.588a6.42 6.42 0 000 12.838 6.42 6.42 0 100-12.838z"/>
                                            </svg>
                                        </a>
                                    </li>
                                    <li class="social__item">
                                        <a href="https://www.youtube.com/channel/UCjbjyNaUl7c47H6Y0rdqNzQ" 
                                            class="social__link" target="_blank" rel="nofollow noopener">
                                            <svg class="svg" width="20" height="20" viewBox="0 0 121.485 85.04" enable-background="new 0 0 121.485 85.04">
                                                <path d="M118.946,13.279c-1.397-5.227-5.514-9.343-10.741-10.74C98.731,0,60.742,0,60.742,0S22.753,0,13.279,2.539
                                                    C8.052,3.936,3.935,8.052,2.538,13.279C0,22.753,0,42.52,0,42.52s0,19.767,2.538,29.24c1.397,5.228,5.514,9.345,10.741,10.741
                                                    c9.474,2.539,47.463,2.539,47.463,2.539s37.989,0,47.463-2.539c5.227-1.396,9.344-5.514,10.741-10.741
                                                    c2.539-9.473,2.539-29.24,2.539-29.24S121.485,22.753,118.946,13.279z M48.593,60.743V24.297L80.155,42.52L48.593,60.743z"/>
                                                </svg>
                                            </svg>
                                        </a>
                                    </li>
                                    <li class="social__item">
                                        <a href="https://www.tiktok.com/@pankobus" 
                                            class="social__link" target="_blank" rel="nofollow noopener">
                                            <svg class="svg" width="18" height="18" viewBox="33.319 0 433.354 500.001" enable-background="new 33.319 0 433.354 500.001">
                                                <path d="M412.712,106.932c-3.375-1.745-6.66-3.656-9.844-5.729c-9.258-6.119-17.745-13.332-25.279-21.478
                                                c-18.854-21.572-25.896-43.456-28.489-58.778h0.105C347.038,8.229,347.934,0,348.068,0h-85.87v332.047c0,4.457,0,8.863-0.188,13.217
                                                c0,0.543-0.053,1.043-0.083,1.625c0,0.24,0,0.49-0.053,0.74c0,0.063,0,0.125,0,0.188c-1.838,24.193-15.59,45.888-36.686,57.871
                                                c-10.827,6.16-23.073,9.394-35.53,9.375c-40.008,0-72.433-32.623-72.433-72.912s32.425-72.914,72.434-72.914
                                                c7.573-0.006,15.099,1.185,22.3,3.531l0.104-87.433c-44.194-5.709-88.761,7.329-122.911,35.956
                                                c-14.801,12.859-27.244,28.204-36.769,45.342c-3.625,6.25-17.302,31.363-18.958,72.121c-1.042,23.135,5.906,47.102,9.218,57.008
                                                v0.209c2.083,5.832,10.156,25.738,23.311,42.519c10.608,13.461,23.142,25.285,37.196,35.092v-0.207l0.208,0.207
                                                c41.571,28.25,87.663,26.395,87.663,26.395c7.979-0.321,34.707,0,65.059-14.385c33.665-15.944,52.83-39.705,52.83-39.705
                                                c12.244-14.196,21.98-30.374,28.791-47.842c7.771-20.426,10.363-44.924,10.363-54.717V167.169
                                                c1.042,0.625,14.916,9.802,14.916,9.802s19.988,12.812,51.175,21.155c22.374,5.937,52.518,7.188,52.518,7.188v-85.246
                                                C456.115,121.212,434.669,117.879,412.712,106.932z"/>
                                            </svg>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php do_action( 'wow-after-custom-menu' ); ?>
                    </div>
                </div>
            </nav>
        <?php
        ob_get_contents();
    }

    public function registerCustomMenu() {
        register_nav_menu( 'custom_menu', __( 'Custom Menu', 'astra-child' ) );
    }

    public function add_menu_link_class( $atts, $item, $args ) {
        if (property_exists($args, 'link_class')) {
          $atts['class'] = $args->link_class;
        }
        return $atts;
      }
}