<?php

namespace Linusx\Empyr;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Linusx\Empyr\Exceptions\EmpyrEmptyEmailException;
use Linusx\Empyr\Exceptions\EmpyrUserNotFoundException;

class Empyr
{

    /**
     * Email address to use for User Auth calls.
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var array
     */
    protected $user;

    /**
     * Empyr API URL
     *
     * @var string
     */
    protected $base_url;

    /**
     * Empyr client id
     *
     * @var string
     */
    protected $partner_client_id;

    /**
     * Empyr partner client secret
     *
     * @var string
     */
    protected $partner_client_secret;

    /**
     * Empyr client id
     *
     * @var string
     */
    protected $client_id;

    /**
     * Empyr client secret
     *
     * @var string
     */
    protected $client_secret;

    /**
     * Guzzle Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Guzzle client options
     *
     * @var array
     */
    protected $guzzle_options = [];

    /**
     * Empyr Access Token
     *
     * @var
     */
    private $access_token;

    /**
     * Whether or not to use partner credentials.
     *
     * @var bool
     */
    private $partner = false;

    /**
     * Key for the session token.
     *
     * @var string
     */
    private $token_session_key = 'empyr_token';

    /**
     * Empyr constructor
     *
     * @param bool $partner
     * @param array $data
     * @param bool $acting_user
     *
     * @throws GuzzleException
     * @throws EmpyrEmptyEmailException
     */
    public function __construct( $partner = false, $data = [] ) {

        $this->base_url = config( 'empyr.api_base_url' );
        $this->client_id = config( 'empyr.client_id' );
        $this->client_secret = config( 'empyr.client_secret' );
        $this->partner_client_id = config( 'empyr.partner_client_id' );
        $this->partner_client_secret = config( 'empyr.partner_client_secret' );
        $this->partner = $partner;
        $this->token_session_key = $partner ? $this->token_session_key . '_partner' : $this->token_session_key;

        if ( ! is_array( $data ) && ! empty( $data ) ) {
            $this->email = $data;
            $this->user = $this->user()->lookup( $this->email );
        } elseif ( ! empty( $data['email'] ) ) {
            $this->email = $data['email'];
            $this->user = $this->user()->lookup( $this->email );
        }

        if (
            empty( $this->client_id ) ||
            empty( $this->client_secret ) ||
            empty( $this->partner_client_id ) ||
            empty( $this->partner_client_secret )
        ) {
            throw new \Exception('Empyr: Missing configuration variables.');
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
     * Return new Venue Controller
     *
     * @param integer $venue_id
     * @param string $email
     * @return EmpyrVenue
     * @throws GuzzleException
     */
    public function Venue( $venue_id = 0, $email = '' ) {
        return new EmpyrVenue( $venue_id, $email );
    }

    /**
     * Return new User Controller
     *
     * @param array $data
     * @return EmpyrUser
     * @throws GuzzleException
     */
    public function User( $data = [] ) {
        return new EmpyrUser( $data );
    }

    /**
     * Get access token from Empyr.
     *
     * @param string $grant_type What type of grant (default: client_usertoken).
     * @param string $user_email User email for user token.
     *
     * @throws GuzzleException
     * @return array
     */
    public function getAccessToken( $grant_type = 'client_credentials', $user_email = '' ) {

        if ( ! empty( $user_email ) ) {
            $this->token_session_key = $this->token_session_key . '_' . \Str::slug( $user_email );
        }

        $token_expire = session()->get( $this->token_session_key . '_expires' );
        $token_arr    = session()->get( $this->token_session_key );

        if (  time() < $token_expire && ( ! empty( $token_arr ) && (int) $token_arr->expires_in > 5 ) ) {
            return $token_arr;
        }

        $params = [
            'client_id'     => true === $this->partner ? $this->partner_client_id : $this->client_id,
            'client_secret' => true === $this->partner ? $this->partner_client_secret : $this->client_secret,
            'grant_type'    => $grant_type
        ];

        if ( ! empty( $user_email ) ) {
            $params['user_token'] = $user_email;
        }

        $url = config( 'empyr.api_token_url' ) . '/oauth/token?' . http_build_query( $params );

        $this->log( __METHOD__ . ' GET request: ' . $url );

        try {
            $response = $this->client->get( $url );
            $status = (int) $response->getStatusCode();
        } catch ( ClientException $e ) {
            $this->log( __METHOD__ . ' Error: ' . $e->getResponse()->getBody()->getContents() );
            return [];
        }

        if ( 200 !== $status ) {
            $this->log( __METHOD__ . ' Error: ' . $status . ' was returned' );
            return [];
        }

        $token_arr = \json_decode( $response->getBody() );

        $expire_date = time() + (int) $token_arr->expires_in;

        session()->put( $this->token_session_key . '_expires', $expire_date );
        session()->put( $this->token_session_key, $token_arr );

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
     * @throws EmpyrEmptyEmailException
     * @throws EmpyrUserNotFoundException
     */
    protected function call_user_api( $url, $options = [], $method = 'get', $file = false ) {

        // Make sure we have an email address.
        if ( empty( $this->email ) ) {
            throw new EmpyrEmptyEmailException('Missing user email address.');
        }

        $options['user_token'] = $this->email;

        $url = $this->generateURL( $url, $options );

        $this->log( strtoupper( $method ) . ' request: ' . $url );

        try {
            if ( 'get' === strtolower( $method ) ) {
                $response = $this->client->get( $url );
            } else {
                $response = $this->client->post( $url );
            }
        } catch ( ClientException $e ) {
            $this->log( $e->getMessage() );
            return false;
        } catch ( ServerException $e ) {
            $this->log( $e->getMessage() );
            return false;
        }

        $data_response = \json_decode( $response->getBody() );

        if ( ! empty( $data_response->meta ) && 200 !== (int) $data_response->meta->code ) {
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
    protected function call_api( $url, $options = [], $method = 'get' ) {

        $url = $this->generateURL( $url, $options );

        $this->log( strtoupper( $method ) . ' request: ' . $url );

        try {
            if ( 'get' === strtolower( $method ) ) {
                $response = $this->client->get( $url );
            } else {
                $response = $this->client->post( $url );
            }
        } catch ( ClientException $e ) {
            $this->log( $e->getMessage() );
            return false;
        } catch ( ServerException $e ) {
            $this->log( $e->getMessage() );
            return false;
        }

        $data_response = \json_decode( $response->getBody() );
        if ( ! empty( $data_response->meta ) && 200 !== (int) $data_response->meta->code ) {
            return false;
        }

        return $data_response;
    }

    /**
     * Log message if debug is turned on.
     *
     * @param string $mesage
     */
    protected function log( $mesage ) {
        if ( true === (bool) config( 'empyr.debug' ) ) {
            \Log::info( $mesage );
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
    protected function generateURL( $url, $extra = [] ) {
        $token_array = $this->getAccessToken();

        if ( isset( $extra['user_token'] ) ) {
            $token_array = $this->getAccessToken( 'client_credentials', $extra['user_token'] );
        }

        $access_token = $token_array->access_token;

        $path_params = [
            'client_id'    => $this->client_id,
            'access_token' => $access_token,
        ];

        $params = collect( $path_params )->merge( $extra );

        $url = $this->base_url . '/' . $url . '?' . http_build_query( $params->all() );

        return $url;
    }
}
