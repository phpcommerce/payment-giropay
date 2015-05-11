<?php
namespace PHPCommerce\Vendor\Giropay\Service\Payment;

use PHPCommerce\Common\Payment\PaymentGatewayType;

class GiropayPaymentGatewayType extends PaymentGatewayType {
    /**
     * @var PaymentGatewayType
     */
    public static $GIROPAY;
}

GiropayPaymentGatewayType::$GIROPAY = new PaymentGatewayType("Giropay", "Giropay");