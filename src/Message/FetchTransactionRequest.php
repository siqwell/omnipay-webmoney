<?php

namespace Omnipay\WebMoney\Message;

/**
 * WebMoney Fetch Transactions Request
 * http://wiki.wmtransfer.com/projects/webmoney/wiki/Interface_X18.
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 */
class FetchTransactionRequest extends AbstractRequest
{
    /**
     * @var string
     */
    protected $endpoint = 'https://merchant.webmoney.ru/conf/xml/XMLTransGet.asp';

    /**
     * @return string
     */
    public function getData()
    {
        $this->validate(
            'webMoneyId',
            'merchantPurse',
            'secretKey',
            'transactionId'
        );

        $document = new \DOMDocument('1.0', 'windows-1251');

        $document->formatOutput = false;

        $request = $document->appendChild(
            $document->createElement('merchant.request')
        );

        $request->appendChild(
            $document->createElement('wmid', $this->getWebMoneyId())
        );

        $request->appendChild(
            $document->createElement('lmi_payee_purse', $this->getMerchantPurse())
        );

        $request->appendChild(
            $document->createElement('lmi_payment_no', $this->getTransactionId())
        );

        $request->appendChild(
            $document->createElement('sha256', $this->calculateSignature('sha256'))
        );

        $request->appendChild(
            $document->createElement('md5', $this->calculateSignature('md5'))
        );

        return $document->saveXML();
    }

    /**
     * @param mixed $data
     *
     * @return FetchTransactionResponse
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->endpoint, null, $data)->send();

        return $this->createResponse($httpResponse->xml());
    }

    /**
     * @param $data
     *
     * @return FetchTransactionResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new FetchTransactionResponse($this, $data);
    }

    /**
     * @param $algorithm
     *
     * @return string
     */
    protected function calculateSignature($algorithm)
    {
        return hash(
            $algorithm,
            $this->getWebMoneyId().
            $this->getMerchantPurse().
            $this->getTransactionId().
            $this->getSecretKey()
        );
    }
}
