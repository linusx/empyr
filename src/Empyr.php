<?php

namespace Linusx\Empyr;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

class Empyr
{
    /**
     * Data passed to the controller.
     * @var array
     */
    private $data = [];

    /**
     * Empyr constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Return new Utility Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrUtility
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function utility($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrUtility($data);
    }

    /**
     * Return new Subscription Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrSubscription
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function subscription($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrSubscription($data);
    }

    /**
     * Return new Report Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrReport
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function report($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrReport($data);
    }

    /**
     * Return new Offer Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrOffer
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function offer($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrOffer($data);
    }

    /**
     * Return new Fundraiser Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrFundraiser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function fundraiser($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrFundraiser($data);
    }

    /**
     * Return new Metro Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrMetro
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function metro($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrMetro($data);
    }

    /**
     * Return new Invoice Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrInvoice
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     * @throws Exceptions\EmpyrNotPartnerCredentials
     */
    public function invoice($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrInvoice($data);
    }

    /**
     * Return new Devices Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrDevice
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function device($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrDevice($data);
    }

    /**
     * Return new BillingAccount Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrBillingAccounts
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function billingAccount($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrBillingAccounts($data);
    }

    /**
     * Return new Cards Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrCard
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function card($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrCard($data);
    }

    /**
     * Return new Venue Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrVenue
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function venue($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrVenue($data);
    }

    /**
     * Return new User Controller.
     *
     * @param array $data
     * @return Controllers\EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function user($data = [])
    {
        if (isset($this->data) && is_array($this->data)) {
            $data = array_merge($this->data, $data);
        }

        return new Controllers\EmpyrUser($data);
    }
}
