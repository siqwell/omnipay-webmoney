<?php

namespace Omnipay\WebMoney\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * WebMoney Purchase Request
 * https://merchant.wmtransfer.com/conf/guide.asp.
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2017 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @return int
     */
    public function getHold()
    {
        if ($hold = $this->getParameter('hold')) {
            return (string)$hold;
        }

        return '0';
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setHold($value)
    {
        return $this->setParameter('hold', $value);
    }

    /**
     * Get the allow sdp.
     *
     * @return string cancel method
     */
    public function getAllowSdp()
    {
        return $this->getParameter('allowSdp');
    }

    /**
     * Set the allow sdp.
     *
     * @param string $value cancel method
     *
     * @return self
     */
    public function setAllowSdp($value)
    {
        return $this->setParameter('allowSdp', $value);
    }

    /**
     * Set lang
     *
     * @return self
     */
    public function getLang()
    {
        return $this->getParameter('lang');
    }

    /**
     * @param string $value interface lang (ru-RU, en-US, vi-VN
     * @return self
     */
    public function setLang($value)
    {
        return $this->setParameter('lang', $value);
    }

    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $this->validate(
            'merchantPurse',
            'transactionId',
            'description',
            'returnUrl',
            'cancelUrl',
            'notifyUrl',
            'currency',
            'amount'
        );

        if ($this->getCurrencyByPurse($this->getMerchantPurse()) !== $this->getCurrency()) {
            throw new InvalidRequestException('Invalid currency for this merchant purse');
        }

        $data = [
            'LMI_PAYEE_PURSE'         => $this->getMerchantPurse(),
            'LMI_PAYMENT_AMOUNT'      => $this->getAmount(),
            'LMI_PAYMENT_NO'          => $this->getTransactionId(),
            'LMI_PAYMENT_DESC_BASE64' => base64_encode($this->getDescription()),
            'LMI_SIM_MODE'            => $this->getTestMode() ? '2' : '0',
            'LMI_RESULT_URL'          => $this->getNotifyUrl(),
            'LMI_SUCCESS_URL'         => $this->getReturnUrl(),
            'LMI_SUCCESS_METHOD'      => $this->getReturnMethod(),
            'LMI_FAIL_URL'            => $this->getCancelUrl(),
            'LMI_FAIL_METHOD'         => $this->getCancelMethod(),
            'LMI_HOLD'                => $this->getHold()
        ];

        if ($this->getAllowSdp() !== null) {
            $data['LMI_ALLOW_SDP'] = $this->getAllowSdp();
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return PurchaseResponse
     */
    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data, $this->getLang());
    }
}
