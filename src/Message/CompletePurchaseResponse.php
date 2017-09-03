<?php

namespace Omnipay\WebMoney\Message;

use Illuminate\Support\Facades\Log;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * WebMoney Complete Purchase Response.
 * https://merchant.wmtransfer.com/conf/guide.asp.
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 */
class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * CompletePurchaseResponse constructor.
     *
     * @param RequestInterface $request
     * @param mixed            $data
     *
     * @throws InvalidResponseException
     */
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data    = $data;

        if ($this->getHash() !== $this->calculateHash()) {
            throw new InvalidResponseException('Invalid hash');
        }

        if ($this->request->getTestMode() !== $this->getTestMode()) {
            throw new InvalidResponseException('Invalid test mode');
        }
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->data['LMI_PAYMENT_NO'];
    }

    /**
     * @return mixed
     */
    public function getTransactionReference()
    {
        return $this->data['LMI_SYS_TRANS_NO'];
    }

    /**
     * @return mixed
     */
    public function getMerchantPurse()
    {
        return $this->data['LMI_PAYEE_PURSE'];
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->data['LMI_PAYMENT_AMOUNT'];
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->request->getCurrencyByPurse($this->data['LMI_PAYEE_PURSE']);
    }

    /**
     * @return bool
     */
    public function getTestMode()
    {
        return (bool)$this->getMode();
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->data['LMI_MODE'];
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->data['LMI_HASH'];
    }

    /**
     * @return null|string
     */
    public function getHashType()
    {
        switch (strlen($this->getHash())) {
            case 32:
                return 'md5';
            case 64:
                return 'sha256';
            case 132:
                return 'sign';
            default:
                return null;
        }
    }

    /**
     * Calculate hash to verify transaction details.
     *
     * The control signature lets the merchant verify the source of data and the integrity of data transferred to the
     * Result URL in the 'Payment notification form '. The control signature is generating by 'sticking' together
     * values of parameters transmitted in the 'Payment notification form' in the following order: Merchant's purse
     * (LMI_PAYEE_PURSE); Amount (LMI_PAYMENT_AMOUNT); Purchase number (LMI_PAYMENT_NO); Test mode flag (LMI_MODE);
     * Account number in WebMoney Transfer (LMI_SYS_INVS_NO); Payment number in WebMoney Transfer (LMI_SYS_TRANS_NO);
     * Date and time of payment (LMI_SYS_TRANS_DATE); Secret Key (LMI_SECRET_KEY); Customer's purse (LMI_PAYER_PURSE);
     * Customer's WM id (LMI_PAYER_WM).
     *
     * @return string
     *
     * @throws InvalidResponseException
     */
    private function calculateHash()
    {
        $hashType = $this->getHashType();

        if ($hashType == 'sign') {
            throw new InvalidResponseException('Control sign forming method "SIGN" is not supported');
        } elseif ($hashType == null) {
            throw new InvalidResponseException('Invalid signature type');
        }

        return strtoupper(hash(
            $hashType,
            $this->data['LMI_PAYEE_PURSE'] .
            $this->data['LMI_PAYMENT_AMOUNT'] .
            $this->data['LMI_PAYMENT_NO'] .
            $this->data['LMI_MODE'] .
            $this->data['LMI_SYS_INVS_NO'] .
            $this->data['LMI_SYS_TRANS_NO'] .
            $this->data['LMI_SYS_TRANS_DATE'] .
            $this->request->getSecretkey() .
            $this->data['LMI_PAYER_PURSE'] .
            $this->data['LMI_PAYER_WM']
        ));
    }

    /**
     * @return void
     */
    public function confirm()
    {
        $this->exitWith('YES');
    }

    /**
     * @param string $description
     */
    public function error($description = null)
    {
        $this->exitWith('ERR: ' . $description);
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string $result
     * @param string $description
     */
    public function exitWith($result)
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo $result;
        exit;
    }
}
