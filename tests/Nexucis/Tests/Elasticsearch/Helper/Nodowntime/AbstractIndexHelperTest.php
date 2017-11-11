<?php

namespace Nexucis\Tests\Elasticsearch\Helper\Nodowntime;

use Elasticsearch\ClientBuilder;
use Nexucis\Elasticsearch\Helper\Nodowntime\IndexHelper;
use PHPUnit\Framework\TestCase;

abstract class AbstractIndexHelperTest extends TestCase
{

    /**
     * @var $helper IndexHelper
     */
    protected $helper;

    /**
     * @var $client \Elasticsearch\Client
     */
    protected $client;

    protected static $documents;

    /**
     * initialize static data
     */
    public static function setUpBeforeClass()
    {
        // load static data
        self::$documents = json_decode(file_get_contents('http://data.consumerfinance.gov/api/views.json'));
    }

    /**
     * initialize elasticsearch client and index Helper
     */
    public function setUp()
    {
        $this->client = ClientBuilder::create()->setHosts([$_SERVER['ES_TEST_HOST']])->build();
        $this->helper = new IndexHelper($this->client);
        parent::setUp();
    }

    public function tearDown()
    {
        // remove all previously indices created by test or by the before setup
        $param = [
            'index' => '_all'
        ];
        $this->client->indices()->delete($param);
    }

    public function aliasDataProvider()
    {
        return [
            'latin-char' => ['myindextest'],
            'utf-8-char' => ['⿇⽸⾽']
        ];
    }

    protected function loadFinancialIndex($alias, $type = 'complains')
    {
        $this->helper->createIndex($alias);

        $this->addBulkDocuments($this->jsonArrayToBulkArray(self::$documents, $alias, $type));
    }

    /**
     * @param $index
     * @return int
     */
    protected function countDocuments($index)
    {
        $params = array(
            'index' => $index,
        );
        return $this->client->count($params)['count'];
    }

    private function addBulkDocuments($params)
    {
        $this->client->bulk($params);
    }

    private function jsonArrayToBulkArray($documents, $index, $type)
    {
        $params = array();
        foreach ($documents as $document) {
            $params['body'][] = [
                'index' => [
                    '_index' => $index,
                    '_type' => $type,
                ]
            ];
            $params['body'][] = $document;
        }
        // wait until the result are visible to search
        $params['refresh'] = true;
        return $params;
    }
}
