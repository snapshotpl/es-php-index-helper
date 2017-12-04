<?php

namespace Nexucis\Tests\Elasticsearch\Helper\Nodowntime;

class IndexActionTest extends AbstractIndexHelperTest
{

    /**
     * @dataProvider aliasDataProvider
     */
    public function testCreateIndex($alias)
    {
        $this->helper->createIndexByAlias($alias);
        $this->assertTrue($this->helper->existsIndex($alias));
        $this->assertTrue($this->helper->existsIndex($alias . $this->helper::INDEX_NAME_CONVENTION_1));
    }

    /**
     * @@expectedException \Nexucis\Elasticsearch\Helper\Nodowntime\Exceptions\IndexAlreadyExistException
     */
    public function testCreateIndexAlreadyExistsException()
    {
        $alias = 'myindextest';
        $this->helper->createIndexByAlias($alias);
        $this->helper->createIndexByAlias($alias);
    }

    /**
     * @dataProvider aliasDataProvider
     */
    public function testDeleteIndex($alias)
    {
        $this->helper->createIndexByAlias($alias);
        $this->helper->deleteIndex($alias);

        $this->assertFalse($this->helper->existsIndex($alias));
        $this->assertFalse($this->helper->existsIndex($alias . $this->helper::INDEX_NAME_CONVENTION_1));
    }

    /**
     * @expectedException \Nexucis\Elasticsearch\Helper\Nodowntime\Exceptions\IndexNotFoundException
     */
    public function testDeleteIndexNotFoundException()
    {
        $alias = 'myindextest';
        $this->helper->deleteIndex($alias);
    }

    /**
     * @dataProvider aliasDataProvider
     */
    public function testCopyEmptyIndex($alias)
    {
        $this->helper->createIndexByAlias($alias);

        $aliasDest = $alias . '2';

        $this->assertEquals($this->helper::RETURN_ACKNOWLEDGE, $this->helper->copyIndex($alias, $aliasDest));

        $this->assertTrue($this->helper->existsIndex($alias));
        $this->assertTrue($this->helper->existsIndex($alias . $this->helper::INDEX_NAME_CONVENTION_1));
        $this->assertTrue($this->helper->existsIndex($aliasDest));
        $this->assertTrue($this->helper->existsIndex($aliasDest . $this->helper::INDEX_NAME_CONVENTION_1));
    }

    public function testCopyIndex()
    {
        $alias = 'financial';
        // create index with some contents
        $this->loadFinancialIndex($alias);

        $aliasDest = "indexcopy";
        $this->assertEquals($this->helper::RETURN_ACKNOWLEDGE, $this->helper->copyIndex($alias, $aliasDest, true));

        $this->assertTrue($this->helper->existsIndex($alias));
        $this->assertTrue($this->helper->existsIndex($alias . $this->helper::INDEX_NAME_CONVENTION_1));
        $this->assertTrue($this->helper->existsIndex($aliasDest));
        $this->assertTrue($this->helper->existsIndex($aliasDest . $this->helper::INDEX_NAME_CONVENTION_1));
        $this->assertEquals($this->countDocuments($alias), $this->countDocuments($aliasDest));
    }

    public function testCopyIndexAsynchronusByTask()
    {
        $alias = 'financial';
        // create index with some contents
        $this->loadFinancialIndex($alias);

        $aliasDest = "indexcopy";

        $result = $this->helper->copyIndex($alias, $aliasDest, false, false);
        $this->assertRegExp('/\w+:\d+/i', $result);
    }

    /**
     * @expectedException \Nexucis\Elasticsearch\Helper\Nodowntime\Exceptions\IndexNotFoundException
     */
    public function testCopyIndexNotFoundException()
    {
        $aliasSrc = 'myindextest';
        $this->helper->copyIndex($aliasSrc, $aliasSrc);
    }

    /**
     * @@expectedException \Nexucis\Elasticsearch\Helper\Nodowntime\Exceptions\IndexAlreadyExistException
     */
    public function testCopyIndexAlreadyExistsException()
    {
        $aliasSrc = 'myindextest';
        $this->helper->createIndexByAlias($aliasSrc);

        $this->helper->copyIndex($aliasSrc, $aliasSrc);
    }

    /**
     * @dataProvider aliasDataProvider
     */
    public function testReindexEmptyIndex($alias)
    {
        $this->helper->createIndexByAlias($alias);

        $this->assertEquals($this->helper::RETURN_ACKNOWLEDGE, $this->helper->reindex($alias));

        $this->assertTrue($this->helper->existsIndex($alias));
        $this->assertTrue($this->helper->existsIndex($alias . $this->helper::INDEX_NAME_CONVENTION_2));
    }

    public function testReindex()
    {
        $alias = 'financial';
        // create index with some contents
        $this->loadFinancialIndex($alias);

        $this->assertEquals($this->helper::RETURN_ACKNOWLEDGE, $this->helper->reindex($alias, true));

        $this->assertTrue($this->helper->existsIndex($alias));
        $this->assertTrue($this->helper->existsIndex($alias . $this->helper::INDEX_NAME_CONVENTION_2));
        $this->assertTrue($this->countDocuments($alias) > 0);
    }

    public function testReindexAsynchronusByTask()
    {
        $alias = 'financial';
        // create index with some contents
        $this->loadFinancialIndex($alias);

        $result = $this->helper->reindex($alias, false, true, false);

        $this->assertRegExp('/\w+:\d+/i', $result);
    }

    /**
     * @expectedException \Nexucis\Elasticsearch\Helper\Nodowntime\Exceptions\IndexNotFoundException
     */
    public function testReindexIndexNotFoundException()
    {
        $aliasSrc = 'myindextest';

        $this->helper->reindex($aliasSrc);
    }
}
