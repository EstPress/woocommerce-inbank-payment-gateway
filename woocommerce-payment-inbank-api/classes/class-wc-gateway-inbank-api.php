<?php
class WC_Gateway_Inbank extends WC_Payment_Gateway {

    public function __construct() {
        $this->id = 'inbank';

        $this->init_settings();
        $this->init_form_fields();

        $this->method_title = __( 'Inbank järelmaks', 'woocommerce-payment-inbank-api' );
        $this->title        = $this->settings['title'];
        $this->icon         = $this->settings['logo_url'];
        $this->has_fields   = ($this->settings['enableApi'] == 'yes' ? true : false);
        $this->enableApi    = $this->settings['enableApi'];
        $this->api_key      = $this->settings['api_key'];
        $this->api_url      = $this->settings['api_url'];
        $this->vendor       = $this->settings['vendor'];
        $this->campaign     = $this->settings['campaign'];
        $this->test_api     = $this->settings['test_api'];

        add_filter( 'woocommerce_payment_successful_result', array( $this, 'alter_response' ), 10, 2 );
        add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'update_fragments' ) );
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_custom', array( $this, 'thankyou_page' ) );
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => __( 'Enable/Disable', 'woocommerce-payment-inbank-api' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable Inbank', 'woocommerce-payment-inbank-api' ),
                'default' => 'no'
            ),
            'enableApi' => array(
                'title'   => __( 'Enable/Disable API', 'woocommerce-payment-inbank-api' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable Inbank API', 'woocommerce-payment-inbank-api' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title'       => __( 'Title', 'woocommerce-payment-inbank-api' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-payment-inbank-api' ),
                'default'     => __( 'Inbank järelmaks', 'woocommerce-payment-inbank-api' )
            ),
            'description' => array(
                'title'       => __('Description', 'woocommerce-payment-inbank-api'),
                'type'        => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.', 'woocommerce-payment-inbank-api' ),
                'default'     => __( '', 'woocommerce-payment-inbank-api' ),
            ),
            'logo' => array(
                'title'         => __('Logo', 'woocommerce-payment-inbank-api'),
                'type'          => 'file',
                'description'   => __( 'Recommended image height is 31px', 'woocommerce-payment-inbank-api' ),
                'current_image' => $this->settings['logo_url']
            ),
            'test_api' => array(
                'title'   => __( 'Enable test server', 'woocommerce-payment-inbank-api' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable', 'woocommerce-payment-inbank-api'),
                'default' => 'no'
            ),
            'api_url' => array(
                'title'       => __( 'API url', 'woocommerce-payment-inbank-api'),
                'type'        => 'text',
                'description' => __( 'Url to access the API.', 'woocommerce-payment-inbank-api' ),
                'default'     => 'https://api.test.cofi.ee',
                'desc_tip'    => true,
            ),
            'api_key' => array(
                'title'       => __( 'API key', 'woocommerce-payment-inbank-api' ),
                'type'        => 'text',
                'description' => __( 'Get your API credentials from Inbank.', 'woocommerce-payment-inbank-api' ),
                'desc_tip'    => true,
            ),
            'vendor' => array(
                'title'       => __( 'Vendor code', 'woocommerce-payment-inbank-api' ),
                'type'        => 'text',
                'description' => __( 'Your vendor code', 'woocommerce-payment-inbank-api' ),
                'desc_tip'    => true
            ),
            'campaign'  => array(
                'title' => __( 'Campaign code (optional)', 'woocommerce-payment-inbank-api' ),
                'type'  => 'text',
            ),
            'logo_url' => array(
                'type' => 'hidden'
            )
        );
    }

    public function process_admin_options() {
        // Save regular options
        parent::process_admin_options();

        if ( ! empty( $_FILES['woocommerce_inbank_logo'] ) && $_FILES['woocommerce_inbank_logo']['error'] === UPLOAD_ERR_OK ) {
            $uploaded_image = wp_upload_bits( $_FILES['woocommerce_inbank_logo']['name'], null, file_get_contents( $_FILES['woocommerce_inbank_logo']['tmp_name'] ) );
            $this->sanitized_fields['logo_url'] = $uploaded_image['url'];
        } else {
            $this->sanitized_fields['logo_url'] = $this->settings['logo_url'];
        }

        if ( $this->sanitized_fields['logo_url'] != $this->settings['logo_url'] ) {
            $this->settings['logo_url'] = $this->sanitized_fields['logo_url'];
            update_option( $this->plugin_id . $this->id . '_settings', apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings ) );
        }
    }

    public function validate_fields() {
        if ( ! empty( $_POST['inbank_offer'] ) ) {
            return true;
        }

        if ($this->enableApi == 'yes') {

            $required_fields = array(
                'pia_fullname'    => __( 'Nimi', 'woocommerce-payment-inbank-api' ),
                'pia_idcode'      => __( 'Isikukood', 'woocommerce-payment-inbank-api' ),
                'pia_doctype'     => __( 'Dokumendi tüüp', 'woocommerce-payment-inbank-api' ),
                'pia_document_nr' => __( 'Dokumendi nr.', 'woocommerce-payment-inbank-api' ),
                'pia_phone'       => __( 'Telefon', 'woocommerce-payment-inbank-api' ),
                'pia_email'       => __( 'E-mail', 'woocommerce-payment-inbank-api' ),
                'pia_address'     => __( 'Aadress (Tänav, Maja/Korter)', 'woocommerce-payment-inbank-api' ),
                'pia_city'        => __( 'Linn', 'woocommerce-payment-inbank-api' ),
                'pia_county'      => __( 'Maakond', 'woocommerce-payment-inbank-api' ),
                'pia_zip'         => __( 'Postiindeks', 'woocommerce-payment-inbank-api' ),
                'pia_language'    => __( 'Suhtluskeel', 'woocommerce-payment-inbank-api' ),
                'pia_job'         => __( 'Töökoht', 'woocommerce-payment-inbank-api' ),
                'pia_wage'        => __( 'Sissetulek', 'woocommerce-payment-inbank-api' ),
                'pia_expenses'    => __( 'Olemasolevad kohustused', 'woocommerce-payment-inbank-api' ),
                'pia_bank'        => __( 'Pank', 'woocommerce-payment-inbank-api' ),
                'pia_account_nr'  => __( 'Konto nr. / IBAN', 'woocommerce-payment-inbank-api' ),
                'pia_payments'    => __( 'Osamaksete arv', 'woocommerce-payment-inbank-api' ),
                'pia_employee'    => __( 'Töökoht', 'woocommerce-payment-inbank-api' ),
                'pia_inbank_tos'  => __( 'Tingimused', 'woocommerce-payment-inbank-api' )
            );

            foreach ( $required_fields as $field_name => $field_label ) {
                if ( empty( $_POST[$field_name] ) ) {
                    if ( $field_name === 'inbank_tos' ) {
                        wc_add_notice( __( "Palun nõustu avalduse tingimustega", 'woocommerce-payment-inbank-api' ), 'error' );
                        return;
                    } else {
                        wc_add_notice( sprintf( __( "%s - ei tohi tühi olla", 'woocommerce-payment-inbank-api' ), "<strong>$field_label</strong>" ), 'error' );
                        return;
                    }
                } else {
                    if ( $field_name === 'idcode' && ! self::validate_idcode( $_POST['idcode'] ) ) {
                        wc_add_notice( __( "Vigane isikukood", 'woocommerce-payment-inbank-api' ), 'error' );
                        return;
                    }
                }
            }

            if ( ! empty( $empty_fields ) ) {
                return false;
            }

            $response = wp_remote_post( $this->api_url . '/financing_requests', array(
                'sslverify' => false,
                'timeout' => 6000,
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body' => $this->generate_financing_request( $_POST )
            ) );

            $response_data = json_decode($response['body']);

            if ( $response_data->decision->status === 'denied' ) {
                wc_add_notice( $response_data->decision->message, 'error' );
                return false;
            } else if ( ! empty( $response_data->error ) ) {
                wc_add_notice( $response_data->error->message, 'error' );
                return false;
            }

            return true;
        } else {
            return true;
        }

    }

    public function process_payment( $order_id ) {

        $order = wc_get_order( $order_id );
        $order->payment_complete();
        $order->update_status( 'wc-on-hold', 'Fusion liising' );
        $order->reduce_order_stock();

        WC()->cart->empty_cart();

        // Return thank you redirect
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url( $order )
        );
    }

    public function alter_response( $result, $order_id ) {
        if ( empty( $_POST['inbank_offer'] ) ) {
            $result['refresh'] = 'true';
        }

        return $result;
    }

    public function update_fragments( $fragments ) {
        return $fragments;
    }

    public function payment_fields() {
        if ( $this->enableApi == 'yes' ) {

            $response = wp_remote_get( $this->api_url . '/options?key=' . $this->api_key , array( 'sslverify' => false ) );
            $select_fields = json_decode( $response['body'] );

            $sorted_options = array(
                'doctype'  => array(),
                'county'   => array(),
                'language' => array(),
                'bank'     => array(),
                // 'payments' => array() // Not in the array anymore
            );
            foreach ( $select_fields as $option ) {
                $sorted_options[$option->option->code][] = $option;
            }

            include plugin_dir_path( __FILE__ ) . '../templates/payment-fields.php';

        }
    }

    public function update_checkout_fields( $fragments ) {
        $fragments[] = array(
            'payment_box.payment_method_inbank' => '<div>Test</div>'
        );

        return $fragments;
    }

    private static function validate_idcode( $input ) {
        if ( strlen( ltrim( $input ) ) != 11 && ! is_numeric( $input ) ) {
            return false;
        }

        $ik = $input;
        $kontrollnr = substr( $ik, 10, 1 );
        $nr = " " . $input;
        $knr = ( $nr[1] + $nr[2] * 2 + $nr[3] * 3 + $nr[4] * 4 + $nr[5] * 5 + $nr[6] * 6 + $nr[7] * 7 + $nr[8] * 8 + $nr[9] * 9 + $nr[10] ) % 11;
        if ( $knr == 10 ) {
            $knr = ( $nr[1] * 3 + $nr[2] * 4 + $nr[3] * 5 + $nr[4] * 6 + $nr[5] * 7 + $nr[6] * 8 + $nr[7] * 9 + $nr[8] + $nr[9] * 2 + $nr[10] * 3 ) % 11;
        }

        if ( $nr[1] < 1 && $nr[1] > 6 ) {
            return false;
        }
        if ( substr($nr, 4, 2) > 12 ) {
            return false;
        }
        if ( substr($nr, 6, 2) > 31 ) {
            return false;
        }

        if ( $knr == $kontrollnr ) {
            return true;
        } else {
            return false;
        }
    }

    private function generate_financing_request( array $fields ) {
        $request_data =  array(
            'financing_request' => array(
                'address'         => $fields['pia_address'],
                'county'          => $fields['pia_county'],
                'docnr'           => $fields['pia_document_nr'],
                'identity_code'   => $fields['pia_idcode'],
                'zip'             => $fields['pia_zip'],
                'bank'            => $fields['pia_bank'],
                'doctype'         => $fields['pia_doctype'],
                'phone'           => $fields['pia_phone'],
                'language'        => $fields['pia_language'],
                'liabilities'     => $fields['pia_expenses'],
                'city'            => $fields['pia_city'],
                'full_name'       => $fields['pia_fullname'],
                'salary'          => $fields['pia_wage'],
                'bank_account_no' => $fields['pia_account_nr'],
                'email'           => $fields['pia_email'],
                'payments'        => $fields['pia_payments'],
                'employee'        => $fields['pia_employee'],
                'down_payment'    => $fields['pia_down_payment']
            ),
            'key' => $this->settings['api_key'],
            'vendor' => $this->settings['vendor']
        );

        foreach ( WC()->cart->get_cart() as $cart_line ) {
            for ( $i = 0; $i < $cart_line['quantity']; $i++ ) {
                $request_data['financing_request']['assets'][] = array(
                    'asset_type' => 'Kaup',
                    'amount' => $cart_line['data']->price,
                    'desc' => $cart_line['data']->post->post_title
                );
            }
        }

        return json_encode( $request_data );
    }
}
