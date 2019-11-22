<?php
/**
 * Reports Controller
 */
namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

/**
 * Class EmpyrReport
 * @package Linusx\Empyr\Controllers
 */
class EmpyrReport extends EmpyrController
{
    /**
     * Empyr Report Methods.
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
     * Allows retrieval of various business stats.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Reports/statsLookup
     *
     * @options
     * * startDate	Include stats data starting from this point.
     * * endDate	Include stats data ending from this point.
     * * business	**required** The business to pull the stats report for.
     * * groupingOption	**required** The grouping option to group the report by [DAY_OF_MONTH, MONTH_OF_YEAR].
     * * statsTypes	multi The type of stats to be pulled (implicitly results will be grouped. Options are: [FACEBOOK_POSTS, TWITTER_POSTS, EMAIL_MENTIONS, WEB_SEARCH_VIEWS, MOB_SEARCH_VIEWS, WEB_PROFILE_VIEWS, MOB_PROFILE_VIEWS, LOGIN, EMAIL_EVENT, PUSH_EVENT, SMS_EVENT, KIOSK_HEARTBEAT]
     * * offset	The offset into the results
     * * numResults	The number of results to retrieve (default 100)
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function statsReport($options = [])
    {
        if (empty($options['business'])) {
            throw new EmpyrMissingRequiredFields('Missing required fields.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, [
            'startDate',
            'endDate',
            'business',
            'groupingOption',
            'statsTypes',
            'offset',
            'numResults',
        ]);

        $defaults = [
            'groupingOption' => 'DAY_OF_MONTH',
        ];

        $params = collect($defaults)->merge($options);

        return $this->callAPI('reportstats/statsReport/', $params->all());
    }

    /**
     * Allows retrieval of summaries of transactional data.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Reports/txReportLookup
     *
     * @options
     * * startDate	Include stats data starting from this point.
     * * endDate	Include stats data ending from this point.
     * * byProcessDate	Whether the start and end date are the transaction dates OR the dates that the transactions are processed. Typically, for example, with invoices you would use the process date but for dashbaords would use the transaction date.
     * * business	**required** The business to pull the stats report for.
     * * groupingOption	**required** The grouping option to group the report by [DAY_OF_MONTH, MONTH_OF_YEAR, GENDER, ZIPCODE, PUBLISHER_DATE].
     * * sortingOption	**required** The sorting option to sort the report by [GROUPED_BY_VALUE, CASHBACK, REVENUE, RECENT, MONTH_YEAR].
     * * offset	The offset into the results
     * * numResults	The number of results to retrieve (default 100)
     *
     * @todo Doesn't seem to be working.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function txReport($options = [])
    {
        if (empty($options['business'])) {
            throw new EmpyrMissingRequiredFields('Missing required fields.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, [
            'startDate',
            'endDate',
            'byProcessDate',
            'business',
            'groupingOption',
            'sortingOption',
            'offset',
            'numResults',
        ]);

        $defaults = [
            'groupingOption' => 'MONTH_OF_YEAR',
            'sortingOption' => 'GROUPED_BY_VALUE',
            'byProcessDate' => false,
        ];

        $params = collect($defaults)->merge($options);

        return $this->callAPI('reportstats/txReport/', $params->all());
    }
}
