<?php

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;
use Linusx\Empyr\Exceptions\EmpyrNotPartnerCredentials;

class EmpyrInvoice extends EmpyrController
{
    /**
     * Empyr Invoices Methods.
     *
     * @param array $data Data to set field with.
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     * @throws EmpyrNotPartnerCredentials
     */
    public function __construct($data = [])
    {
        parent::__construct($data);

        if (! isset($this->partner) || false === $this->partner) {
            throw new EmpyrNotPartnerCredentials('This call needs to be used with partner credentials.');
        }
    }

    /**
     * Allows lookup up of an invoice by id.
     *
     * https://www.mogl.com/api/docs/v2/Invoices/invoice
     *
     * Options:
     * invoice	required The invoice id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function invoice($options = [])
    {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        return $this->callAPI('invoices/'.$options['invoice']);
    }

    /**
     * Allows lookup up of invoices given criteria.
     *
     * https://www.mogl.com/api/docs/v2/Invoices/lookup
     *
     * Options:
     * startDate	Include only invoices after this start date
     * endDate	Include only invoices before this end date
     * business	Include only invoices for the given business
     * account	Include only invoices for the given account
     * state	Include only invoices in the given state [PENDING, POSTED, COMPLETED, FAILED, PAST_DUE]
     * offset	The offset into the results
     * numResults	The number of results to retrieve (default 100)
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function lookup($options = [])
    {

        // Filter options.
        $options = $this->allowedKeys($options, ['startDate', 'endDate', 'business', 'account', 'state', 'offset', 'numResults']);

        return $this->callAPI('invoices/', $options);
    }

    /**
     * Get all adjustments given the invoice.
     *
     * https://www.mogl.com/api/docs/v2/Invoices/adjustments
     *
     * Options:
     * invoice required The invoice id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function adjustments($options = [])
    {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        return $this->callAPI('invoices/'.$options['invoice'].'/adjustments', $options);
    }

    /**
     * Get all transactions run on a given an invoice.
     *
     * https://www.mogl.com/api/docs/v2/Invoices/invoiceTransactions
     *
     * Options:
     * invoice required The invoice id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function invoiceTransactions($options = [])
    {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        return $this->callAPI('invoices/'.$options['invoice'].'/invoiceTransactions', $options);
    }

    /**
     * Get all the transactions that were run to bill this invoice.
     *
     * https://www.mogl.com/api/docs/v2/Invoices/transactions
     *
     * Options:
     * invoice required The invoice id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function transactions($options = [])
    {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        return $this->callAPI('invoices/'.$options['invoice'].'/transactions', $options);
    }

    /**
     * Attempts collection of the invoice.
     *
     * https://www.mogl.com/api/docs/v2/Invoices/collect
     *
     * Options:
     * invoice required The invoice id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function leave($options = [])
    {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        return $this->callUserAPI('invoices/'.$options['invoice'].'/collect', $options, 'post');
    }
}
