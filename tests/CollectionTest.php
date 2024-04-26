<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;

function collect(array $data)
{
    return new Collection($data);
}

class CollectionTest extends TestCase
{
    public function testInit()
    {
        $collection = new Collection([]);
        $this->assertTrue($collection instanceof Collection);
        $this->assertTrue(collect([]) instanceof Collection);
    }

    public function testAll()
    {
        $collection = new Collection([1, 2, 3]);
        $this->assertIsArray($collection->all());
        $this->assertEquals([1, 2, 3], $collection->all());
    }

    public function testAverage()
    {
        $this->assertEquals(collect([
            ['foo' => 10],
            ['foo' => 10],
            ['foo' => 20],
            ['foo' => 40]
        ])->avg('foo'), 20);

        $this->assertEquals(collect([1, 1, 2, 4])->average(), 2);
    }

    public function testChunk()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7]);
        $chunks = $collection->chunk(4);

        $this->assertEquals($chunks->all(), [[1, 2, 3, 4], [5, 6, 7]]);
    }

    public function testChunkWhile()
    {
        $this->assertEquals(collect(str_split('AABBCCCD'))->chunkWhile(function (string $value, int $key, Collection $chunk) {
            return $value === $chunk->last();
        })->all(), [['A', 'A'], ['B', 'B'], ['C', 'C', 'C'], ['D']]);
    }

    public function testLast()
    {
        $this->assertEquals(
            collect([1, 2, 3, 4])->last(function (int $value, int $key) {
                return $value < 3;
            })
            ,
            2
        );
        $this->assertEquals(collect([1, 2, 3, 4])->last(), 4);
    }

    public function testCollapse()
    {
        $this->assertEquals(collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ])->collapse()->all(), [1, 2, 3, 4, 5, 6, 7, 8, 9]);
    }

    public function testCollect()
    {
        $collection = collect([1, 2, 3]);
        $copy = $collection->collect();
        $this->assertEquals($collection->all(), $copy->all());
    }
}