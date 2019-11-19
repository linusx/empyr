<?php

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

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
     * https://www.mogl.com/api/docs/v2/Fundraisers/get
     *
     * Options:
     * fundraiser	required The fundraiser id.
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

        $data = $this->callAPI('fundraisers/'.$options['fundraiser']);

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
    }

    /**
     * Searches for fundraisers.
     *
     * https://www.mogl.com/api/docs/v2/Fundraisers/search
     *
     * Options:
     * query	required The query to search for fundraisers.
     * offset	Offset into the results.
     * numResults	Number of results to return per page.
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

        $data = $this->callAPI('fundraisers/search', $options, 'post');

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
    }

    /**
     * Returns a list of donations.
     *
     * https://www.mogl.com/api/docs/v2/Fundraisers/donations
     *
     * Options:
     * fundraiser required The fundraiser id.
     * offset	Start offset.
     * numResults	Number of results to retrieve (max 100).
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

        $data = $this->callAPI('fundraisers/'.$options['fundraiser'].'/donations', $options);

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
    }

    /**
     * Returns earnings/donations summary information for a given fundraiser.
     *
     * https://www.mogl.com/api/docs/v2/Fundraisers/summary
     *
     * Options:
     * fundraiser required The fundraiser id.
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

        $data = $this->callAPI('fundraisers/'.$options['fundraiser'].'/summary', $options);

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
    }

    /**
     * Returns a list of fundraiser user totals.
     *
     * https://www.mogl.com/api/docs/v2/Fundraisers/userFundraiserTotals
     *
     * Options:
     * fundraiser required The fundraiser id.
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

        $data = $this->callAPI('fundraisers/'.$options['fundraiser'].'/userFundraiserTotals', $options);

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
    }

    /**
     * Will join the logged in user to this fundraiser.
     *
     * https://www.mogl.com/api/docs/v2/Fundraisers/join
     *
     * Options:
     * fundraiser required The fundraiser id.
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

        $data = $this->callUserAPI('fundraisers/'.$options['fundraiser'].'/join', $options, 'post');

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
    }

    /**
     * Will cause the currently logged in user to leave a fundraiser.
     *
     * https://www.mogl.com/api/docs/v2/Fundraisers/leave
     *
     * Options:
     * fundraiser required The fundraiser id.
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

        $data = $this->callUserAPI('fundraisers/'.$options['fundraiser'].'/leave', $options, 'post');

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
    }
}
