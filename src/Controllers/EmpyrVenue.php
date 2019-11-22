<?php
/**
 * Venue Controller
 */

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\File;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

/**
 * Class EmpyrVenue
 * @package Linusx\Empyr\Controllers
 */
class EmpyrVenue extends EmpyrController
{
    /**
     * Empyr Venue Constructor.
     *
     * @param array $data
     *
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * Returns the venue with the associated id.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/get
     *
     * @param int $venue_id
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function venue($venue_id = 0)
    {
        if (empty($venue_id) && empty($this->venue)) {
            throw new EmpyrMissingRequiredFields('Missing venue id');
        }

        if (! empty($venue_id)) {
            $this->venue = $venue_id;
        }

        return $this->callAPI('venues/'.$this->venue);
    }

    /**
     * Searches the venue directory for active venues with the given parameters.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/search
     *
     * @options
     * * query	A user entered query to search venues by.
     * * queryLocation	A user entered location to search venues by (could be a city/state or zip).
     * * city	The name of a city to search in. Not required if lat and long are provided.
     * * state	The two letter abbreviation of the state. Required if city is provided.
     * * lat	Specific latitude to search within. If specified then requires long and distance.
     * * long	Specific longitude to search within. If specified then requires lat and distance.
     * * page	Page number of results.
     * * numResults	Number of results to return per page.
     * * checkBookmarks	Whether to check whether the results are in bookmarks (and mark them)
     * * checkLinks	Whether to check whether the offers have links to the current user.
     * * featured	Whether to only return featured businesses.
     * * sort	The sort order for the business.
     * * cities	multi A list of cities to restrict the results to. (Use facet filtering).
     * * distance	A distance filter to restrict results to.
     * * attributes	multi A list of attributes to restrict the results to. (Use facet filtering).
     * * categories	multi A list of categories to restrict the results to. (Use facet filtering).
     * * features	multi A list of features to restrict the results to. (Use facet filtering).
     * * ambiances	multi A list of ambiances to restrict the results to. (Use facet filtering).
     * * serves	multi A list of serves to restrict the results to. (Use facet filtering).
     * * priceRanges	multi A list of priceranges to restrict the results to. (Use facet filtering).
     * * bestNights	multi A list of best nights to restrict the results to. (Use facet filtering).
     * * minRating	Minimum rating for the business.
     * * facet	Return a facet map for filtering results. Defaults to false for better performance.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function search($options = [])
    {
        return $this->callAPI('venues/search', $options, 'post');
    }

    /**
     * Searches the venue directory for venues by phone.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/searchByPhone
     *
     * @param string $phone_number
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function searchByPhone($phone_number)
    {
        return $this->callAPI('venues/search/phone', ['phoneNumber' => $phone_number], 'post');
    }

    /**
     * Returns all the businesses that require segmentation data.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/segmented
     *
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function segmented()
    {
        return $this->callAPI('venues/segmented', [], 'post');
    }

    /**
     * Retrieves the summary of information and data about a business for a given number of months.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/summary
     *
     * @param int $venue_id
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function summary($venue_id = 0, $options = [])
    {
        if (empty($venue_id) && empty($this->venue)) {
            throw new EmpyrMissingRequiredFields('Missing venue id');
        }

        if (! empty($venue_id)) {
            $this->venue = $venue_id;
        }

        return $this->callAPI('venues/'.$this->venue.'/summary', $options);
    }

    /**
     * Returns user venue totals for the given month. Can be used to build leaderboards.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/userVenueTotals
     *
     * @options
     * * user	The id of the specific user we want to find totals for.
     * * date	The month that we want to look at totals for. If not provided then will be the most current.
     * * page	Page number of results.
     * * numResults	Number of results to return per page.
     *
     * @param int $venue_id
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function userVenueTotals($venue_id = 0, $options = [])
    {
        if (empty($venue_id) && empty($this->venue)) {
            throw new EmpyrMissingRequiredFields('Missing venue id');
        }

        if (! empty($venue_id)) {
            $this->venue = $venue_id;
        }

        return $this->callAPI('venues/'.$this->venue.'/userVenueTotals', $options);
    }

    /**
     * Creates a new business in the business directory.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/add
     *
     * @param array $options
     * @return bool|mixed
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function add($options = [])
    {
        $defaults = [];
        $defaults['name'] = '';
        $defaults['owner'] = '';
        $defaults['address.streetName'] = '';
        $defaults['address.postalCode'] = '';
        $defaults['description'] = '';
        $defaults['website'] = '';
        $defaults['priceRange'] = '';
        $defaults['menuUrl'] = '';
        $defaults['buzz'] = '';
        $defaults['noiseLevel'] = '';
        $defaults['ambiance'] = '';
        $defaults['knownFor'] = '';
        $defaults['feedbackEmail'] = '';
        $defaults['fullPhone'] = '';
        $defaults['businessToken'] = '';
        $defaults['merchantInfo.merchantId'] = '';
        $defaults['merchantInfo.amexId'] = '';
        $defaults['merchantInfo.acquiringBankName'] = '';
        $defaults['merchantInfo.processorName'] = '';
        $defaults['discount'] = '';
        $defaults['referralPercent'] = '';
        $defaults['activeFeatures'] = '';
        $defaults['bestDays'] = '';
        $defaults['serves'] = '';

        $params = collect($defaults)->merge($options)->reject(function ($value) {
            if (empty($value)) {
                return true;
            }

            return $value;
        });

        if (
            empty($params['name']) ||
            empty($params['owner']) ||
            empty($params['address.streetName']) ||
            empty($params['address.postalCode']) ||
            empty($params['description'])
        ) {
            throw new EmpyrMissingRequiredFields('Missing required fields');
        }

        return $this->callAPI('venues/add', $params, 'post');
    }

    /**
     * Add a new business image.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/addPhoto
     *
     * @param string $file_path File path to send.
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function addPhoto($file_path, $options = [])
    {
        // Make sure we have a venue id.
        if (empty($this->venue)) {
            throw new EmpyrMissingRequiredFields('Missing venue id');
        }

        if (empty($options) || empty($options['type'])) {
            throw new EmpyrMissingRequiredFields('Missing photo type.');
        }

        if (! in_array(strtoupper($options['type']), ['LOGO', 'AMBIANCE', 'FOOD'])) {
            throw new EmpyrMissingRequiredFields('Type has to be LOGO, AMBIANCE, or FOOD');
        }

        $url = $this->generateURL('venues/images/'.$this->venue.'/addPhoto', $options);

        $this->log('POST request: '.$url);

        try {
            $file_content = File::get($file_path);
            $file_name = File::name($file_path);

            $response = $this->client->post($url, [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $file_content,
                        'filename' => $file_name,
                    ],
                ],
            ]);
        } catch (ClientException $e) {
            $this->log($e->getMessage());

            return false;
        } catch (ServerException $e) {
            $this->log($e->getMessage());

            return false;
        }

        $data_response = json_decode($response->getBody());

        if (! empty($data_response->meta) && 200 !== (int) $data_response->meta->code) {
            return false;
        }

        return $this->setData($data_response->response->venue);
    }

    /**
     * Bookmarks the business for the authenticated user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/bookmark
     *
     * @param int $venue_id
     * @param array $options
     * @return bool|mixed
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function bookmark($venue_id = 0, $options = [])
    {
        if (empty($venue_id) && empty($this->venue)) {
            throw new EmpyrMissingRequiredFields('Missing venue id');
        }

        if (! empty($venue_id)) {
            $this->venue = $venue_id;
        }

        return $this->callUserAPI('venues/'.$this->venue.'/bookmark', $options, 'post');
    }

    /**
     * Removes the venue bookmark for the authenticated user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/removeBookmark
     *
     * @param int $venue_id
     * @param array $options
     * @return bool|mixed
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function removeBookmark($venue_id = 0, $options = [])
    {
        if (empty($venue_id) && empty($this->venue)) {
            throw new EmpyrMissingRequiredFields('Missing venue id');
        }

        if (! empty($venue_id)) {
            $this->venue = $venue_id;
        }

        $data = $this->callUserAPI('venues/'.$this->venue.'/removeBookmark', $options, 'post');

        return (bool) $data->response->result;
    }

    /**
     * Removes the venue bookmark for the authenticated user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/removePhoto
     *
     * @todo Not working. Have to figure out what 'media' is.
     *
     * @param int $venue_id
     * @param array $options
     * @return bool|mixed
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function removePhoto($venue_id = 0, $options = [])
    {
        if (empty($venue_id) && empty($this->venue)) {
            throw new EmpyrMissingRequiredFields('Missing venue id');
        }

        if (! empty($venue_id)) {
            $this->venue = $venue_id;
        }

        $data = $this->callAPI('venues/images/'.$this->venue.'/removePhoto', $options, 'post');

        return (bool) $data->response->result;
    }

    /**
     * Updates a business in the business directory.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Venues/update
     *
     * @param array $options
     * @return bool|mixed
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function update($options = [])
    {
        if (empty($this->venue)) {
            throw new EmpyrMissingRequiredFields('Missing venue id');
        }

        $defaults = [];
        $defaults['name'] = '';
        $defaults['owner'] = '';
        $defaults['address.streetName'] = '';
        $defaults['address.postalCode'] = '';
        $defaults['description'] = '';
        $defaults['website'] = '';
        $defaults['priceRange'] = '';
        $defaults['menuUrl'] = '';
        $defaults['buzz'] = '';
        $defaults['noiseLevel'] = '';
        $defaults['ambiance'] = '';
        $defaults['knownFor'] = '';
        $defaults['feedbackEmail'] = '';
        $defaults['fullPhone'] = '';
        $defaults['businessToken'] = '';
        $defaults['merchantInfo.merchantId'] = '';
        $defaults['merchantInfo.amexId'] = '';
        $defaults['merchantInfo.acquiringBankName'] = '';
        $defaults['merchantInfo.processorName'] = '';
        $defaults['discount'] = '';
        $defaults['referralPercent'] = '';
        $defaults['activeFeatures'] = '';
        $defaults['bestDays'] = '';
        $defaults['serves'] = '';

        $params = collect($defaults)->merge($options)->reject(function ($value) {
            if (empty($value)) {
                return true;
            }

            return $value;
        });

        return $this->callAPI('venues/'.$this->venue.'/update', $params, 'post');
    }
}
