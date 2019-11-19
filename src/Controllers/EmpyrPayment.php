<?php

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

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
     * https://www.mogl.com/api/docs/v2/Payments/get
     *
     * Options:
     * payable	required The payable id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function get($options = [])
    {
        if (empty($options['payable']) && empty( $this->payable)) {
            throw new EmpyrMissingRequiredFields('No payable id given.');
        }

        if (empty( $options['payable'])) {
            $options['payable'] = $this->payable;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['payable']);

        $data = $this->call_api('payments/' . $options['payable']);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Creates a payable on the given card as a refund.
     *
     * https://www.mogl.com/api/docs/v2/Payments/add
     *
     * Options:
     * billingDetail	required The card to process the payment to.
     * amount	required The amount of the independent refund.
     * details	required Any notes to associate with the payment.
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

        $data = $this->call_api('payments/', $options, 'post');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Creates a payable on given a PAN and expiration date.
     *
     * https://www.mogl.com/api/docs/v2/Payments/addDirect
     *
     * Options:
     * card.cardNumber	required The card PAN to process the payment to.
     * card.expirationMonth	required The card to process the payment to.
     * card.expirationYear	required The card to process the payment to.
     * amount	required The amount of the independent refund.
     * details	required Any notes to associate with the payment.
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

        $data = $this->call_api('payments/direct', $options, 'post');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }
}
