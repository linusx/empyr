<?php
/**
 * Main Empyr Controller.
 */

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;
use Linusx\Empyr\Facades\Empyr;

/**
 * Class EmpyrController.
 */
class EmpyrController
{
    /**
     * Returned data.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Email address to use for User Auth calls.
     *
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $user;

    /**
     * Error message.
     *
     * @var string|bool
     */
    private $error = false;

    /**
     * Empyr API URL.
     *
     * @var string
     */
    private $base_url;

    /**
     * Empyr CP client id.
     *
     * @var string
     */
    private $client_id;

    /**
     * Empyr CP client secret.
     *
     * @var string
     */
    private $client_secret;

    /**
     * Empyr publisher client id.
     *
     * @var string
     */
    private $publisher_client_id;

    /**
     * Empyr publisher client secret.
     *
     * @var string
     */
    private $publisher_client_secret;

    /**
     * Guzzle Client.
     *
     * @var Client
     */
    private $client;

    /**
     * Guzzle client options.
     *
     * @var array
     */
    private $guzzle_options = [];

    /**
     * Key for the session token.
     *
     * @var string
     */
    private $token_session_key = 'empyr_token';

    /**
     * Options used in the class.
     * @var array
     */
    private $class_options = [];

    /**
     * Is publisher credentials.
     *
     * @var bool
     */
    protected $publisher = false;

    /**
     * Empyr constructor.
     *
     * @param array $data
     *
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function __construct($data = [])
    {
        // Get config options.
        $this->base_url = config('empyr.api_base_url');
        $this->client_id = config('empyr.cp_client_id');
        $this->client_secret = config('empyr.cp_client_secret');
        $this->publisher_client_id = config('empyr.publisher_client_id');
        $this->publisher_client_secret = config('empyr.publisher_client_secret');

        // Make sure we have the required fields.
        if (
            empty($this->client_id) ||
            empty($this->client_secret) ||
            empty($this->publisher_client_id) ||
            empty($this->publisher_client_secret)
        ) {
            throw new EmpyrMissingRequiredFields('Empyr: Missing configuration variables.');
        }

        $this->class_options = $data;

        // Set class variables from instantiation.
        if (! empty($data)) {
            foreach ($data as $field => $value) {
                $this->{$field} = $value;
            }
        }

        $this->token_session_key = $this->publisher ? $this->token_session_key.'_publisher' : $this->token_session_key;

        if (! empty($this->email)) {
            $this->user = Empyr::user()->lookup($this->email)->array();
        }

        $this->guzzle_options = [
            'headers' => [
                'content-type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'base_uri' => $this->base_url,
            'timeout' => 60,
        ];

        $this->client = new Client($this->guzzle_options);

        $this->getAccessToken();
    }

    /**
     * Set returned data.
     *
     * @param $data
     * @return $this
     */
    protected function setData($data)
    {
        $data = (object) $data;

        /*
         * If this is a response from a call
         * then add the needed error field.
         */
        if (! empty($data->response)) {
            $data->response->error = false;
        }

        if ($this->isError()) {
            $data->error = true;
        }

        $this->data = $data->response ?? $data;

        return $this;
    }

    /**
     * Magic method for setting variables.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * Get access token from Empyr.
     *
     * @param string $grant_type What type of grant (default: client_usertoken).
     * @param string $user_email User email for user token.
     *
     * @return array
     * @throws GuzzleException
     */
    public function getAccessToken($grant_type = 'client_credentials', $user_email = '')
    {
        if (! empty($user_email)) {
            $this->token_session_key = $this->token_session_key.'_'.Str::slug($user_email);
        }

        $token_expire = session()->get($this->token_session_key.'_expires');
        $token_arr = session()->get($this->token_session_key);

        if (time() < $token_expire && (! empty($token_arr) && (int) $token_arr->expires_in > 5)) {
            return $token_arr;
        }

        $params = [
            'client_id' => true === $this->publisher ? $this->publisher_client_id : $this->client_id,
            'client_secret' => true === $this->publisher ? $this->publisher_client_secret : $this->client_secret,
            'grant_type' => $grant_type,
        ];

        if (! empty($user_email)) {
            $params['user_token'] = $user_email;
        }

        $url = config('empyr.api_token_url').'/oauth/token?'.http_build_query($params);

        $this->log(__METHOD__.' GET request: '.$url);

        try {
            $response = $this->client->get($url);
            $status = (int) $response->getStatusCode();
        } catch (ClientException $e) {
            $this->log(__METHOD__.' Error: '.$e->getResponse()->getBody()->getContents());

            return [];
        }

        if (200 !== $status) {
            $this->log(__METHOD__.' Error: '.$status.' was returned');

            return [];
        }

        $token_arr = json_decode($response->getBody());

        $expire_date = time() + (int) $token_arr->expires_in;

        session()->put($this->token_session_key.'_expires', $expire_date);
        session()->put($this->token_session_key, $token_arr);
        session()->save();

        return $token_arr;
    }

    /**
     * Call an Empyr API with a user token.
     *
     * @param string $url URL to call.
     * @param array $options
     * @param string $method Get or Post.
     *
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    protected function callUserAPI($url, $options = [], $method = 'get', $file = false)
    {

        // Make sure we have an email address.
        if (empty($this->email)) {
            throw new EmpyrMissingRequiredFields('Missing user email address');
        }

        $options['user_token'] = $this->email;

        $url = $this->generateURL($url, $options);

        $this->log(strtoupper($method).' request: '.$url);

        try {
            if ('get' === strtolower($method)) {
                $response = $this->client->get($url);
            } else {
                $response = $this->client->post($url);
            }
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents());

            $this->handleEmpyrError($error);
        } catch (ServerException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents());

            $this->handleEmpyrError($error);
        }

        if (! empty($data_response->meta) && 200 !== (int) $data_response->meta->code) {
            $this->setError('Bad request. No error given.');
        }

        if ($this->isError()) {
            $data = ['error' => $this->getError()];
        } else {
            $data = json_decode($response->getBody());
        }

        return $this->setData($data);
    }

    /**
     * Call an Empyr API.
     *
     * @param string $url URL to call.
     * @param array $options
     * @param string $method Get or Post.
     *
     * @return bool|mixed
     * @throws GuzzleException
     */
    protected function callAPI($url, $options = [], $method = 'get')
    {
        $url = $this->generateURL($url, $options);

        $this->log(strtoupper($method).' request: '.$url);

        try {
            if ('get' === strtolower($method)) {
                $response = $this->client->get($url);
            } else {
                $response = $this->client->post($url);
            }
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents());

            $this->handleEmpyrError($error);
        } catch (ServerException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents());

            $this->handleEmpyrError($error);
        }

        if (! empty($response->meta) && 200 !== (int) $response->meta->code) {
            $this->setError('Bad request. No error given.');
        }

        if ($this->isError()) {
            $data = ['error' => $this->getError()];
        } else {
            $data = json_decode($response->getBody());
        }

        return $this->setData($data);
    }

    /**
     * Print class data.
     */
    public function debug() : void
    {
        dd($this->data);
    }

    /**
     * Make the data an array.
     *
     * @param bool $ret
     * @return $this|array
     */
    public function array($ret = false)  : array
    {
        if (true === $ret) {
            return (array) $this->data;
        }

        $this->data = (array) $this->data;

        return $this->data;
    }

    /**
     * Make the data a collection.
     *
     * @param bool $ret
     * @return Collection
     */
    public function collection() : Collection
    {
        return collect($this->data);
    }

    /**
     * Return the data.
     *
     * @return Collection
     */
    public function get() : Collection
    {
        return $this->collection();
    }

    /**
     * Set the error.
     *
     * @param bool|string $msg
     */
    protected function setError($msg): void
    {
        $this->error = $msg;
    }

    /**
     * Did we experience an error.
     *
     * @return bool
     */
    protected function isError(): bool
    {
        return false !== $this->error;
    }

    /**
     * Get error message.
     *
     * @return bool|string
     */
    protected function getError()
    {
        if (false === $this->isError()) {
            return '';
        }

        return $this->error;
    }

    /**
     * Make sure we log the exception from Empyr.
     *
     * @param object $error Error from Empyr ( json_decode )
     * @return bool
     */
    private function handleEmpyrError($error): bool
    {
        $code = 500;
        $message = '';

        if (! empty($error->meta) && ! empty($error->meta->code)) {
            $code = (int) $error->meta->code;
        }

        if (! empty($error->meta) && ! empty($error->meta->error)) {
            $message = $error->meta->error;
        }

        if (! empty($error->meta) && ! empty($error->meta->errorDetails)) {
            foreach ($error->meta->errorDetails as $err) {
                $message .= '('.$err.') ';
            }
        }

        $this->log('Empyr Error: '.$code.' - '.$message);

        $this->setError($message);

        return false;
    }

    /**
     * Log message if debug is turned on.
     *
     * @param string $mesage
     */
    protected function log($mesage): void
    {
        if (true === (bool) config('empyr.debug')) {
            Log::info($mesage);
        }
    }

    /**
     * Generate the URL used for the API call.
     *
     * @param string $url URL to append everything to.
     * @param array $extra Extra query params.
     *
     * @return string
     *
     * @throws GuzzleException
     */
    protected function generateURL($url, $extra = []): string
    {
        $token_array = $this->getAccessToken();

        if (isset($extra['user_token'])) {
            $token_array = $this->getAccessToken('client_credentials', $extra['user_token']);
        }

        $access_token = $token_array->access_token;

        $path_params = [
            'client_id' => true === $this->publisher ? $this->publisher_client_id : $this->client_id,
            'access_token' => $access_token,
        ];

        $params = collect($path_params)->merge($extra);

        $url = $this->base_url.'/'.$url.'?'.http_build_query($params->all());

        return $url;
    }

    /**
     * Removed keys except the ones provided.
     *
     * @param array $arr
     * @param array $keys
     * @return array
     */
    protected function allowedKeys($arr, $keys)
    {
        $saved = [];

        foreach ($keys as $key => $value) {
            if (is_int($key) || is_int($value)) {
                $keysKey = $value;
            } else {
                $keysKey = $key;
            }

            if (isset($arr[$keysKey])) {
                $saved[$keysKey] = $arr[$keysKey];
                if (is_array($value)) {
                    $saved[$keysKey] = allow_keys($saved[$keysKey], $keys[$keysKey]);
                }
            }
        }

        return $saved;
    }
}
