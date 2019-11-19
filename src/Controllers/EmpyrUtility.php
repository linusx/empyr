<?php

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;
use Linusx\Empyr\Exceptions\EmpyrNotPartnerCredentials;

class EmpyrUtility extends EmpyrController
{
    /**
     * Empyr Utility Methods.
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
     * Returns a list of MOGL categories.
     *
     * https://www.mogl.com/api/docs/v2/Utilities/categories
     *
     * Options:
     * level	The level of category [MO_TC, MO]
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function categories($options = [])
    {
        // Filter options.
        $options = $this->allowedKeys($options, ['level']);

        $data = $this->call_api('utilities/categories/');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Returns a list of MOGL categories.
     *
     * https://www.mogl.com/api/docs/v2/Utilities/extendedCategories
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function extendedCategories($options = [])
    {
        $data = $this->call_api('utilities/extendedCategories/');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Returns a list of MOGL features.
     *
     * https://www.mogl.com/api/docs/v2/Utilities/features
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function features($options = [])
    {
        $data = $this->call_api('utilities/features/');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Returns information about the current application.
     *
     * https://www.mogl.com/api/docs/v2/Utilities/info
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function info($options = [])
    {
        $data = $this->call_api('utilities/info/');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Returns a list of suggested locations.
     *
     * https://www.mogl.com/api/docs/v2/Utilities/locationSuggestions
     *
     * Options:
     * query    required A search term to lookup some candidate locations.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function locationSuggestions($options = [])
    {
        if (empty($options['query'])) {
            throw new EmpyrMissingRequiredFields('Missing required information.');
        }

        $data = $this->call_api('utilities/locationSuggestions/', $options);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Returns a list of suggested searches.
     *
     * https://www.mogl.com/api/docs/v2/Utilities/searchSuggestions
     *
     * Options:
     * query    required A search term to lookup some candidate locations.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function searchSuggestions($options = [])
    {
        if (empty($options['query'])) {
            throw new EmpyrMissingRequiredFields('Missing required information.');
        }

        $data = $this->call_api('utilities/searchSuggestions/', $options);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Returns a map (business id, business name) of suggested searches.
     *
     * https://www.mogl.com/api/docs/v2/Utilities/searchSuggestionsMap
     *
     * Options:
     * query    required A search term to lookup some candidate locations.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function searchSuggestionsMap($options = [])
    {
        if (empty($options['query'])) {
            throw new EmpyrMissingRequiredFields('Missing required information.');
        }

        $data = $this->call_api('utilities/searchSuggestionsMap/', $options);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }
}
