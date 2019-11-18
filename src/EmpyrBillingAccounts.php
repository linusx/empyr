<?php

namespace Linusx\Empyr;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;
use Linusx\Empyr\Exceptions\EmpyrNotPartnerCredentials;

class EmpyrBillingAccounts extends EmpyrController
{
    /**
     * Create new Empyr Billing Account.
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
     * Allows lookup up of an individual account by id.
     *
     * https://www.mogl.com/api/docs/v2/BillingAccounts/accounts
     *
     * @param int $id Billing Account ID to get
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrNotPartnerCredentials
     */
    public function get($id)
    {
        if (! isset($this->partner) || false === $this->partner) {
            throw new EmpyrNotPartnerCredentials('This call needs to be used with partner credentials.');
        }

        $data = $this->call_api('billingAccounts/'.$id);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Allows the searching or listing of accounts.
     *
     * https://www.mogl.com/api/docs/v2/BillingAccounts/search
     *
     * Options:
     * query    The query to try to lookup the name with. Mutually exclusive with business. Ignored if business supplied.
     * business    The business to look up by. If provided we look for the account associated with this business. Mutually exclusive with query.
     * offset    Start offset.
     * numResults    Number of results to retrieve (max 100).
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrNotPartnerCredentials
     */
    public function search($options = [])
    {
        if (! isset($this->partner) || false === $this->partner) {
            throw new EmpyrNotPartnerCredentials('This call needs to be used with partner credentials.');
        }

        $data = $this->call_api('billingAccounts/search', $options);

        if (! $this->is_error()) {
            return $this->return_success($data->response->results);
        }

        return $this->return_error('', $this->get_error());
    }

    /**
     * Shows the business links for this account.
     *
     * https://www.mogl.com/api/docs/v2/BillingAccounts/links
     *
     * Options:
     * query    The query to try to lookup the name with. Mutually exclusive with business. Ignored if business supplied.
     * business    The business to look up by. If provided we look for the account associated with this business. Mutually exclusive with query.
     * offset    Start offset.
     * numResults    Number of results to retrieve (max 100).
     *
     * @todo Call to Empyr doesn't seem to be working properly.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function links($options = [])
    {
        if (empty($options['billing_account']) && empty($this->billing_account)) {
            throw new EmpyrMissingRequiredFields('Missing billing account id');
        }

        if (! empty($options['billing_account'])) {
            $this->billing_account = $options['billing_account'];
            unset($options['billing_account']);
        }

        $data = $this->call_api('billingAccounts/'.(int) $this->billing_account.'/links', $options);
        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Allows the adding of accounts.
     *
     * https://www.mogl.com/api/docs/v2/BillingAccounts/add
     *
     * Options:
     * account.name    required The name of the account.
     * account.accountingEmail    required The email address that will receive invoices.
     * account.paymentMethod    required The method of payment [MANUAL|BILLINGDETAIL]
     * card.cardNumber    The card number without spaces or hyphens.
     * card.expirationMonth    A valid expiration month (1 to 12).
     * card.expirationYear    A valid expiration year.
     * echeck.business    Whether the checking account is personal or business.
     * echeck.nameOnAccount    The name on the checking account.
     * echeck.accountType    CHECKING or SAVINGS
     * echeck.accountNumber    The account number.
     * echeck.routingNumber    The bank routing number.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     * @throws EmpyrNotPartnerCredentials
     */
    public function add($options = [])
    {
        $defaults = [];
        $defaults['account.name'] = '';
        $defaults['account.accountingEmail'] = '';
        $defaults['account.paymentMethod'] = '';
        $defaults['address.postalCode'] = '';
        $defaults['card.cardNumber'] = '';
        $defaults['card.expirationMonth'] = '';
        $defaults['card.expirationYear'] = '';
        $defaults['echeck.business'] = '';
        $defaults['echeck.nameOnAccount'] = '';
        $defaults['echeck.accountType'] = '';
        $defaults['echeck.accountNumber'] = '';
        $defaults['echeck.routingNumber'] = '';

        $params = collect($defaults)->merge($options)->reject(function ($value) {
            if (empty($value)) {
                return true;
            }

            return false;
        });

        if (
            empty($params['account.name']) ||
            empty($params['account.accountingEmail']) ||
            empty($params['account.paymentMethod'])
        ) {
            throw new EmpyrMissingRequiredFields('Missing required fields');
        }

        $user_exists = $this->search(['query' => $options['account.name'], 'numResults' => 100]);
        $found = [];
        if (true === $user_exists->status && ! empty($user_exists->data->results)) {
            foreach ($user_exists->data->results as $user) {
                if (
                    strtolower($options['account.name']) === strtolower($user->name) &&
                    strtolower($options['account.accountingEmail']) === strtolower($user->email) &&
                    strtolower($options['account.paymentMethod']) === strtolower($user->paymentMethod)
                ) {
                    $found[] = $user;
                }
            }
        }

        if (! empty($found)) {
            return $this->return_error($found, 'Name, Email Address, and Payment Method  already exists.');
        }

        $data = $this->call_api('billingAccounts/', $options, 'post');
        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error('', 'Error adding new Billing Account');
    }

    /**
     * Allows the linking of accounts with businesses.
     *
     * https://www.mogl.com/api/docs/v2/BillingAccounts/link
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function link($options = [])
    {
        if (empty($options['billing_account']) && empty($this->billing_account)) {
            throw new EmpyrMissingRequiredFields('Missing billing account id');
        }

        if (! empty($options['billing_account'])) {
            $this->billing_account = $options['billing_account'];
            unset($options['billing_account']);
        }

        if (empty($options['business'])) {
            throw new EmpyrMissingRequiredFields('No business given to link to.');
        }

        // Filter options to only allow the business.
        $options = $this->allowedKeys($options, ['business']);

        $data = $this->call_api('billingAccounts/'.(int) $this->billing_account.'/link', $options, 'post');
        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Allows the updating of accounts.
     *
     * https://www.mogl.com/api/docs/v2/BillingAccounts/update
     *
     * Options:
     * account.name	The name of the account.
     * account.accountingEmail	The email address that will receive invoices.
     * account.paymentMethod	The method of payment [MANUAL|BILLINGDETAIL]
     * card.cardNumber	The card number without spaces or hyphens.
     * card.expirationMonth	A valid expiration month (1 to 12).
     * card.expirationYear	A valid expiration year.
     * echeck.business	Whether the checking account is personal or business.
     * echeck.nameOnAccount	The name on the checking account.
     * echeck.accountType	CHECKING or SAVINGS
     * echeck.accountNumber	The account number.
     * echeck.routingNumber	The bank routing number.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function update($options = [])
    {
        if (empty($options['billing_account']) && empty($this->billing_account)) {
            throw new EmpyrMissingRequiredFields('Missing billing account id');
        }

        if (! empty($options['billing_account'])) {
            $this->billing_account = $options['billing_account'];
            unset($options['billing_account']);
        }

        // Filter options to only allow the business.
        $options = $this->allowedKeys($options, [
            'account.name',
            'account.accountingEmail',
            'account.paymentMethod',
            'card.cardNumber',
            'card.expirationMonth',
            'card.expirationYear',
            'echeck.business',
            'echeck.nameOnAccount',
            'echeck.accountType',
            'echeck.accountNumber',
            'echeck.routingNumber',
        ]);

        $data = $this->call_api('billingAccounts/'.(int) $this->billing_account.'/update', $options, 'post');
        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }
}
