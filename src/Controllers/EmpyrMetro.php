<?php

namespace Linusx\Empyr\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;

class EmpyrMetro extends EmpyrController
{
    /**
     * Empyr Metro Methods.
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
     * Returns details for the given metro.
     *
     * https://www.mogl.com/api/docs/v2/Metros/get
     *
     * Options:
     * metro	required The metro id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function get($options = [])
    {
        if (empty($options['metro']) && empty($this->metro)) {
            throw new EmpyrMissingRequiredFields('No metro id given.');
        }

        if (empty($options['metro'])) {
            $options['metro'] = $this->metro;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['metro']);

        $data = $this->call_api('metros/'.$options['metro']);

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Lists all the metros that are currently active.
     *
     * https://www.mogl.com/api/docs/v2/Metros/list
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function list($options = [])
    {
        $data = $this->call_api('metros/', $options);

        if (! $this->is_error()) {
            return $this->return_success($data->response->results);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Retrieves summary information about the metro for the given number of months.
     *
     * https://www.mogl.com/api/docs/v2/Metros/summary
     *
     * Options:
     * metro	required The metro id.
     * offset	The offset to pull months from (e.g. 0 is the current which is the default).
     * numResults	The number of months of summary to pull.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function summary($options = [])
    {
        if (empty($options['metro']) && empty($this->metro)) {
            throw new EmpyrMissingRequiredFields('No metro id given.');
        }

        if (empty($options['metro'])) {
            $options['metro'] = $this->metro;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['metro', 'offset', 'numResults']);

        $data = $this->call_api('metros/'.$options['metro'].'/summary');

        if (! $this->is_error()) {
            return $this->return_success($data->response);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Retrieves business summary information for the top businesses in the metro.
     *
     * https://www.mogl.com/api/docs/v2/Metros/topBusinesses
     *
     * Options:
     * metro	required The metro id.
     * offset	The offset to pull months from (e.g. 0 is the current which is the default).
     * numResults	The number of months of summary to pull.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function topBusinesses($options = [])
    {
        if (empty($options['metro']) && empty($this->metro)) {
            throw new EmpyrMissingRequiredFields('No metro id given.');
        }

        if (empty($options['metro'])) {
            $options['metro'] = $this->metro;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['metro', 'offset', 'numResults']);

        $data = $this->call_api('metros/'.$options['metro'].'/topBusinesses');

        if (! $this->is_error()) {
            return $this->return_success($data->response->results);
        }

        return $this->return_error([], $this->get_error());
    }

    /**
     * Retrieves user summary information for the top users in the metro.
     *
     * https://www.mogl.com/api/docs/v2/Metros/topUsers
     *
     * Options:
     * metro	required The metro id.
     * offset	The offset to pull months from (e.g. 0 is the current which is the default).
     * numResults	The number of months of summary to pull.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function topUsers($options = [])
    {
        if (empty($options['metro']) && empty($this->metro)) {
            throw new EmpyrMissingRequiredFields('No metro id given.');
        }

        if (empty($options['metro'])) {
            $options['metro'] = $this->metro;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['metro', 'offset', 'numResults']);

        $data = $this->call_api('metros/'.$options['metro'].'/topUsers');

        if (! $this->is_error()) {
            return $this->return_success($data->response->results);
        }

        return $this->return_error([], $this->get_error());
    }
}
