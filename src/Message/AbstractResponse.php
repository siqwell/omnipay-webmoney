<?php

namespace Omnipay\WebMoney\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 * Abstract Response
 *
 * Add language to request params
 *
 * @see \Omnipay\Common\Message\AbstractResponse
 */
abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{
    /**
     * The data contained in the response.
     *
     * @var mixed
     */
    protected $lang;

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     */
    public function __construct(RequestInterface $request, $data, $lang)
    {
        $this->request = $request;
        $this->data = $data;
        $this->lang = $lang;
    }
}
