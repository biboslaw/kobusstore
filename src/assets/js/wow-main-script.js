jQuery( function( $ ) {

    $( document ).ready(function() {

        function init_payment_methods() {
			var $payment_methods = $( '.woocommerce-checkout' ).find( 'input[name="payment_method"]' );

            var selectedPaymentMethod = $( '.woocommerce-checkout input[name="payment_method"]:checked' ).attr( 'id' );

            // if ( selectedPaymentMethod !== wc_checkout_form.selectedPaymentMethod ) {
            //     $( document.body ).trigger( 'payment_method_selected' );
            // }

			// If there is one method, we can hide the radio input
			if ( 1 === $payment_methods.length ) {
				$payment_methods.eq(0).hide();
			}

			// If there was a previously selected method, check that one.
			// if ( wc_checkout_form.selectedPaymentMethod ) {
			// 	$( '#' + wc_checkout_form.selectedPaymentMethod ).prop( 'checked', true );
			// }

			// If there are none selected, select the first.
			if ( 0 === $payment_methods.filter( ':checked' ).length ) {
				$payment_methods.eq(0).prop( 'checked', true );
			}

			// Get name of new selected method.
			var checkedPaymentMethod = $payment_methods.filter( ':checked' ).eq(0).prop( 'id' );

			if ( $payment_methods.length > 1 ) {
				// Hide open descriptions.
				$( 'div.payment_box:not(".' + checkedPaymentMethod + '")' ).filter( ':visible' ).slideUp( 0 );
			}

			// Trigger click event for selected method
			$payment_methods.filter( ':checked' ).eq(0).trigger( 'click' );
		}

        const checkoutForm = $('form.checkout');
        const qtyBttns = $('form.checkout .quantity__btn');

        if( checkoutForm && qtyBttns ) {
            qtyBttns.each( function( index ) {
                $(this).on('click', function(e) {

                    e.preventDefault();
                    const currentVal = $(this).closest('.wow-quantity').find('.wow-quantity__field').val();
                    const productId = $(this).closest('.wow-card-row').data( 'product-id' );

                    checkoutForm.find('tr.cart_item[data-product-id="' + productId + '"] .quantity__field').val( currentVal );

                    const data = {
                        action: 'wow_update_order_review',
                        security: wow_ajax.checkout_nonce,
                        data: $( 'form.checkout' ).serialize()
                    }

                    $.post( wow_ajax.url, data, function( response )
                    {
                        $( 'body' ).trigger( 'update_checkout' );   
                    });
                    
                } )
            } )
        }

        $( document.body ).on( 'updated_checkout', function(e){

            const data = {
                action: 'wow_update_totals',
                security: wow_ajax.checkout_nonce,
                data: {
                    checkout_ajax: 'true'
                }
            }

            $.post( wow_ajax.url, data, function( response )
            {
                if( response.shipping ) $('.wow-totals-shippment').html( response.shipping );
                if( response.total ) $('.wow-totals-total ').html( response.total );
                if( response.regular ) $('.wow-totals-regular').html( response.regular + ' zł');
                if( response.discount ) $('.wow-totals-discount').html( response.discount + ' zł');

            });
        } );

        $('.discount__input').on('change', function() {
            $('#ast-coupon-code').val( $(this).val() );
        } )

        $( '.wow-apply-coupon' ).on( 'click', function(e) {

            const data = {
                action: 'wow_update_coupon',
                security: wow_ajax.checkout_nonce,
                data: {
                    checkout_coupon: $('.discount__input').val()
                }
            }

            e.preventDefault();

            $.post( wow_ajax.url, data, function( response )
            {
                $( 'body' ).trigger( 'update_checkout' ); 
                $('.wow-coupon-input-info').text(response.message);
            });

        } )

        $( document.body ).on( 'applied_coupon_in_checkout removed_coupon_in_checkout', function(e) {

            $('.wow-coupon-input-info').text($( '.woocommerce-message,.woocommerce-error' ).text());

        } );

        $( '.wow-place-order' ).on( 'click', function() {
            $('#place_order').trigger('click');
        } )

        $( '.wow-payment-check-class' ).on( 'click', function(e) {
            const current_vlaue = $(this).val();

            $( 'form.checkout input[value="' + current_vlaue + '"]' ).prop('checked', 'checked');

            const data = {
                action: 'set_payment_method',
                security: wow_ajax.checkout_nonce,
                data: {
                    payment_method: current_vlaue
                }
            }

            $.post( wow_ajax.url, data, function( response )
            {

                var paymentDetails = {};
                $( '.payment_box :input' ).each( function() {
                    var ID = $( this ).attr( 'id' );

                    if ( ID ) {
                        if ( $.inArray( $( this ).attr( 'type' ), [ 'checkbox', 'radio' ] ) !== -1 ) {
                            paymentDetails[ ID ] = $( this ).prop( 'checked' );
                        } else {
                            paymentDetails[ ID ] = $( this ).val();
                        }
                    }
                });

                if ( ! $.isEmptyObject( paymentDetails ) ) {
                    $( '.payment_box :input' ).each( function() {
                        var ID = $( this ).attr( 'id' );
                        if ( ID ) {
                            if ( $.inArray( $( this ).attr( 'type' ), [ 'checkbox', 'radio' ] ) !== -1 ) {
                                $( this ).prop( 'checked', paymentDetails[ ID ] ).trigger( 'change' );
                            } else if ( $.inArray( $( this ).attr( 'type' ), [ 'select' ] ) !== -1 ) {
                                $( this ).val( paymentDetails[ ID ] ).trigger( 'change' );
                            } else if ( null !== $( this ).val() && 0 === $( this ).val().length ) {
                                $( this ).val( paymentDetails[ ID ] ).trigger( 'change' );
                            }
                        }
                    });
                }
                // Re-init methods
				//wc_checkout_form.init_payment_methods();
                init_payment_methods();

                $( 'body' ).trigger( 'updated_checkout', [ response ] );

            });

        } )

        $('#payment-bank-transfer').on( 'change', function() {
            if( $(this).prop( 'checked' ) ){
                $('.wow-payment-on-line .form-payments').slideUp();
                $('.wow-payment-gateways').slideUp();   
            } 
        } )
        $('#payment-online').on( 'change', function() {
            if( $(this).prop( 'checked' ) ){
                $('.wow-payment-on-line .form-payments').slideDown();
                $('.wow-payment-gateways').slideDown();  
                $('.wow-payment-gateways .wow-single-gateway-0').prop( 'checked', 'checked' ); 
            } 
        } )

        $( '.wow-shipping-input' ).on( 'click', function(e) {

            const current_vlaue = $(this).val();
            $( 'form.checkout input[value="' + current_vlaue + '"]' ).prop('checked', 'checked');

            const data = {
                action: 'set_shipping_method',
                security: wow_ajax.checkout_nonce,
                data: {
                    shipping_method: [current_vlaue]
                }
            }

            $.post( wow_ajax.url, data, function( response )
            {
                var paymentDetails = {};
                $( '.payment_box :input' ).each( function() {
                    var ID = $( this ).attr( 'id' );

                    if ( ID ) {
                        if ( $.inArray( $( this ).attr( 'type' ), [ 'checkbox', 'radio' ] ) !== -1 ) {
                            paymentDetails[ ID ] = $( this ).prop( 'checked' );
                        } else {
                            paymentDetails[ ID ] = $( this ).val();
                        }
                    }
                });

                if ( ! $.isEmptyObject( paymentDetails ) ) {
                    $( '.payment_box :input' ).each( function() {
                        var ID = $( this ).attr( 'id' );
                        if ( ID ) {
                            if ( $.inArray( $( this ).attr( 'type' ), [ 'checkbox', 'radio' ] ) !== -1 ) {
                                $( this ).prop( 'checked', paymentDetails[ ID ] ).trigger( 'change' );
                            } else if ( $.inArray( $( this ).attr( 'type' ), [ 'select' ] ) !== -1 ) {
                                $( this ).val( paymentDetails[ ID ] ).trigger( 'change' );
                            } else if ( null !== $( this ).val() && 0 === $( this ).val().length ) {
                                $( this ).val( paymentDetails[ ID ] ).trigger( 'change' );
                            }
                        }
                    });
                }
                // Re-init methods
				//wc_checkout_form.init_payment_methods();
                init_payment_methods();
                $( 'body' ).trigger( 'updated_checkout', [ response ] );

            } )
        } )

    });

    $('.wow-terms-checkbox').on( 'change', function() {

        if( $(this).is(':checked') ) {
            $('.woocommerce-form__input#terms').prop( 'checked', true );
        } else {
            $('.woocommerce-form__input#terms').prop( 'checked', false );
        }

    } );

    const miniCartqtyBttns = $('.offcanvas.offcanvas-end form.wow-mini-cart-form .quantity__btn');


    $(document).on('click', '.offcanvas.offcanvas-end form.wow-mini-cart-form .quantity__btn', function() {

        const data = {
            action: 'wow_update_minicart',
            security: wow_ajax.checkout_nonce,
            data: $( 'form.wow-mini-cart-form' ).serialize()
        }

        $.ajax({
            type: "POST",
            url: wow_ajax.url,
            data: data,
            beforeSend: function () {
                //$('#loader').removeClass('display-none')
            },
            success: function (data) {
                //$('#data-table').removeClass('display-none')   
                console.log(data)
                $('.wow-minicart-contents').replaceWith(data.renderContent);
                $('.wow-minicart-items-counter').text(data.cartItems)
                $('.offcanvas__cart-foot').remove();
                $('.offcanvas__cart-summary').replaceWith(data.miniCartTotals)
            },
            complete: function () {
                //$('#loader').addClass('display-none')
            },
        });
    })        

    const singleProductQtyBtns = $( '.wow-single-qty-btn' );
    
    if( singleProductQtyBtns ) {
        
        singleProductQtyBtns.each( function( index ) {

            $(this).on('click', function(e) {

                if( e.target.classList.contains( 'wow-single-qty-btn-minus' ) ) {

                    $current_val = $( '.wow-woocommerce-quantity-form .quantity__field' ).val();
                    $current_val == 1 ? '' : $current_val--;
                    $( '.wow-woocommerce-quantity-form .quantity__field' ).val( $current_val );

                } else if ( e.target.classList.contains( 'wow-single-qty-btn-plus' ) ) {

                    $current_val = $( '.wow-woocommerce-quantity-form .quantity__field' ).val();
                    $current_val++;
                    $( '.wow-woocommerce-quantity-form .quantity__field' ).val( $current_val);

                }
            })
        })
    }

    const singleProductAddToCartBtn = $( '.wow-single-product-add-btn' );

    if( singleProductAddToCartBtn ){
        singleProductAddToCartBtn.on( 'click', function() {
            $( '.wow-woocommerce-quantity-form .single_add_to_cart_button' ).trigger( 'click' );
        } )
    }

})
