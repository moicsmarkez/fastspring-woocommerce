<?php
if (!defined('ABSPATH')) {
    exit;
}


class WC_Product_FastSpring extends WC_Product_Simple {

    public function __construct($product) {
        $this->product_type = 'fastspring_product';
        parent::__construct($product);
    }

    public function get_type() {
        return 'fastspring_product';
    }

}
