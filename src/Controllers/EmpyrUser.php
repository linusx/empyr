<?php
/**
 * User Controller.
 */

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\File;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

/**
 * Class EmpyrUser.
 */
class EmpyrUser extends EmpyrController
{
    /**
     * Create new Empyr user.
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
     * Lookup user by email.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/lookup
     *
     * @param $email
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function lookup($email = '')
    {
        if (empty($email) && ! empty($this->email)) {
            $email = $this->email;
        } elseif (empty($email) && empty($this->email)) {
            throw new EmpyrMissingRequiredFields('Missing email address');
        }

        return $this->callAPI('users/lookup', ['email' => $email]);
    }

    /**
     * Lookup user by ID.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/get
     *
     * @param int $id User ID to get
     * @return EmpyrUser
     * @throws GuzzleException
     */
    public function user($id)
    {
        return $this->callAPI('users/'.$id);
    }

    /**
     * Get alerts for a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/alerts
     *
     * Needs acting user token.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function alerts($options = [])
    {
        return $this->callUserAPI('users/alerts', $options);
    }

    /**
     * Get donation list for a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/donateList
     *
     * Needs acting user token.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function donations($options = [])
    {
        return $this->callUserAPI('users/donate/donateList', $options);
    }

    /**
     * Get donation list for a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/forgotPassword
     *
     * @return EmpyrUser
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function forgotPassword()
    {

        // Make sure we have an email address.
        if (empty($this->email)) {
            throw new EmpyrMissingRequiredFields('Missing user email address');
        }

        return $this->callUserAPI('users/forgotPassword', ['email' => $this->email], 'post');
    }

    /**
     * Get donation list for a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/listOAuth
     *
     * Needs acting user token.
     *
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function listOAuth()
    {
        return $this->callUserAPI('users/listOAuth');
    }

    /**
     * Retrieves the notification settings for a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/notificationSettings
     *
     * Needs acting user token.
     *
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function notificationSettings()
    {
        return $this->callUserAPI('users/notificationSettings');
    }

    /**
     * Retrieves a list of payments for the given user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/payments
     *
     * Needs acting user token.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function payments($options = [])
    {
        return $this->callUserAPI('users/payments', $options);
    }

    /**
     * Returns a list of cash rewards.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/rewardList
     *
     * Needs acting user token.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function rewardList($options = [])
    {
        return $this->callUserAPI('users/admin/rewardList', $options);
    }

    /**
     * Will search for users based on their first and last name.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/search
     *
     * It seems that search doesn't actually work properly.
     * If no query is given, it returns all users. If query is
     * given, even if I know the user first name and last name are correct,
     * it doesn't return anything.
     *
     * @param string $query
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     */
    public function search($query = '', $options = [])
    {
        $options['query'] = $query;

        return $this->callAPI('users/search', $options);
    }

    /**
     * Signs up a user with an associated card.
     * Note that if the user already exists then the
     * card will be added to the account.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/signupWithCard
     *
     * @todo Still needs work.
     *
     * @return EmpyrUser
     */
    public function signupWithCard()
    {
        return false;
    }

    /**
     * Retrieves a list of friends for the user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/friends
     *
     * Needs acting user token.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function friendsList($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('User not found.');
        }

        return $this->callUserAPI('users/friends/'.$this->user->id.'/', $options);
    }

    /**
     * Returns a list of fundraiser user totals.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/fundraiserHistory
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function fundraiserHistory($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('User not found.');
        }

        return $this->callUserAPI('users/'.$this->user->id.'/fundraiserHistory', $options);
    }

    /**
     * Retrieves a leaderboard for the given user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/leaderboard
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function leaderboard($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('User not found.');
        }

        return $this->callAPI('users/friends/'.$this->user->id.'/leaderboard', $options);
    }

    /**
     * Will retrieve a list of user recommendations or alternatively
     * if businesses are provided it will check if one of the
     * provided businesses is in the recommendations list.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/recommendations
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function recommendations($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('User not found.');
        }

        if (empty($options['businesses'])) {
            throw new EmpyrMissingRequiredFields('No business given.');
        }

        return $this->callAPI('users/'.$this->user->id.'/recommendations', $options);
    }

    /**
     * Retrieves the most recent summary of information and data about a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/summary
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function summary($options = [])
    {
        if ( empty( $this->user ) ) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        return $this->callAPI('users/' . $this->user->id . '/summary', $options);
    }

    /**
     * Retrieves a list of transactions for the given user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/transactions
     *
     * ### Options:
     * * offset    Start offset.
     * * numResults    Number of results to retrieve (max 100).
     * * startDate    Retrieve results after this date YYYY/MM/DD.
     * * endDate    Retrieve results before this date YYYY/MM/DD.
     * * business    Restrict response to just this business.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function transactions($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        return $this->callAPI('users/'.$this->user->id.'/transactions', $options);
    }

    /**
     * Retrieves the most recent summary of information and data about a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/venueHistory
     *
     * @options
     * * curCashbackOnly    Include only venues where there has been cashback this month.
     * * jackpotsOnly    Whether to only return the history where the user won the jackpot.
     * * offset    The offset into the results list.
     * * numResults    Number of results to return per page.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function venueHistory($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        return $this->callAPI('users/'.$this->user->id.'/venueHistory', $options);
    }

    /**
     * Retrieves the most recent summary of information and data about a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/addReward
     *
     * @options
     * * curCashbackOnly    Include only venues where there has been cashback this month.
     * * jackpotsOnly    Whether to only return the history where the user won the jackpot.
     * * offset    The offset into the results list.
     * * numResults    Number of results to return per page.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function addReward($options = [])
    {
        if (empty($options['amount'])) {
            throw new EmpyrMissingRequiredFields('No amount given.');
        }

        return $this->callUserAPI('users/admin/addReward', $options, 'post');
    }

    /**
     * Decrements the number of alerts for the logged in user by the specified amount.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/alertsDismiss
     *
     * @options
     * * numAlerts    Number of alerts to dismiss or empty to dismiss all.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function alertsDismiss($options = [])
    {
        return $this->callUserAPI('users/alertsDismiss', $options, 'post');
    }

    /**
     * Approve a friendship request.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/approve
     *
     * @options
     * * user    The user id of the user to approve friendship of.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function friendApprove($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        if (empty($options['user'])) {
            throw new EmpyrMissingRequiredFields('No friend given.');
        }

        $user = $options['user'];
        unset($options['user']);

        return $this->callUserAPI('users/friends/'.$user.'/approve', $options, 'post');
    }

    /**
     * Deny/Unfriend a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/deny
     *
     * @options
     * * user    The user id of the user to deny friendship of.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function friendDeny($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        if (empty($options['user'])) {
            throw new EmpyrMissingRequiredFields('No friend ID given.');
        }

        $user = $options['user'];
        unset($options['user']);

        return $this->callUserAPI('users/friends/'.$user.'/deny', $options, 'post');
    }

    /**
     * Performs a donation on behalf of the acting user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/donate
     *
     * @options
     * * donationValue  **required** The amount of the donation.
     * transaction      The transaction to lookup a donation for.
     * jackpot          The business user total of a jackpot to donate against.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function donate($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        if (empty($options['user'])) {
            throw new EmpyrMissingRequiredFields('No friend ID given.');
        }

        return $this->callUserAPI('users/donate', $options, 'post');
    }

    /**
     * Will invite users on behalf of the current user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/invite
     *
     * @options
     * * invitees    **required** A csv of emails, phone numbers, and facebook ids (prefixed fb) to invite.
     * * message    A message to customize the invite. Note that this will only be applicable to emails.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     *
     * @todo Get the CSV file working properly.
     */
    public function invite($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        if (empty($options['invitees'])) {
            throw new EmpyrMissingRequiredFields('No friend ID given.');
        }

        return $this->callUserAPI('users/invite', $options, 'post');
    }

    /**
     * Link a business and a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/link
     *
     * @options
     * * offer    **required** The offer to activate/link to the user.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     *
     * @todo Test this. Find an offer to link.
     */
    public function offerLink($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        if (empty($options['offer'])) {
            throw new EmpyrMissingRequiredFields('No offer given.');
        }

        return $this->callUserAPI('users/offers/link', $options, 'post');
    }

    /**
     * Get a list of links for a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/linksList
     *
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function offerLinksList($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        return $this->callUserAPI('users/offers/linksList', $options, 'post');
    }

    /**
     * Links/stores OAuth credentials for a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/oauthLink
     *
     * @options
     * * provider    **required** The provider that we are storing credentials for [TWITTER, FACEBOOK, GOOGLE].
     * * ss    **required** The secret token for the provider authenticating this user.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function oauthLink($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        return $this->callUserAPI('users/oauthLink', $options, 'post');
    }

    /**
     * UnLinks OAuth credentials for a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/oauthUnlink
     *
     * @options
     * * provider    **required** The provider that we are storing credentials for [TWITTER, FACEBOOK, GOOGLE].
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function oauthUnlink($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        return $this->callUserAPI('users/oauthUnlink', $options, 'post');
    }

    /**
     * Request a friendship with the provided user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/request
     *
     * @options
     * * provider    **required** The provider that we are storing credentials for [TWITTER, FACEBOOK, GOOGLE].
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function friendRequest($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        if (empty($options['user'])) {
            throw new EmpyrMissingRequiredFields('No friend ID given.');
        }

        $user = $options['user'];
        unset($options['user']);

        return $this->callUserAPI('users/friends/'.$user.'/request', $options, 'post');
    }

    /**
     * Unlink a previously linked offer.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/unlink
     *
     * @options
     * * offer    **required** The offer to unlink from the user.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function offerUnlink($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        return $this->callUserAPI('users/offers/unlink', $options, 'post');
    }

    /**
     * Signs up a user.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/add
     *
     * @options
     * * firstName    **required** The user's first name.
     * * lastName    **required** The user's last name.
     * * address.postalCode    **required** The user's postal code.
     * * password    **required** A password for the user.
     * * email    **required** The email address for the user.
     * * userWhoInvited    The referral code the user should be signed up under.
     * * donatePercent    The default amount to donate per transaction.
     * * provider    A social provider to link with the account.
     * * ss    The secret token from the social provider that accesses the account.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function add($options = [])
    {
        $defaults = [];
        $defaults['firstName'] = '';
        $defaults['lastName'] = '';
        $defaults['address.postalCode'] = '';
        $defaults['password'] = '';
        $defaults['email'] = '';
        $defaults['userWhoInvited'] = '';
        $defaults['donatePercent'] = '';
        $defaults['provider'] = '';
        $defaults['ss'] = '';

        $params = collect($defaults)->merge($options)->reject(function ($value) {
            if (empty($value)) {
                return true;
            }

            return $value;
        });

        if (
            empty($params['firstName']) ||
            empty($params['lastName']) ||
            empty($params['address.postalCode']) ||
            empty($params['password']) ||
            empty($params['email'])
        ) {
            throw new EmpyrMissingRequiredFields('Missing required fields');
        }

        return $this->callAPI('users', $params, 'post');
    }

    /**
     * Updates a user with the provided settings.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/update
     *
     * @options
     * * offer    **required** The offer to unlink from the user.
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function update($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        $defaults = [];
        $defaults['firstName'] = '';
        $defaults['lastName'] = '';
        $defaults['address.postalCode'] = '';
        $defaults['privacyLevel'] = '';
        $defaults['thumbnailUrl'] = '';
        $defaults['donatePercent'] = '';
        $defaults['userEmailPreference.receiveFriendRequest'] = '';
        $defaults['userEmailPreference.receiveKudo'] = '';
        $defaults['userEmailPreference.becomeMaven'] = '';
        $defaults['userEmailPreference.newTrustLevel'] = '';
        $defaults['userEmailPreference.claimBusiness'] = '';
        $defaults['userEmailPreference.myBusinessReviewed'] = '';
        $defaults['userEmailPreference.receiveNewsletter'] = '';
        $defaults['userEmailPreference.cashbackPurchases'] = '';
        $defaults['userEmailPreference.cashbackJackpot'] = '';
        $defaults['userEmailPreference.cashbackFriends'] = '';
        $defaults['userEmailPreference.rankChange'] = '';
        $defaults['userEmailPreference.vipRewards'] = '';
        $defaults['userEmailPreference.fwbSignup'] = '';
        $defaults['userEmailPreference.weeklyStats'] = '';
        $defaults['userEmailPreference.feedbackRequest'] = '';
        $defaults['userEmailPreference.newVenues'] = '';
        $defaults['userEmailPreference.bizUserOffers'] = '';
        $defaults['userEmailPreference.pushNotifyCashback'] = '';
        $defaults['userEmailPreference.pushNotifyJackpotSlip'] = '';
        $defaults['userEmailPreference.pushNotifyNewVenues'] = '';

        $params = collect($defaults)->merge($options)->reject(function ($value) {
            if (empty($value)) {
                return true;
            }

            return $value;
        });

        return $this->callUserAPI('users/update', $params, 'post');
    }

    /**
     * Update profile picture.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/updatePhoto
     *
     * @param string $file_path File path to send.
     *
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function updatePhoto($file_path)
    {
        // Make sure we have an email address.
        if (empty($this->email)) {
            throw new EmpyrMissingRequiredFields('Missing user email address');
        }

        $options['user_token'] = $this->email;

        $url = $this->generateURL('users/updatePhoto', $options);

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

        return $this->setData($data_response->response->user);
    }

    /**
     * Updates a user's secure settings requiring a password.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Users/updateSecure
     *
     * @options
     * * password        **required** The user's current password
     * * newPassword    The user's new password
     * * email        The user's new email
     *
     * @param array $options
     * @return EmpyrUser
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function updatePassword($options = [])
    {
        if (empty($this->user)) {
            throw new EmpyrMissingRequiredFields('No user found.');
        }

        return $this->callUserAPI('users/secure', $options, 'post');
    }
}
