<?php

namespace Omnipay\WebMoney\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * WebMoney Purchase Response
 * https://merchant.wmtransfer.com/conf/guide.asp.
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2015 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @var string
     *
     * https://merchant.webmoney.ru/lmi/payment.asp
     * or
     * https://merchant.wmtransfer.com/lmi/payment.asp
     */
    protected $redirect = 'https://merchant.webmoney.ru/lmi/payment.asp';

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        if ($this->lang)
            return $this->redirect . '?lang=' . $this->lang;

        return $this->redirect;
    }

    /**
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * @return mixed
     */
    public function getRedirectData()
    {
        return $this->data;
    }
}
