<?php
namespace PHPCommerce\Vendor\Giropay\Service\Payment\Type;

class GiropayMethodType {
    private static $TYPES = array();

    /**
     * @var GiropayType
     */
    public static $BANKSTATUS;

    /**
     * @var GiropayType
     */
    public static $TRANSACTION_START;

    /**
     * @var GiropayType
     */
    public static $TRANSACTION_NOTIFY;

    /**
     * @var GiropayType
     */
    public static $TRANSACTION_STATUS;

    public function __construct($type = null, $friendlyType = null) {
        if ($friendlyType) {
            $this->friendlyType = $friendlyType;
        }

        if ($type) {
            $this->setType($type);
        }
    }

    public function getType() {
        return $this->type;
    }

    public function getFriendlyType() {
        return $this->friendlyType;
    }

    private function setType($type) {
        $this->type = $type;

        if (!array_key_exists($type, self::$TYPES)) {
            self::$TYPES[$type] = $this;
        }
    }

    public function equals(GiropayMethodType $obj) {
        return ($this->getType() == $obj->getType());
    }
}

GiropayMethodType::$BANKSTATUS          = new GiropayMethodType("bankstatus", "Bankstatus");
GiropayMethodType::$TRANSACTION_START   = new GiropayMethodType("transaction_start", "Transaction Start");
GiropayMethodType::$TRANSACTION_NOTIFY  = new GiropayMethodType("transaction_notify", "Transaction Notify");
GiropayMethodType::$TRANSACTION_STATUS  = new GiropayMethodType("transaction_status", "Transaction Status");
