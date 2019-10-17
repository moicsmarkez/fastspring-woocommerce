<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.facebook.com/moicsmarkez
 * @since      1.0.0
 *
 * @package    Fs_geek 
 * @subpackage Fs_geek/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fs_geek
 * @subpackage Fs_geek/admin
 * @author     moicsmarkez <moicsmarkez@gmail.com>
 */
class Fs_geek_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Notices (array)
     * @var array
     */
    public $notices = array();

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Fs_geek_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Fs_geek_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/fs_geek-admin.css', array(), $this->version, 'all');

        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Fs_geek_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Fs_geek_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/fs_geek-admin.js', array('jquery'), $this->version, false);

        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery'));
        wp_enqueue_script('fastspring_admin', 'https://d1f8f9xcsvx3ha.cloudfront.net/sbl/0.7.5/fastspring-builder.min.js', '', false, true);
    }

    /**
     * Get plugin setting link.
     *
     * @return string Setting link
     */
    public function get_setting_link() {

        return admin_url('admin.php?page=wc-settings&tab=checkout&section=geekyguards_fastspring');
    }

    public function fs_geek_woocommerce_product_write_panel_tabs() {
        echo '<li class="general_options general_tab"><a href="#producto_fastspring"><span><b>' . __('FastSpring', 'fs_geek') . '</b></span></a></li>';
    }

    /**
     * Allow this class and other classes to add slug keyed notices (to avoid duplication)
     *
     * @param string slug
     * @param string class
     * @param string message
     */
    public function add_admin_notice($slug, $class, $message) {
        $this->notices[$slug] = array(
            'class' => $class,
            'message' => $message,
        );
    }

    /**
     * Display admin notices and warnings
     */
    public function admin_notices() {
        foreach ((array) $this->notices as $notice_key => $notice) {
            echo "<div class='" . esc_attr($notice['class']) . "'><p>";
            echo wp_kses($notice['message'], array('a' => array('href' => array())));
            echo '</p></div>';
        }
    }

    public function fs_geek_woocommerce_product_write_panels() {
        global $post;
        $product = get_product($post->ID);

        $valor_prod = get_post_meta($product->id, '_producto_fastspring', true) ? get_post_meta($product->id, '_producto_fastspring', true) : '';
        //$args = array('post_type' => 'auto-terapia', 'post_parent' => 0,'post_status' => 'publish', 'posts_per_page'=>'-1');
        //$terapias = get_posts( $args );
        ?>
        <div id="overlay_loading" class="overlay_loading" style="cursor: wait; width: 80%;height: 100% !important;background-color: #00000087;position: absolute;z-index: 999;display: none;"></div>
        <div class="options_group show_if_fastspring_product">
            <h2 class="title"> Por favor ubica el producto relacionado con tus registrados en fastspring </h2>
            <p class="description">
                <?php echo __('debes seleccionar el producto que sea indicado para el pago correcto.', 'fs_geek'); ?>
            </p>


            <?php
            $url = 'https://api.fastspring.com/products';

            $context = stream_context_create(array(
                'http' => array(
                    'user_agent' => 'Mozilla/5.0', // Not important what it is but must be set
                    'header' => "Authorization: Basic " . base64_encode(Fs_geek::get_fs_option('api_username') . ':' . Fs_geek::get_fs_option('api_password')),
            )));

            $data = @json_decode(file_get_contents($url, false, $context));
            ?>
            <div class="options_group custom_tab_options opciones_producto_geekguard_fastspring">
                <p class="form-field">
                    <label style="width: 50%;" for="_proceso_auto_terapia" >"<?php echo $valor_prod ? 'Cambiar p' : 'P'; ?>roducto de "<b>FastSpring</b>": </label>
                    <select name="_producto_fastspring" id="_producto_fastspring"  class="select short">
                        <option value="">Elige un producto</option>
                        <?php foreach ($data->products as $producto) { ?>
                            <?php if ($valor_prod == $producto) { ?>
                                <option value="<?php echo $producto; ?>" selected="selected" ><?php echo $producto; ?></option>
                                <?php
                                continue;
                            }
                            ?>
                            <?php
                            if ($this->getIdFromMeta('_producto_fastspring', $producto)) {
                                continue;
                            }
                            ?>
                            <option value="<?php echo $producto; ?>" ><?php echo $producto; ?></option>
                        <?php } ?> 
                    </select>
                </p>
                <div class="datos-producto-fs">
                    <?php if ($valor_prod) { ?>
                        <p><b>Datos del producto seleccionado en FastSpring</b></p>
                        <table style="margin: 0 auto;width: 100%;padding: 0px 10px;">
                            <tbody>
                                <tr>
                                    <th style="text-align: right;" >Nombre:</th>
                                    <td><p data-fsc-item-path="<?php echo $valor_prod; ?>" data-fsc-item-display ></p></td>
                                </tr>
                                <tr>
                                    <th style="text-align: right;" >Precio:</th>
                                    <td><p data-fsc-item-path="<?php echo $valor_prod; ?>" data-fsc-item-price ></p></td>
                                </tr>
                            </tbody>
                        </table>
                        <input name="v_prc_fs_prod" type="hidden" data-fsc-item-path="<?php echo $valor_prod; ?>" data-fsc-item-priceValue value />
                        <input name="v_dis_prc_fs_prod" type="hidden" data-fsc-item-path="<?php echo $valor_prod; ?>" data-fsc-item-discountTotalValue value />
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function get_action_fs_geek_product() {
        $patch_product = $_POST['path_product'];

        $url = 'https://api.fastspring.com/products/' . $patch_product;
        $context = stream_context_create(array(
            'http' => array(
                'user_agent' => 'Mozilla/5.0', // Not important what it is but must be set
                'header' => "Authorization: Basic " . base64_encode(Fs_geek::get_fs_option('api_username') . ':' . Fs_geek::get_fs_option('api_password')),
        )));

        $data = @json_decode(file_get_contents($url, false, $context));

        if ($data) {
            ob_start();
            ?>
            <p><b>Datos del producto seleccionado en FastSpring</b></p>
            <table style="margin: 0 auto;width: 100%;padding: 0px 10px;">
                <tbody>
                    <tr>
                        <th style="text-align: right;" >Nombre:</th>
                        <td><p><?php echo $data->products[0]->display->en; ?></p></td>
                    </tr>
                    <tr>
                        <th style="text-align: right;" >Precio:</th>
                        <td><p>USD <?php echo wc_format_decimal($data->products[0]->pricing->price->USD); ?></p></td>
                    </tr>
                </tbody>
            </table>
            <input name="v_prc_fs_prod" type="hidden" value="<?php echo wc_format_decimal($data->products[0]->pricing->price->USD); ?>" />
            <?php
            $retorno = ob_get_clean();
            wp_send_json_success($retorno);
            wp_die();
        } else {
            wp_send_json_error('<p><center><b><em>No encontro ningun producto parecido, revisa tu tienda en fastspring</em></b></center></p>');
            wp_die();
        }
    }

    private function getIdFromMeta($meta_key, $meta_value) {
        global $wpdb;
        $pid = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '$meta_value' AND meta_key = '$meta_key' ORDER BY post_id DESC");
        if ($pid != '')
            return $pid;
        else
            return false;
    }

    public function geekyguard_product_type($types) {
        $types['fastspring_product'] = __('Producto FastSpring');
        return $types;
    }

    public function save_producto_fs_geek($post_id) {

        $proc_fs_geek = isset($_POST['_producto_fastspring']) ? $_POST['_producto_fastspring'] : '';
        $valor_producto_fastspring = isset($_POST['v_prc_fs_prod']) ? wc_format_decimal($_POST['v_prc_fs_prod']) : '';

        if (isset($proc_fs_geek) && $proc_fs_geek != '') {
            update_post_meta($post_id, '_producto_fastspring', $proc_fs_geek);
        } else {
            delete_post_meta($post_id, '_producto_fastspring');
        }

        if (isset($valor_producto_fastspring) && $valor_producto_fastspring != '') {
            update_post_meta($post_id, '_regular_price', $valor_producto_fastspring);
            update_post_meta($post_id, '_sale_price', '');
            update_post_meta($post_id, '_price', stripslashes($valor_producto_fastspring));
        }
    }

    public function add_gateways($methods) {
        $methods[] = 'WC_Gateway_FastSpring';
        return $methods;
    }

    /**
     * Load scripts hook handler
     *
     * @param $tag script tag
     * @param $tahandleg script handle
     * @return string tag
     */
    public function modificar_etiqueta_scripts($tag, $handle) {

        if ('fastspring' === $handle || 'fastspring_admin' == $handle) {

            $debug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? 'true' : 'false';
            return str_replace(' src', ' id="fsc-api"  data-storefront="' . $this->get_storefront_path() . '" data-before-requests-callback="fastspringBeforeRequestHandler" data-access-key="' . Fs_geek::get_fs_option('access_key') . '" data-popup-closed="fastspringPopupCloseHandler" data-debug="' . $debug . '" src', $tag);
        }
        return $tag;

        // Possible FastSpring script tag values
        /*
          id="fsc-api"
          src="https://d1f8f9xcsvx3ha.cloudfront.net/sbl/0.7.3/fastspring-builder.min.js" type="text/javascript"
          data-storefront="vendor.test.onfastspring.com"
          data-data-callback="dataCallbackFunction"
          data-error-callback="errorCallback"
          data-before-requests-callback="beforeRequestsCallbackFunction"
          data-after-requests-callback="afterRequestsCallbackFunction"
          data-before-markup-callback="beforeMarkupCallbackFunction"
          data-after-markup-callback="afterMarkupCallbackFunction"
          data-decorate-callback="decorateURLFunction"
          data-popup-event-received="popupEventReceived"
          data-popup-webhook-received="popupWebhookReceived"
          data-popup-closed="onPopupClose"
          data-access-key=".. access key .."
          data-debug="true"
          data-continuous="true"
         */
    }

    /**
     * Fetch stoefront path based on live/test mode
     *
     * @return string storefront path
     */
    protected function get_storefront_path() {
        return Fs_geek::get_fs_option('testmode') ? str_replace('onfastspring.com', 'test.onfastspring.com', Fs_geek::get_fs_option('storefront_path')) : str_replace('test.onfastspring.com', 'onfastspring.com', Fs_geek::get_fs_option('storefront_path'));
    }

    public function geekyguard_fs_product_class($classname, $product_type) {
        if ($product_type == 'fastspring_product') {
            $classname = 'WC_Product_FastSpring';
        }
        return $classname;
    }

    /**
     * Adds plugin action links
     */
    public function plugin_vinculo_opciones($links) {
        $setting_link = $this->get_setting_link();

        $plugin_links = array(
            '<a href="' . $setting_link . '">' . __('Opciones', 'fs_geek') . '</a>',
            '<a href="https://docs.fastspring.com">' . __('Docs', 'fs_geek') . '</a>',
        );
        return array_merge($plugin_links, $links);
    }

    /*
      public function compatibilidad_de_entorno() {
      include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
      $_warnings = self::adventencia_de_compatibilidad();

      if ($_warnings && is_plugin_active(plugin_dir_path(dirname(__FILE__)))) {
      $this->add_admin_notice('bad_environment', 'error', $_warnings);
      }

      $bad = !Fs_geek::get_fs_option('access_key') || !Fs_geek::get_fs_option('storefront_path') || !Fs_geek::get_fs_option('private_key');

      if ($bad && !(isset($_GET['page'], $_GET['section']) && 'wc-settings' === $_GET['page'] && 'geekyguards_fastspring' === $_GET['section'])) {
      $setting_link = $this->get_setting_link();
      $this->add_admin_notice('prompt_connect', 'notice notice-warning', sprintf(__('FastSpring está casi listo. Para instalar, <a href="%s">configura tus credenciales de FastSpring</a>.', 'fs_geek'), $setting_link));
      }
      }

      static function adventencia_de_compatibilidad() {
      if (version_compare(phpversion(), PLUGIN_MIN_PHP_VER, '<')) {
      $message = __('GeekyGuards WC FastSpring - La versión mínima de PHP requerida para este complemento es %1$s. Estás ejecutando %2$s.', 'fs_geek');

      return sprintf($message, PLUGIN_MIN_PHP_VER, phpversion());
      }

      if (!is_plugin_active('woocommerce/woocommerce.php')) {
      return __('GeekyGuards WC FastSpring requiere que WooCommerce se active para funcionar.', 'fs_geek');
      }

      if (version_compare(WC_VERSION, PLUGIN_MIN_WC_VER, '<')) {
      $message = __('GeekyGuards WC FastSpring - La versión mínima de WooCommerce requerida para este complemento es %1$s. Estas usuando %2$s.', 'fs_geek');

      return sprintf($message, PLUGIN_MIN_WC_VER, WC_VERSION);
      }

      return false;
      } */
}
