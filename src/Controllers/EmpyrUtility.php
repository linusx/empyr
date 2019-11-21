<?php

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

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

        return $this->callAPI('utilities/categories/', $options);
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
        return $this->callAPI('utilities/extendedCategories/');
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
        return $this->callAPI('utilities/features/');
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
        return $this->callAPI('utilities/info/');
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

        return $this->callAPI('utilities/locationSuggestions/', $options);
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

        return $this->callAPI('utilities/searchSuggestions/', $options);
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

        return $this->callAPI('utilities/searchSuggestionsMap/', $options);
    }
}
