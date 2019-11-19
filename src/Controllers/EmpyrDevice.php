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
    public function device($options = [])
    {
        if (empty($options['device'])) {
            throw new EmpyrMissingRequiredFields('No device id given.');
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['device']);

        $data = $this->callUserAPI('devices/'.$options['device']);

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
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
        $data = $this->callUserAPI('devices/list', $options);

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
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

        $data = $this->callUserAPI('devices/add', $options, 'post');

        if (! $this->isError()) {
            return $this->returnSuccess($data->response);
        }

        return $this->returnError([], $this->getError());
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

        $data = $this->callUserAPI('devices/remove', $options, 'post');

        if (! $this->isError()) {
            return $this->returnSuccess($data->response->result);
        }

        return $this->returnError([], $this->getError());
    }
}
