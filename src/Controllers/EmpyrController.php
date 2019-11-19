<?php

/** @noinspection ALL */

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;
use Linusx\Empyr\Facades\Empyr;
use stdClass;

class EmpyrController
{
    /**
     * Email address to use for User Auth calls.
     *
     * @var string
     */
    protected $email;

    /**
     * @var array
     */
    protected $user;

    /**
     * Error message.
     *
     * @var string|bool
     */
    protected $error = false;

    /**
     * Empyr API URL.
     *
     * @var string
     */
    protected $base_url;

    /**
     * Empyr client id.
     *
     * @var string
     */
    protected $partner_client_id;

    /**
     * Empyr partner client secret.
     *
     * @var string
     */
    protected $partner_client_secret;

    /**
     * Empyr client id.
     *
     * @var string
     */
    protected $client_id;

    /**
     * Empyr client secret.
     *
     * @var string
     */
    protected $client_secret;

    /**
     * Guzzle Client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Guzzle client options.
     *
     * @var array
     */
    protected $guzzle_options = [];

    /**
     * Key for the session token.
     *
     * @var string
     */
    private $token_session_key = 'empyr_token';

    private $data = [];

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
        $this->client_id = config('empyr.client_id');
        $this->client_secret = config('empyr.client_secret');
        $this->partner_client_id = config('empyr.partner_client_id');
        $this->partner_client_secret = config('empyr.partner_client_secret');

        // Make sure we have the required fields.
        if (
            empty($this->client_id) ||
            empty($this->client_secret) ||
            empty($this->partner_client_id) ||
            empty($this->partner_client_secret)
        ) {
            throw new EmpyrMissingRequiredFields('Empyr: Missing configuration variables.');
        }

        $this->data = $data;

        // Set class variables from instantiation.
        if (! empty($data)) {
            foreach ($data as $field => $value) {
                $this->{$field} = $value;
            }
        }

        $this->token_session_key = $this->partner ? $this->token_session_key.'_partner' : $this->token_session_key;

        if (! empty($this->email)) {
            $this->user = Empyr::user()->lookup($this->email);
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
     * Magic methods for getting variables.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (! isset($this->{$name})) {
            return false;
        }

        return $this->{$name};
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
            'client_id' => true === $this->partner ? $this->partner_client_id : $this->client_id,
            'client_secret' => true === $this->partner ? $this->partner_client_secret : $this->client_secret,
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
    protected function call_user_api($url, $options = [], $method = 'get', $file = false)
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

            return $this->handleEmpyrError($error);
        } catch (ServerException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents());

            return $this->handleEmpyrError($error);
        }

        $data_response = json_decode($response->getBody());
        if (! empty($data_response->meta) && 200 !== (int) $data_response->meta->code) {
            $this->set_error('Bad request. No error given.');

            return false;
        }

        return $data_response;
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
    protected function call_api($url, $options = [], $method = 'get')
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

            return $this->handleEmpyrError($error);
        } catch (ServerException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents());

            return $this->handleEmpyrError($error);
        }

        $data_response = json_decode($response->getBody());
        if (! empty($data_response->meta) && 200 !== (int) $data_response->meta->code) {
            $this->set_error('Bad request. No error given.');

            return false;
        }

        return $data_response;
    }

    /**
     * Set the error.
     *
     * @param bool|string $msg
     */
    protected function set_error($msg): void
    {
        $this->error = $msg;
    }

    /**
     * Did we experience an error.
     *
     * @return bool
     */
    protected function is_error(): bool
    {
        return false !== $this->error;
    }

    /**
     * Get error message.
     *
     * @return bool|string
     */
    protected function get_error()
    {
        if (false === $this->is_error()) {
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

        $this->set_error($message);

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
            'client_id' => true === $this->partner ? $this->partner_client_id : $this->client_id,
            'access_token' => $access_token,
        ];

        $params = collect($path_params)->merge($extra);

        $url = $this->base_url.'/'.$url.'?'.http_build_query($params->all());

        return $url;
    }

    /**
     * Return success data.
     *
     * @param mixed $data
     * @param string $msg
     *
     * @return object
     */
    public function return_success($data, $msg = '')
    {
        $ret = new stdClass();
        $ret->status = true;
        $ret->message = $msg;
        $ret->data = (object) $data;

        return $ret;
    }

    /**
     * Return success data.
     *
     * @param mixed $data
     * @param string $msg
     *
     * @return object
     */
    public function return_error($data, $msg = 'Error')
    {
        $ret = new stdClass();
        $ret->status = false;
        $ret->message = $msg;
        $ret->data = (object) $data;

        return $ret;
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
