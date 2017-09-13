<?php


namespace Nexucis\Elasticsearch\Helper\Nodowntime;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\BadMethodCallException;
use Elasticsearch\Common\Exceptions\RuntimeException;
use Nexucis\Elasticsearch\Helper\Nodowntime\Exceptions\IndexNotFoundException;

/**
 * Class IndexHelperInterface
 *
 * @category Elasticsearch Helper
 * @package  Nexucis\Elasticsearch\Helper\Nodowntime
 * @author   Augustin Husson <husson.augustin@gmail.com>
 * @license  MIT
 */
interface IndexHelperInterface
{

    /**
     * You can pass an alias name or an index name here.
     *
     * @param string $index [REQUIRED]
     * @return bool
     */
    public function existsIndex($index);

    /**
     * @param string $alias [REQUIRED]
     * @return void
     * @throws BadMethodCallException
     */
    public function createIndex($alias);

    /**
     * @param $index : index or alias can put here [REQUIRED]
     * @return void
     * @throws IndexNotFoundException
     */
    public function deleteIndex($index);

    /**
     * @param string $alias_src [REQUIRED]
     * @param string $alias_dest [REQUIRED]
     * @return void
     * @throws RuntimeException
     * @throws IndexNotFoundException
     * @throws BadMethodCallException
     */
    public function copyIndex($alias_src, $alias_dest);

    /**
     * @param string $alias [REQUIRED]
     * @param bool $needToCreateIndexDest
     * @return void
     * @throws RuntimeException
     * @throws IndexNotFoundException
     */
    public function reindex($alias, $needToCreateIndexDest = true);

    /**
     * This method must call when you want to add something inside the settings. Because the reindexation is a long task,
     * you should do the difference between add and delete something inside the settings. In the add task,
     * you don't need to reindex , unlike the delete task
     *
     * @param string $alias [REQUIRED]
     * @param array $settings [REQUIRED]
     * @return void
     * @throws IndexNotFoundException
     */
    public function addSettings($alias, $settings);

    /**
     * This méthod must call when you want to delete something inside the settings.
     *
     * @param string $alias [REQUIRED]
     * @param array $settings [REQUIRED]
     * @param bool $needReindexation : The process of reindexation can be so long, instead of calling reindex method inside this method, you may want to call it in an asynchronous process.
     * But if you pass this parameters to false, don't forget to reindex. If you don't do it, you will not see your modification of the settings
     * @return void
     * @throws RuntimeException
     * @throws IndexNotFoundException
     */
    public function updateSettings($alias, $settings, $needReindexation = true);

    /**
     * @param string $alias [REQUIRED]
     * @param array $mapping [REQUIRED]
     * @param bool $needReindexation : The process of reindexation can be so long, instead of calling reindex method inside this method, you may want to call it in an asynchronous process.
     * But if you pass this parameters to false, don't forget to reindex. If you don't do it, you will not see your modification of the mappings
     * @return void
     * @throws RuntimeException
     * @throws IndexNotFoundException
     */
    public function updateMapping($alias, $mapping, $needReindexation = true);

    /**
     * @return array
     */
    public function getListAlias();

    /**
     * @param string $alias [REQUIRED]
     * @return array
     */
    public function getMapping($alias);

    /**
     * @param string $alias [REQUIRED]
     * @return array
     */
    public function getSetting($alias);

    /**
     * @param string $alias [REQUIRED]
     * @param int $from the offset from the first result you want to fetch (0 by default)
     * @param int $size allows you to configure the maximum amount of hits to be returned. (10 by default)
     * @return array
     */
    public function getAllDocuments($alias, $from = 0, $size = 10);

    /**
     * @param string $alias [REQUIRED]
     * @param array $query [REQUIRED]
     * @param null|string $type
     * @param int $from the offset from the first result you want to fetch (0 by default)
     * @param int $size allows you to configure the maximum amount of hits to be returned. (10 by default)
     * @return array
     */
    public function searchDocuments($alias, $query, $type = null, $from = 0, $size = 10);

    /**
     * @param string $index [REQUIRED]
     * @param $id [REQUIRED]
     * @param string $type [REQUIRED]
     * @param array $body [REQUIRED]
     * @return boolean
     * @throws IndexNotFoundException
     */
    public function updateDocument($index, $id, $type, $body);

    /**
     * @param string $index [REQUIRED]
     * @param $id [REQUIRED]
     * @param string $type [REQUIRED]
     * @param array $body [REQUIRED]
     * @return boolean
     * @throws IndexNotFoundException
     */
    public function addDocument($index, $id, $type, $body);

    /**
     * Remove all documents from the given index seen through its alias
     *
     * @param string $alias [REQUIRED]
     * @return boolean
     * @throws IndexNotFoundException
     */
    public function deleteAllDocuments($alias);

    /**
     * @param $alias [REQUIRED]
     * @param $id [REQUIRED]
     * @param string $type [REQUIRED]
     * @return bool
     * @throws IndexNotFoundException
     */
    public function deleteDocument($alias, $id, $type);

    /**
     * @param Client $client
     */
    public function setClient($client);

}