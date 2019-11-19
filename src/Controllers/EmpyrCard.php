<?php

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

class EmpyrCard extends EmpyrController
{
    /**
     * Empyr Cards Methods.
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
     * Returns details about a specific card resource.
     *
     * https://www.mogl.com/api/docs/v2/Cards/get
     *
     * Options:
     * card	required The card id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function get($options = [])
    {
        if (empty($options['card'])) {
            throw new EmpyrMissingRequiredFields('No card id given.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['card']);

        $data = $this->call_user_api('cards/' . $options['card']);


        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Lists the active user's cards cards.
     *
     * https://www.mogl.com/api/docs/v2/Cards/list
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function list($options = [])
    {
        $data = $this->call_user_api('cards', $options);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Add a card to the user account.
     *
     * https://www.mogl.com/api/docs/v2/Cards/add
     *
     * Options:
     * cardNumber	required The card number without spaces or hyphens.
     * expirationMonth	required A valid expiration month (1 to 12).
     * expirationYear	required A valid expiration year.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function add($options = [])
    {
        $data = $this->call_user_api('cards', $options, 'post');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Removes the specified card from the user account.
     *
     * https://www.mogl.com/api/docs/v2/Cards/delete
     *
     * Options:
     * card	required The card id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function delete($options = [])
    {
        if (empty($options['card'])) {
            throw new EmpyrMissingRequiredFields('No card id given.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['card']);

        $data = $this->call_user_api('cards/' . $options['card'] . '/delete', [], 'post');

        if (! $this->is_error()) {
            return $this->return_success($data->response->result);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Removes the specified card from the user account by card pan.
     *
     * https://www.mogl.com/api/docs/v2/Cards/deleteByNumber
     *
     * Options:
     * cardNumber	required The card.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function deleteByNumber($options = [])
    {
        if (empty($options['cardNumber'])) {
            throw new EmpyrMissingRequiredFields('No card number given.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['cardNumber']);

        $data = $this->call_user_api('cards/deleteByNumber', $options, 'post');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Sets the specified card to the user's primary payment card.
     *
     * https://www.mogl.com/api/docs/v2/Cards/setPrimary
     *
     * Options:
     * card	required The id of the card.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function setPrimary($options = [])
    {
        if (empty($options['card'])) {
            throw new EmpyrMissingRequiredFields('No card id given.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['card']);

        $data = $this->call_user_api('cards/' . $options['card'] . '/setPrimary', $options, 'post');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }
}
