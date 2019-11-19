<?php

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;
use Linusx\Empyr\Exceptions\EmpyrNotPartnerCredentials;

class EmpyrSubscription extends EmpyrController
{
    /**
     * Empyr Subscription Methods.
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
     * Retrieves a subscription
     *
     * https://www.mogl.com/api/docs/v2/Subscriptions/get
     *
     * Options:
     * subscription	The id of the subscription to retrieve.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function get($options = [])
    {

        if (empty($options['subscription']) && empty( $this->subscription)) {
            throw new EmpyrMissingRequiredFields('No subscription id given.');
        }

        if (empty( $options['subscription'])) {
            $options['subscription'] = $this->subscription;
        }

        $subscription_id = $options['subscription'];

        $data = $this->call_api('subscriptions/' . $subscription_id);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Adds a subscription to a business.
     *
     * https://www.mogl.com/api/docs/v2/Subscriptions/add
     *
     * Options:
     * business    required The business to add the subscription to.
     * plan    required The plan template that is being used to create the subscription.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     * @throws EmpyrNotPartnerCredentials
     */
    public function add($options = [])
    {
        if (! isset($this->partner) || false === $this->partner) {
            throw new EmpyrNotPartnerCredentials('This call needs to be used with partner credentials.');
        }

        if (empty($options['business']) || empty($options['plan'])) {
            throw new EmpyrMissingRequiredFields('No required information.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, [
            'business',
            'plan',
        ]);

        $data = $this->call_api('subscriptions/add', $options);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }
}
