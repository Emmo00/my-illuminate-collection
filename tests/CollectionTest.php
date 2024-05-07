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

    public function testCombine()
    {
        $combined = collect(['name', 'age'])->combine(['George', 29]);
        $this->assertEquals($combined->all(), [
            'name' => 'George',
            'age' => 29
        ]);
    }

    public function testConcat()
    {
        $collection = collect(['John Doe']);

        $concatenated = $collection->concat(['Jane Doe'])->concat(['name' => 'Johnny Doe']);

        $this->assertEquals($concatenated->all(), ['John Doe', 'Jane Doe', 'Johnny Doe']);
    }

    public function testContains()
    {
        $col = collect([1, 2, 3, 4, 5]);
        $this->assertFalse($col->contains(function (int $value, int $key) {
            return $value > 5;
        }));
        $this->assertTrue($col->contains(function (int $value, int $key) {
            return $value < 5;
        }));

        $col = collect(['name' => 'Desk', 'price' => 100]);
        $this->assertTrue($col->contains('Desk'));
        $this->assertFalse($col->contains('New York'));

        $col = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
        ]);
        $this->assertFalse($col->contains('product', 'Bookcase'));
        $this->assertTrue($col->contains('product', 'Chair'));
    }

    public function testContainsStrict()
    {
        $col = collect([1, 2, 3, 4, 5]);
        $this->assertFalse($col->containsStrict(function (int $value, int $key) {
            return $value > 5;
        }));
        $this->assertTrue($col->containsStrict(function (int $value, int $key) {
            return $value < 5;
        }));

        $col = collect(['name' => 'Desk', 'price' => 100]);
        $this->assertFalse($col->containsStrict('100'));
        $this->assertTrue($col->containsStrict(100));

        $col = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
        ]);
        $this->assertFalse($col->containsStrict('price', '200'));
        $this->assertTrue($col->containsStrict('price', 100));
    }
    public function testDoesntContain()
    {
        $col = collect([1, 2, 3, 4, 5]);
        $this->assertTrue($col->doesntContain(function (int $value, int $key) {
            return $value > 5;
        }));
        $this->assertFalse($col->doesntContain(function (int $value, int $key) {
            return $value < 5;
        }));

        $col = collect(['name' => 'Desk', 'price' => 100]);
        $this->assertFalse($col->doesntContain('Desk'));
        $this->assertTrue($col->doesntContain('New York'));

        $col = collect([
            ['product' => 'Desk', 'price' => 200],
            ['product' => 'Chair', 'price' => 100],
        ]);
        $this->assertTrue($col->doesntContain('product', 'Bookcase'));
        $this->assertFalse($col->doesntContain('product', 'Chair'));
    }

    public function testContainsOneItem()
    {
        $this->assertFalse(collect([])->containsOneItem());

        $this->assertTrue(collect(['1'])->containsOneItem());

        $this->assertFalse(collect(['1', '2'])->containsOneItem());
    }

    public function testCount()
    {
        $collection = collect([1, 2, 3, 4]);

        $this->assertEquals($collection->count(), 4);
    }

    public function testCountBy()
    {
        $collection = collect([1, 2, 2, 2, 3]);
        $this->assertEquals($collection->countBy()->all(), [
            1 => 1,
            2 => 3,
            3 => 1,
        ]);

        $collection = collect(['alice@gmail.com', 'bob@yahoo.com', 'carlos@gmail.com']);
        $this->assertEquals($collection->countBy(function (string $email) {
            return substr(strrchr($email, '@'), 1);
        })->all(), [
            'gmail.com' => 2,
            'yahoo.com' => 1
        ]);
    }
}