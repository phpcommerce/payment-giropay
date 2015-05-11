<?php
namespace PHPCommerce\Payment\Service\Gateway;

use InvalidArgumentException;
use PHPCommerce\Common\Payment\Dto\PaymentRequestDTO;
use PHPCommerce\Common\Payment\Dto\PaymentResponseDTO;
use PHPCommerce\Common\Payment\PaymentType;
use PHPCommerce\Common\Payment\Service\PaymentGatewayHostedService;
use PHPCommerce\Common\Vendor\Service\Monitor\ServiceStatusDetectable;
use PHPCommerce\Core\Payment\Service\Exception\PaymentException;
use PHPCommerce\Vendor\Giropay\Service\Payment\GiropayConstants;
use PHPCommerce\Vendor\Giropay\Service\Payment\GiropayPaymentGatewayType;
use PHPCommerce\Vendor\Giropay\Service\Payment\Message\Transaction\GiropayTransactionStartRequest;
use PHPCommerce\Vendor\Giropay\Service\Payment\Message\Transaction\GiropayTransactionStartResponse;
use PHPCommerce\Vendor\Giropay\Service\Payment\Type\GiropayResultType;

/**
 * @Service("pcGiropayHostedService")
 */
class GiropayHostedServiceImpl implements PaymentGatewayHostedService {
    /**
     * @var GiropayConfiguration
     * @Resource(name = "pcGiropayConfiguration")
     */
    public $configuration;

    /**
     * @var ServiceStatusDetectable
     * @Resource(name = "pcGiropayPaymentServiceImpl")
     */
    public $giropayPaymentService;

    /**
     * @param PaymentRequestDTO $paymentRequestDTO
     * @throws PaymentException
     * @return PaymentResponseDTO
     */
    public function requestHostedEndpoint(PaymentRequestDTO $paymentRequestDTO)
    {
        $giropayRequest = new GiropayTransactionStartRequest();

        /*
         *
         * $request->setInfo1Label("Ihre Kundennummer");
         * $request->setInfo1Text("0815");
         */

        if($paymentRequestDTO->getSepaBankAccount() == null) {
            throw new InvalidArgumentException();
        }

        $giropayRequest->setMerchantTxId(1234567890);

        $giropayRequest->setPurpose($paymentRequestDTO->getOrderDescription());

        $giropayRequest->setCurrency($paymentRequestDTO->getOrderCurrencyCode());
        $giropayRequest->setAmount($paymentRequestDTO->getTransactionTotal());

        $giropayRequest->setIban($paymentRequestDTO->getSepaBankAccount()->getSepaBankAccountIBAN());
        $giropayRequest->setBic($paymentRequestDTO->getSepaBankAccount()->getSepaBankAccountBIC());

        $giropayRequest->setUrlRedirect($this->configuration->getRedirectUrl());
        $giropayRequest->setUrlNotify($this->configuration->getNotifyUrl());

        try {
            $giropayResponse = $this->giropayPaymentService->process($giropayRequest);
            /** @var GiropayTransactionStartResponse $giropayResponse */

            if(!GiropayResultType::$OK->equals($giropayResponse->getResult())) {
                throw new PaymentException($giropayResponse->getResult()->getType() . ' (' . $giropayResponse->getResult()->getFriendlyType() . ')' );
            }

        } catch (\Exception $e) {
            throw new PaymentException("Could not process payment: " . $e->getMessage(), 0, $e);
        }

        $responseDTO = new PaymentResponseDTO(PaymentType::$THIRD_PARTY_ACCOUNT, GiropayPaymentGatewayType::$GIROPAY);
        $responseDTO
            ->responseMap(GiropayConstants::HOSTED_REDIRECT_URL, $giropayResponse->getRedirect())
            ->responseMap(GiropayConstants::GATEWAY_TRANSACTION_ID, $giropayResponse->getReference()
        );

        return $responseDTO;
    }
}