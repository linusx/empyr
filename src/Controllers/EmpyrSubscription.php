<?php
/**
 * Subscription Controller.
 */

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;
use Linusx\Empyr\Exceptions\EmpyrNotPublisherCredentials;

/**
 * Class EmpyrSubscription.
 */
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
     * Retrieves a subscription.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Subscriptions/get
     *
     * @options
     * * subscription	The id of the subscription to retrieve.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function subscription($options = [])
    {
        if (empty($options['subscription']) && empty($this->subscription)) {
            throw new EmpyrMissingRequiredFields('No subscription id given.');
        }

        if (empty($options['subscription'])) {
            $options['subscription'] = $this->subscription;
        }

        $subscription_id = $options['subscription'];

        return $this->callAPI('subscriptions/'.$subscription_id);
    }

    /**
     * Adds a subscription to a business.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Subscriptions/add
     *
     * @options
     * * business    **required** The business to add the subscription to.
     * * plan    **required** The plan template that is being used to create the subscription.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     * @throws EmpyrNotPublisherCredentials
     */
    public function add($options = [])
    {
        if (! isset($this->publisher) || false === $this->publisher) {
            throw new EmpyrNotPublisherCredentials('This call needs to be used with publisher credentials.');
        }

        if (empty($options['business']) || empty($options['plan'])) {
            throw new EmpyrMissingRequiredFields('No required information.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, [
            'business',
            'plan',
        ]);

        return $this->callAPI('subscriptions/add', $options);
    }
}
