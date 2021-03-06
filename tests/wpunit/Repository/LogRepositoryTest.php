<?php

namespace TrackMage\WordPress\Tests\wpunit\Repository;

use Codeception\TestCase\WPTestCase;
use TrackMage\WordPress\Repository\LogRepository;
use WpunitTester;

class LogRepositoryTest extends WPTestCase
{
    /** @var WpunitTester */
    protected $tester;

    /** @var LogRepository */
    private $repo;

    protected function _before()
    {
        global $wpdb;
        $this->repo = new LogRepository($wpdb, true);
        $this->repo->delete([]);
    }

    public function testCriticalPath()
    {
        $row = $this->repo->insert( $submitted = [
            'message' => 'hello',
            'context' => '{}',
        ]);
        self::assertNotNull($row);
        $id = $row['id'];
        self::assertTrue(is_numeric($id), "{$id} is not numeric");
        //find
        self::assertArraySubset($submitted, $this->repo->find($id));

        //findOneBy
        $data = $this->repo->insert( $submitted = [
            'message' => 'world',
            'context' => '{}',
        ]);
        $id = $data['id'];
        self::assertArraySubset($submitted, $this->repo->findOneBy(['message' => 'world']));

        //update
        $this->repo->update( $submitted = [
            'context' => '[]',
        ], ['id' => $id]);
        self::assertArraySubset($submitted, $this->repo->findOneBy(['id' => $id]));

        //findBy
        self::assertCount(2, $this->repo->findBy([]));
        self::assertCount(1, $this->repo->findBy(['message' => 'world']));

        //delete
        self::assertEquals(1, $this->repo->delete(['message' => 'world']));
        self::assertCount(1, $this->repo->findBy([]));
        self::assertEquals(1, $this->repo->delete([]));
        self::assertCount(0, $this->repo->findBy([]));
    }
}
