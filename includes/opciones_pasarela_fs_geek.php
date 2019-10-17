<?php
if (!defined('ABSPATH')) {
  exit;
}

return apply_filters('opciones_pasarela_fs_geek',

  array(
    'enabled' => array(
      'title' => __('Activado/Desactivado', 'fs_geek'),
      'label' => __('Activar pasarela de pago Fastspring', 'fs_geek'),
      'type' => 'checkbox',
      'description' => '',
      'default' => 'no',
    ),
    'title' => array(
      'title' => __('Titulo', 'fs_geek'),
      'type' => 'text',
      'description' => __('El título que el usuario ve durante el pago. Manténgalo breve para evitar posibles problemas de diseño con iconos.', 'fs_geek'),
      'default' => __('FastSpring', 'fs_geek'),
      'desc_tip' => false,
    ),
    'description' => array(
      'title' => __('Descripción', 'fs_geek'),
      'type' => 'text',
      'description' => __('La descripción más larga que el usuario ve una vez seleccionando esta pasarela de pago.', 'fs_geek'),
      'default' => __('Paga con Tarjeta de credito, PayPal, Amazon y mucho mas modo de pago.', 'fs_geek'),
      'desc_tip' => false,
    ),
    'testmode' => array(
      'title' => __('Modo prueba (Test Mode)', 'fs_geek'),
      'label' => __('Activar modo prueba', 'fs_geek'),
      'type' => 'checkbox',
      'description' => __('Coloca la pasarela de pago en modo de prueba. En este modo, puede usar los números de tarjeta proporcionados en el panel de prueba del panel de FastSpring. Consulte la documentación "<a target="_blank" href="http://docs.fastspring.com/activity-events-orders-and-subscriptions/test-orders">Testing Orders</a>"  para obtener más información.', 'fs_geek'),
      'default' => 'no',
      'desc_tip' => false,
    ),
    'logging' => array(
      'title' => __('Registro log', 'fs_geek'),
      'label' => __('Registra mensaje de depuración', 'fs_geek'),
      'type' => 'checkbox',
      'description' => __('Guarda los mensajes de depuracion en los estados de Woocommerces log.', 'fs_geek'),
      'default' => 'no',
    ),
    'storefront_path' => array(
      'title' => __('Storefront (Tienda FastSpring)', 'fs_geek'),
      'type' => 'text',
      'description' => __('La ruta de su tienda FastSpring en modo producción (live) (por ejemplo: mystore.onfastspring.com/mystore). Este complemento maneja dominios hospedados o ventanas emergentes.', 'fs_geek)'),
      'desc_tip' => false,
    ),
    'api_details' => array(
      'title' => __('Credenciales de acceso', 'fs_geek'),
      'type' => 'title',
      'description' => __('Su clave de acceso y clave privada se utilizan para encriptar la información de la orden que se envía a FastSpring. Consulte la <a target="_blank" href="http://docs.fastspring.com/integrating-with-fastspring/store-builder-library/passing-sensitive-data-with-secure-requests"> documentación </a> en la sección <i> Generación de "securePayload" y "secureKey" </i> para obtener instrucciones sobre cómo crear un certificado SSL y claves privadas / públicas. Una vez generado, ingrese la clave privada a continuación y la clave pública en el panel de FastSping en  <i>Integrations > Store Builder Library</i> donde también puede obtener la clave de acceso para ingresar a continuación.', 'fs_geek'),
    ),
    'access_key' => array(
      'title' => __('Clave de acceso', 'fs_geek'),
      'type' => 'text',
      'description' => __('Su clave de acceso FastSpring.', 'fs_geek'),
      'desc_tip' => false,
    ),
    'private_key' => array(
      'title' => __('Clave privada', 'fs_geek'),
      'type' => 'textarea',
      'description' => __('RSA certificado privado.', 'fs_geek'),
      'desc_tip' => false,
    ),
    'order_verification' => array(
      'title' => __('Verificación de orden', 'fs_geek'),
      'type' => 'title',
      'description' => __('Para permitir que FastSpring marque pedidos como completados en WooCommerce, puede usar un Webhook o FastSpring API. Si está utilizando una tienda alojada, debe usar el método webhook. Si, por el contrario, está utilizando una ventana emergente, puede usar un webhook o una llamar a la API (o ambas). <h4> Instrucciones del método Webhook </h4> Para utilizar el método webhook, genere una clave secreta a continuación e introdúzcalo junto con su URL webhook (<i> '. site_url ('? wc-api = wc_gateway_fastspring ',' https ' ). '</ i>) en el panel de FastSpring en <i> Integrations > Webhooks </i> en los campos HMAC SHA256 secret y URL respectivamente. <h4> Instrucciones de método de API </h4> Para usar el método de verificación API, ingrese su nombre de usuario y contraseña API a continuación. Estos se pueden generar desde el panel de FastSpring en <i> Integrations >  API Credentials </i>.', 'fs_geek'),
    ),
    'webhook_secret' => array(
      'title' => __('Clave secret Webhook', 'fs_geek'),
      'type' => 'text',
      'description' => __('Una clave secret webhook es una secuencia aleatoria de caracteres utilizados para autenticar las llamadas webhook. <br> Los valores predeterminados se generaron automáticamente para su comodidad.', 'fs_geek'),
      'desc_tip' => false,
      'default' => substr(str_shuffle(MD5(microtime())), 0, 30),
    ),
    'api_username' => array(
      'title' => __('Nombre de usuario API ', 'fs_geek'),
      'type' => 'text',
      'description' => __('Tu API username, de FastSpring.', 'fs_geek'),
      'desc_tip' => false,
    ),
    'api_password' => array(
      'title' => __('Contraseña API ', 'fs_geek'),
      'type' => 'text',
      'description' => __('Tu API password, de FastSpring.', 'fs_geek'),
      'desc_tip' => false,
    ),

  )
);
