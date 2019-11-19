<?php

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

class EmpyrDevice extends EmpyrController
{
    /**
     * Empyr Devices Methods.
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
     * Returns a device given it's mogl id.
     *
     * https://www.mogl.com/api/docs/v2/Devices/get
     *
     * Options:
     * device	required The device id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function get($options = [])
    {
        if (empty($options['device'])) {
            throw new EmpyrMissingRequiredFields('No device id given.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['device']);

        $data = $this->call_user_api('devices/' . $options['device']);


        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Lists the user's active devices.
     *
     * https://www.mogl.com/api/docs/v2/Devices/list
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function list($options = [])
    {
        $data = $this->call_user_api('devices/list', $options);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Registers/associates the device with the user.
     *
     * https://www.mogl.com/api/docs/v2/Devices/add
     *
     * Options:
     * deviceToken	required The device token for receiving push notifications.
     * deviceType	required The type of the device [IOS,ANDROID].
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function add($options = [])
    {
        if (empty($options['deviceToken']) || empty($options['deviceType'])) {
            throw new EmpyrMissingRequiredFields('No token or type given.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['deviceToken', 'deviceType']);

        $data = $this->call_user_api('devices/add', $options, 'post');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Removes the device with the specified token from the user.
     *
     * hhttps://www.mogl.com/api/docs/v2/Devices/remove
     *
     * Options:
     * deviceToken	required The device token for receiving push notifications.
     * deviceType	required The type of the device [IOS,ANDROID].
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function delete($options = [])
    {
        if (empty($options['deviceToken']) || empty($options['deviceType'])) {
            throw new EmpyrMissingRequiredFields('No token or type given.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['deviceToken', 'deviceType']);

        $data = $this->call_user_api('devices/remove', $options, 'post');

        if (! $this->is_error()) {
            return $this->return_success($data->response->result);
        }

        return $this->return_error([], $this->get_error());
    }
}
