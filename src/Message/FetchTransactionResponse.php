<?php

namespace Omnipay\WebMoney\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * WebMoney Fetch Transactions Response
 * http://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_X18.
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 */
class FetchTransactionResponse extends AbstractResponse
{
    /**
     * FetchTransactionResponse constructor.
     *
     * @param RequestInterface  $request
     * @param \SimpleXMLElement $data
     */
    public function __construct(RequestInterface $request, \SimpleXMLElement $data)
    {
        $this->request = $request;
        $this->data    = $data;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getCode() === 0;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return (int)$this->data->retval;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        $message = (string)$this->data->retdesc;

        return $message ? $message : null;
    }

    /**
     * @return null|string
     */
    public function getTransactionReference()
    {
        return $this->data->operation ? (string)$this->data->operation->attributes()->wmtransid : null;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->data->operation ? (string)$this->data->operation->purpose : null;
    }

    /**
     * @return null|string
     */
    public function getAmount()
    {
        return $this->data->operation ? (string)$this->data->operation->amount : null;
    }

    /**
     * @return null|string
     */
    public function getClientIp()
    {
        return $this->data->operation ? (string)$this->data->operation->IPAddress : null;
    }
}
