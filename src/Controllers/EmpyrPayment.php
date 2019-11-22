<?php
/**
 * Payment Controller.
 */

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

/**
 * Class EmpyrPayment.
 */
class EmpyrPayment extends EmpyrController
{
    /**
     * Empyr Payment Methods.
     *
     * @param array $data Data to set field with.
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * Retrieves a payment.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Payments/get
     *
     * @options
     * * payable	**required** The payable id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function payment($options = [])
    {
        if (empty($options['payable']) && empty($this->payable)) {
            throw new EmpyrMissingRequiredFields('No payable id given.');
        }

        if (empty($options['payable'])) {
            $options['payable'] = $this->payable;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['payable']);

        return $this->callAPI('payments/'.$options['payable']);
    }

    /**
     * Creates a payable on the given card as a refund.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Payments/add
     *
     * @options
     * * billingDetail	**required** The card to process the payment to.
     * * amount	**required** The amount of the independent refund.
     * * details	**required** Any notes to associate with the payment.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function add($options = [])
    {
        if (
            empty($options['billingDetail']) ||
            empty($options['amount']) ||
            empty($options['details'])
        ) {
            throw new EmpyrMissingRequiredFields('Missing required fields.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, [
            'billingDetail',
            'amount',
            'details',
        ]);

        return $this->callAPI('payments/', $options, 'post');
    }

    /**
     * Creates a payable on given a PAN and expiration date.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Payments/addDirect
     *
     * @options
     * * card.cardNumber	**required** The card PAN to process the payment to.
     * * card.expirationMonth	**required** The card to process the payment to.
     * * card.expirationYear	**required** The card to process the payment to.
     * * amount	**required** The amount of the independent refund.
     * * details	**required** Any notes to associate with the payment.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function addDirect($options = [])
    {
        if (
            empty($options['card.cardNumber']) ||
            empty($options['card.expirationMonth']) ||
            empty($options['card.expirationYear']) ||
            empty($options['amount']) ||
            empty($options['details'])
        ) {
            throw new EmpyrMissingRequiredFields('Missing required fields.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, [
            'card.cardNumber',
            'card.expirationMonth',
            'card.expirationYear',
            'amount',
            'details',
        ]);

        return $this->callAPI('payments/direct', $options, 'post');
    }
}
