<?php

if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-fs_geek-constructor.php';

/**
 * WC_Gateway_FastSpring class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_FastSpring extends WC_Payment_Gateway {

    /**
     * API Context used for PayPal Authorization
     * @var null
     */
    public $apiContext = null;

    /**
     * Constructor for your shipping class
     *
     * @access public
     * @return void
     */
    public function __construct() {
        $this->id = 'geekyguards_fastspring';
        $this->method_title = __('FastSpring', 'fs_geek');
        $this->method_description = __('Pasarela de pago desarrollada para geekyguards', 'fs_geek');
        $this->title = $this->Opciones('title');
        $this->description = $this->Opciones('description');
        if ($this->Opciones('testmode')) {
            $this->description .= "\n" . sprintf(__('MODO DE PRUEBA ACTIVADO. En el modo de prueba, puede usar los números de tarjeta proporcionados en el panel de prueba del tablero de FastSpring. Consulte la documentación  "<a target="_blank" href="%s">Testing Orders</a>" para obtener más información.', 'woocommerce-gateway-fastspring'), 'http://docs.fastspring.com/activity-events-orders-and-subscriptions/test-orders');
            $this->description = trim($this->description);
        }

        $this->has_fields = false;
        $this->supports = array(
            'products',
            'refunds',
            'subscriptions', // subscription.activated
            'subscription_cancellation', // FS subscription.canceled
            'subscription_suspension', // FS subscription.deactivated,
            'subscription_reactivation', // FS  subscription.activated
            'subscription_amount_changes', // FS subscription.updated
            'subscription_date_changes', // FS subscription.updated
            'multiple_subscriptions',
        );
        //$this->get_paypal_sdk();
// Load the settings.
        $this->init_form_fields();
        $this->init_settings();
        $this->icon = apply_filters('woocommerce_gateway_icon', plugins_url('../public/img/fs_geek_icon.png', __FILE__));
        //$this->enabled = $this->get_option('enabled');
        //add_action('check_woopaypal', array($this, 'check_response'));
// Save settings
        if (is_admin()) {
            // Versions over 2.0
            // Save our administration options. Since we are not going to be doing anything special
            // we have not defined 'process_admin_options' in this class so the method in the parent
            // class will be used instead
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }
//HOoKS        
        add_action('wc_ajax_wc_fastspring_order_complete', array($this, 'ajax_order_complete'));
        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
        add_action('woocommerce_api_wc_gateway_fastspring_commerce', array($this, 'return_handler'));
    }

    /**
     * Validate access key settings field
     *
     * @params $value
     */
    public function validate_access_key_field($key, $value) {
        if (!empty($value)) {
            return $value;
        }
        WC_Admin_Settings::add_error(esc_html__('Se requiere una clave de acceso FastSpring.', 'fs_geek'));
    }

    /**
     * Validate private key settings field
     *
     * @params $value
     */
    public function validate_private_key_field($key, $value) {

        if (@openssl_private_encrypt('abc', $aes_key_encrypted, openssl_pkey_get_private($value))) {
            return $value;
        }

        WC_Admin_Settings::add_error(esc_html__('El campo de la clave privada de RSA no es válido.', 'fs_geek'));
    }

    /**
     * Validate title settings field
     *
     * @params $value
     */
    public function validate_title_field($key, $value) {
        if (empty($value)) {
            WC_Admin_Settings::add_error(esc_html__('Introduce un titulo valido.', 'fs_geek'));
        }
        return $value;
    }

    /**
     * Validate storefront path settings field
     *
     * @params $value
     */
    public function validate_storefront_path_field($key, $value) {

        if (empty($value)) {
            WC_Admin_Settings::add_error(esc_html__('Introduce una direcion valida del storefront .', 'fs_geek'));
        } else if (!empty($value)) {
            return preg_replace('#^https?://#', '', rtrim($value, '/'));
        }
    }

    /**
     * Check if this gateway is enabled
     */
    public function is_available() {

        if (!$this->Opciones('enabled')) {
            return false;
        }

        if ($this->Opciones('access_key') && $this->Opciones('private_key') && $this->Opciones('storefront_path')) {
            return true;
        }
        return false;
    }

    public function init_form_fields() {
        $this->form_fields = include 'opciones_pasarela_fs_geek.php';
    }

    /**
     * Process the payment.
     *
     * @param int $order_id
     *
     * @return array|void
     */
    public function process_payment($order_id) {

        $order = wc_get_order($order_id);

        return array(
            'result' => 'success',
            'session' => WC_Gateway_FastSpring_Builder::get_secure_json_payload(),
            'receip_url' => $order->get_checkout_order_received_url(),
        );
    }

    /**
     * Payment_scripts function.
     *
     * Outputs scripts used for fastspring payment
     */
    public function payment_scripts() {

        $load_scripts = false;

        if (is_checkout()) {
            $load_scripts = true;
        }

        if ($this->is_available()) {
            $load_scripts = true;
        }

        if (false === $load_scripts) {
            return;
        }

        $suffix = ''/* defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min' */;

        if ($this->Opciones('enabled')) {

            wp_enqueue_script('fastspring', GEEK_WC_FASTSPRING_LIB, '', false, true);

            wp_enqueue_script('wc_geekguards_fastspring', plugins_url('admin/js/fs_geek_checkout' . $suffix . '.js', GEEK_BASE_MAIN_FILE), array('jquery', 'fastspring'), FS_GEEK_VERSION, true);
        }

        $fastspring_params = array(
            'ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
            'nonce' => array(
                'receipt' => wp_create_nonce('wc-fastspring-receipt'),
            ),
        );

        wp_localize_script('wc_geekguards_fastspring', 'wc_geekguards_fastspring_params', apply_filters('woocommerce_fastspring_params', $fastspring_params));
    }

    /**
     * Opciones
     *
     * @param string $opcion opcion name
     * @return mixed option value
     */
    public function Opciones($opcion) {
        return Fs_geek::get_fs_option($opcion);
    }

    /**
     * Logs
     *
     * @param string $message
     */
    public function log($message) {
        Fs_geek::log($message);
    }

    /**
     * Payment form on checkout page.
     */
    public function payment_fields() {
        $description = $this->get_description();

        if ($description) {
            echo wpautop(wptexturize(trim($description)));
        }
    }

}
