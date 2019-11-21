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
    public function metro($options = [])
    {
        if (empty($options['metro']) && empty($this->metro)) {
            throw new EmpyrMissingRequiredFields('No metro id given.');
        }

        if (empty($options['metro'])) {
            $options['metro'] = $this->metro;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['metro']);

        return $this->callAPI('metros/'.$options['metro']);
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
        return $this->callAPI('metros/', $options);
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

        return $this->callAPI('metros/'.$options['metro'].'/summary');
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

        return $this->callAPI('metros/'.$options['metro'].'/topBusinesses');
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

        return $this->callAPI('metros/'.$options['metro'].'/topUsers');
    }
}
