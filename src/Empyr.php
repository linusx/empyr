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
     * Return new Venue Controller.
     *
     * @param array $data
     * @return EmpyrBillingAccounts
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function billingAccount($data = [])
    {
        if ( isset($this->data) && is_array( $this->data)) {
            $data = array_merge( $this->data, $data );
        }
        return new EmpyrBillingAccounts($data);
    }

    /**
     * Return new Venue Controller.
     *
     * @param array $data
     * @return EmpyrVenue
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function Venue($data = [])
    {
        if ( isset($this->data) && is_array( $this->data)) {
            $data = array_merge( $this->data, $data );
        }
        return new EmpyrVenue($data);
    }

    /**
     * Return new User Controller.
     *
     * @param array $data
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function User($data = [])
    {
        if ( isset($this->data) && is_array( $this->data)) {
            $data = array_merge( $this->data, $data );
        }
        return new EmpyrUser($data);
    }
}
