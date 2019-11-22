<?php
/**
 * Fundraiser Controller
 */
namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

/**
 * Class EmpyrFundraiser
 * @package Linusx\Empyr\Controllers
 */
class EmpyrFundraiser extends EmpyrController
{
    /**
     * Empyr Fundraisers Methods.
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
     * Returns details of a fundraiser.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Fundraisers/get
     *
     * @options
     * * fundraiser	**required** The fundraiser id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function fundraiser($options = [])
    {
        if (empty($options['fundraiser']) && empty($this->fundraiser)) {
            throw new EmpyrMissingRequiredFields('No fundraiser id given.');
        }

        if (empty($options['fundraiser'])) {
            $options['fundraiser'] = $this->fundraiser;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['fundraiser']);

        return $this->callAPI('fundraisers/'.$options['fundraiser']);
    }

    /**
     * Searches for fundraisers.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Fundraisers/search
     *
     * @options
     * * query	**required** The query to search for fundraisers.
     * * offset	Offset into the results.
     * * numResults	Number of results to return per page.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function search($options = [])
    {
        if (empty($options['query'])) {
            throw new EmpyrMissingRequiredFields('No query given.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['query', 'offset', 'numResults']);

        return $this->callAPI('fundraisers/search', $options, 'post');
    }

    /**
     * Returns a list of donations.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Fundraisers/donations
     *
     * @options
     * * fundraiser **required** The fundraiser id.
     * * offset	Start offset.
     * * numResults	Number of results to retrieve (max 100).
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function donations($options = [])
    {
        if (empty($options['fundraiser']) && empty($this->fundraiser)) {
            throw new EmpyrMissingRequiredFields('No fundraiser id given.');
        }

        if (empty($options['fundraiser'])) {
            $options['fundraiser'] = $this->fundraiser;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['fundraiser', 'offset', 'numResults']);

        return $this->callAPI('fundraisers/'.$options['fundraiser'].'/donations', $options);
    }

    /**
     * Returns earnings/donations summary information for a given fundraiser.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Fundraisers/summary
     *
     * @options
     * * fundraiser **required** The fundraiser id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function summary($options = [])
    {
        if (empty($options['fundraiser']) && empty($this->fundraiser)) {
            throw new EmpyrMissingRequiredFields('No fundraiser id given.');
        }

        if (empty($options['fundraiser'])) {
            $options['fundraiser'] = $this->fundraiser;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['fundraiser']);

        return $this->callAPI('fundraisers/'.$options['fundraiser'].'/summary', $options);
    }

    /**
     * Returns a list of fundraiser user totals.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Fundraisers/userFundraiserTotals
     *
     * @options
     * * fundraiser **required** The fundraiser id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function userFundraiserTotals($options = [])
    {
        if (empty($options['fundraiser']) && empty($this->fundraiser)) {
            throw new EmpyrMissingRequiredFields('No fundraiser id given.');
        }

        if (empty($options['fundraiser'])) {
            $options['fundraiser'] = $this->fundraiser;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['fundraiser']);

        return $this->callAPI('fundraisers/'.$options['fundraiser'].'/userFundraiserTotals', $options);
    }

    /**
     * Will join the logged in user to this fundraiser.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Fundraisers/join
     *
     * @options
     * * fundraiser **required** The fundraiser id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function join($options = [])
    {
        if (empty($options['fundraiser']) && empty($this->fundraiser)) {
            throw new EmpyrMissingRequiredFields('No fundraiser id given.');
        }

        if (empty($options['fundraiser'])) {
            $options['fundraiser'] = $this->fundraiser;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['fundraiser']);

        return $this->callUserAPI('fundraisers/'.$options['fundraiser'].'/join', $options, 'post');
    }

    /**
     * Will cause the currently logged in user to leave a fundraiser.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Fundraisers/leave
     *
     * @options
     * * fundraiser **required** The fundraiser id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function leave($options = [])
    {
        if (empty($options['fundraiser']) && empty($this->fundraiser)) {
            throw new EmpyrMissingRequiredFields('No fundraiser id given.');
        }

        if (empty($options['fundraiser'])) {
            $options['fundraiser'] = $this->fundraiser;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['fundraiser']);

        return $this->callUserAPI('fundraisers/'.$options['fundraiser'].'/leave', $options, 'post');
    }
}
