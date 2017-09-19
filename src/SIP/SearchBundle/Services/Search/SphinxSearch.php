<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\SearchBundle\Services\Search;

class SphinxSearch
{
    /**
     * @var string $host
     */
    private $host;

    /**
     * @var string $port
     */
    private $port;

    /**
     * @var string $socket
     */
    private $socket;

    /**
     * @var array $indexes
     *
     * $this->indexes should have the format:
     *
     *    $this->indexes = array(
     *        'IndexLabel' => array(
     *            'index_name'    => 'IndexName',
     *            'field_weights'    => array(
     *                'FieldName'    => (int)'FieldWeight',
     *                ...,
     *            ),
     *        ),
     *        ...,
     *    );
     *
     */
    private $indexes;

    /**
     * @var \SphinxClient $sphinx
     */
    private $sphinx;

    /**
     * Constructor.
     *
     * @param string $host The server's host name/IP.
     * @param string $port The port that the server is listening on.
     * @param string $socket The UNIX socket that the server is listening on.
     * @param array $indexes The list of indexes that can be used.
     */
    public function __construct($host = 'localhost', $port = '9312', $socket = null, array $indexes = array())
    {
        $this->host = $host;
        $this->port = $port;
        $this->socket = $socket;
        $this->indexes = $indexes;

        $this->sphinx = new \SphinxClient();



        if ($this->socket !== null)
            $this->sphinx->setServer($this->socket);
        else
            $this->sphinx->setServer($this->host, $this->port);
    }

    /**
     * Set the desired match mode.
     *
     * @param int $mode The matching mode to be used.
     */
    public function setMatchMode($mode)
    {
        $this->sphinx->setMatchMode($mode);
    }

    /**
     * Set the desired search filter.
     *
     * @param string $attribute The attribute to filter.
     * @param array $values The values to filter.
     * @param bool $exclude Is this an exclusion filter?
     */
    public function setFilter($attribute, $values, $exclude = false)
    {
        $this->sphinx->setFilter($attribute, $values, $exclude);
    }

    /**
     * Search for the specified query string.
     *
     * @param string $query The query string that we are searching for.
     * @param array $indexes The indexes to perform the search on.
     *
     * @return array The results of the search.
     *
     * $indexes should have the format:
     *
     *    $indexes = array(
     *        'IndexLabel' => array(
     *            'result_offset'    => (int),
     *            'result_limit'    => (int)
     *        ),
     *        ...,
     *    );
     */
    public function search($query, array $indexes)
    {


        $query = $this->sphinx->escapeString($query);

        $results = array();
        foreach ($indexes as $label => $options) {
            /**
             * Ensure that the label corresponds to a defined index.
             */
            if (!isset($this->indexes[$label]))
                continue;

            /**
             * Set the offset and limit for the returned results.
             */
            if (isset($options['result_offset']) && isset($options['result_limit']))
                $this->sphinx->setLimits($options['result_offset'], $options['result_limit']);

            /**
             * Weight the individual fields.
             */
            if (!empty($this->indexes[$label]['field_weights']))
                $this->sphinx->setFieldWeights($this->indexes[$label]['field_weights']);

            // sort mode
            if (!empty($options['sort_mode'])) {
                $this->sphinx->SetSortMode(SPH_SORT_EXTENDED, $options['sort_mode']);
            }
            else {
                $this->sphinx->SetSortMode(SPH_SORT_RELEVANCE);
            }

            if(!empty($options['filters']) && is_array($options['filters'])) {
                foreach($options['filters'] as $filter) {
                    $this->sphinx->SetFilter($filter['attribute'], $filter['values']);
                }
            } else {
                $this->sphinx->ResetFilters();
            }


            /**
             * Perform the query.
             */
            $results[$label] = $this->sphinx->query($query, $this->indexes[$label]['index_name']);
            if ($results[$label]['status'] !== SEARCHD_OK)
                throw new \RuntimeException(sprintf('Searching index "%s" for "%s" failed with error "%s".', $label, $query, $this->sphinx->getLastError()));
        }

        return $results;
    }

    public function combinedSearch($query, array $indexNames, $escape = true)
    {
        if($escape)
            $query = $this->sphinx->escapeString($query);

        $indexes = '';
        foreach ($indexNames as $indexName) {            
            if (isset($this->indexes[$indexName])) {
                $indexes .= $this->indexes[$indexName]['index_name'] . ' ';
            }
        }


        if (!strlen($indexes)) {
            // returning empty result set when no indexes passed is more user-friendly
            return array();
        }
        
        if ($indexes == 'cloudIndex ' || $indexes == 'factIndex ') {
            $this->sphinx->SetSortMode(SPH_SORT_ATTR_DESC, 'date_timestamp');
        }

        $result = $this->sphinx->query($query, $indexes);
        if ($result['status'] !== SEARCHD_OK) {
            throw new \RuntimeException(sprintf('Searching index "%s" for "%s" failed with error "%s".', $indexes, $query, $this->sphinx->getLastError()));
        }

        return $result;
    }

    /** @return \SphinxClient */
    public function getSphinx()
    {
        return $this->sphinx;
    }

    /**
     * Proxy to SphixApi->BuildExcerpts()
     * Currently just maps index names
     * 
     * @param array $docs
     * @param string $index
     * @param string $words
     * @param array $opts
     */
    public function BuildExcerpts($docs, $index, $words, $opts = array())
    {
        if(!isset($this->indexes[$index]))
            throw new \RuntimeException(sprintf('Building excerpts with index "%s" failed. Index not mapped.', $index));
        return $this->getSphinx()->BuildExcerpts($docs, $this->indexes[$index]['index_name'], $words, $opts);
    }
}
