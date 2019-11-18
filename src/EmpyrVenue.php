<?php /** @noinspection DuplicatedCode */

namespace Linusx\Empyr;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\File;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

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
     * https://www.mogl.com/api/docs/v2/Venues/get
     *
     * @param int $venue_id
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function get($venue_id = 0)
    {
        if (empty($venue_id) && empty($this->venue)) {
            throw new EmpyrMissingRequiredFields('Missing venue id');
        }

        if (! empty($venue_id)) {
            $this->venue = $venue_id;
        }

        $data = $this->call_api('venues/'.$this->venue);

        return $data->response->business ?? false;
    }

    /**
     * Searches the venue directory for active venues with the given parameters.
     *
     * https://www.mogl.com/api/docs/v2/Venues/search
     *
     * Options:
     * query	A user entered query to search venues by.
     * queryLocation	A user entered location to search venues by (could be a city/state or zip).
     * city	The name of a city to search in. Not required if lat and long are provided.
     * state	The two letter abbreviation of the state. Required if city is provided.
     * lat	Specific latitude to search within. If specified then requires long and distance.
     * long	Specific longitude to search within. If specified then requires lat and distance.
     * page	Page number of results.
     * numResults	Number of results to return per page.
     * checkBookmarks	Whether to check whether the results are in bookmarks (and mark them)
     * checkLinks	Whether to check whether the offers have links to the current user.
     * featured	Whether to only return featured businesses.
     * sort	The sort order for the business.
     * cities	multi A list of cities to restrict the results to. (Use facet filtering).
     * distance	A distance filter to restrict results to.
     * attributes	multi A list of attributes to restrict the results to. (Use facet filtering).
     * categories	multi A list of categories to restrict the results to. (Use facet filtering).
     * features	multi A list of features to restrict the results to. (Use facet filtering).
     * ambiances	multi A list of ambiances to restrict the results to. (Use facet filtering).
     * serves	multi A list of serves to restrict the results to. (Use facet filtering).
     * priceRanges	multi A list of priceranges to restrict the results to. (Use facet filtering).
     * bestNights	multi A list of best nights to restrict the results to. (Use facet filtering).
     * minRating	Minimum rating for the business.
     * facet	Return a facet map for filtering results. Defaults to false for better performance.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function search($options = [])
    {
        $data = $this->call_api('venues/search', $options, 'post');

        return $data->response->results ?? false;
    }

    /**
     * Searches the venue directory for venues by phone.
     *
     * https://www.mogl.com/api/docs/v2/Venues/searchByPhone
     *
     * @param string $phone_number
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function searchByPhone($phone_number)
    {
        $data = $this->call_api('venues/search/phone', ['phoneNumber' => $phone_number], 'post');

        return $data->response->results ?? false;
    }

    /**
     * Returns all the businesses that require segmentation data.
     *
     * https://www.mogl.com/api/docs/v2/Venues/segmented
     *
     * @return bool|mixed
     * @throws GuzzleException
     */
    public function segmented()
    {
        $data = $this->call_api('venues/segmented', [], 'post');

        return $data->response->results ?? false;
    }

    /**
     * Retrieves the summary of information and data about a business for a given number of months.
     *
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

        $data = $this->call_api('venues/'.$this->venue.'/summary', $options);

        return $data->response->results ?? false;
    }

    /**
     * Returns user venue totals for the given month. Can be used to build leaderboards.
     *
     * https://www.mogl.com/api/docs/v2/Venues/userVenueTotals
     *
     * Options:
     * user	The id of the specific user we want to find totals for.
     * date	The month that we want to look at totals for. If not provided then will be the most current.
     * page	Page number of results.
     * numResults	Number of results to return per page.
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

        $data = $this->call_api('venues/'.$this->venue.'/userVenueTotals', $options);

        return $data->response->results ?? false;
    }

    /**
     * Creates a new business in the business directory.
     *
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

        $data = $this->call_api('venues/add', $params, 'post');

        return $data->response->venue ?? $data->response;
    }

    /**
     * Add a new business image.
     *
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

        return $data_response->response->venue;
    }

    /**
     * Bookmarks the business for the authenticated user.
     *
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

        $data = $this->call_user_api('venues/'.$this->venue.'/bookmark', $options, 'post');

        return $data->response->item ?? false;
    }

    /**
     * Removes the venue bookmark for the authenticated user.
     *
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

        $data = $this->call_user_api('venues/'.$this->venue.'/removeBookmark', $options, 'post');

        return (bool) $data->response->result;
    }

    /**
     * Removes the venue bookmark for the authenticated user.
     *
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

        $data = $this->call_api('venues/images/'.$this->venue.'/removePhoto', $options, 'post');

        return (bool) $data->response->result;
    }

    /**
     * Updates a business in the business directory.
     *
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

        $data = $this->call_api('venues/'.$this->venue.'/update', $params, 'post');

        return $data->response->venue;
    }
}
