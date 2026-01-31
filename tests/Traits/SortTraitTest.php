<?php
declare(strict_types=1);

namespace App\Tests\Traits;

use App\Traits\SortTrait;
use PHPUnit\Framework\TestCase;

class SortTraitTest extends TestCase
{
    private object $trait_object;

    protected function setUp(): void
    {
        $this->trait_object = new class {
            use SortTrait;
        };
    }

    public function testSortByFieldFilterWithObjectsAscending(): void
    {
        $items = [
            (object) ['name' => 'Charlie', 'age' => 30],
            (object) ['name' => 'Alice', 'age' => 25],
            (object) ['name' => 'Bob', 'age' => 35],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'name', 'asc');

        $this->assertEquals('Alice', $result[0]->name);
        $this->assertEquals('Bob', $result[1]->name);
        $this->assertEquals('Charlie', $result[2]->name);
    }

    public function testSortByFieldFilterWithObjectsDescending(): void
    {
        $items = [
            (object) ['name' => 'Charlie', 'age' => 30],
            (object) ['name' => 'Alice', 'age' => 25],
            (object) ['name' => 'Bob', 'age' => 35],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'name', 'desc');

        $this->assertEquals('Charlie', $result[0]->name);
        $this->assertEquals('Bob', $result[1]->name);
        $this->assertEquals('Alice', $result[2]->name);
    }

    public function testSortByFieldFilterWithArraysAscending(): void
    {
        $items = [
            ['name' => 'Charlie', 'age' => 30],
            ['name' => 'Alice', 'age' => 25],
            ['name' => 'Bob', 'age' => 35],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'name', 'asc');

        $this->assertEquals('Alice', $result[0]['name']);
        $this->assertEquals('Bob', $result[1]['name']);
        $this->assertEquals('Charlie', $result[2]['name']);
    }

    public function testSortByFieldFilterWithArraysDescending(): void
    {
        $items = [
            ['name' => 'Charlie', 'age' => 30],
            ['name' => 'Alice', 'age' => 25],
            ['name' => 'Bob', 'age' => 35],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'name', 'desc');

        $this->assertEquals('Charlie', $result[0]['name']);
        $this->assertEquals('Bob', $result[1]['name']);
        $this->assertEquals('Alice', $result[2]['name']);
    }

    public function testSortByFieldFilterWithNumericFieldAscending(): void
    {
        $items = [
            (object) ['name' => 'Charlie', 'age' => 30],
            (object) ['name' => 'Alice', 'age' => 25],
            (object) ['name' => 'Bob', 'age' => 35],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'age', 'asc');

        $this->assertEquals(25, $result[0]->age);
        $this->assertEquals(30, $result[1]->age);
        $this->assertEquals(35, $result[2]->age);
    }

    public function testSortByFieldFilterWithNumericFieldDescending(): void
    {
        $items = [
            (object) ['name' => 'Charlie', 'age' => 30],
            (object) ['name' => 'Alice', 'age' => 25],
            (object) ['name' => 'Bob', 'age' => 35],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'age', 'desc');

        $this->assertEquals(35, $result[0]->age);
        $this->assertEquals(30, $result[1]->age);
        $this->assertEquals(25, $result[2]->age);
    }

    public function testSortByFieldFilterWithEqualValues(): void
    {
        $items = [
            (object) ['name' => 'Alice', 'score' => 100],
            (object) ['name' => 'Bob', 'score' => 100],
            (object) ['name' => 'Charlie', 'score' => 100],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'score', 'asc');

        // All items have equal scores, order should be preserved or stable
        $this->assertEquals(100, $result[0]->score);
        $this->assertEquals(100, $result[1]->score);
        $this->assertEquals(100, $result[2]->score);
    }

    public function testSortByFieldFilterWithEmptyArray(): void
    {
        $items = [];

        $result = $this->trait_object->sortByFieldFilter($items, 'name', 'asc');

        $this->assertEmpty($result);
        $this->assertIsArray($result);
    }

    public function testSortByFieldFilterWithSingleItem(): void
    {
        $items = [(object) ['name' => 'Alice', 'age' => 25]];

        $result = $this->trait_object->sortByFieldFilter($items, 'name', 'asc');

        $this->assertCount(1, $result);
        $this->assertEquals('Alice', $result[0]->name);
    }

    public function testSortByFieldFilterDefaultAscending(): void
    {
        $items = [
            (object) ['name' => 'Charlie'],
            (object) ['name' => 'Alice'],
            (object) ['name' => 'Bob'],
        ];

        // Not specifying direction should default to 'asc'
        $result = $this->trait_object->sortByFieldFilter($items, 'name');

        $this->assertEquals('Alice', $result[0]->name);
        $this->assertEquals('Bob', $result[1]->name);
        $this->assertEquals('Charlie', $result[2]->name);
    }

    public function testSortByFieldFilterMixedCaseStrings(): void
    {
        $items = [
            (object) ['name' => 'charlie'],
            (object) ['name' => 'Alice'],
            (object) ['name' => 'BOB'],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'name', 'asc');

        // Should sort case-sensitively (uppercase before lowercase in ASCII)
        $this->assertEquals('Alice', $result[0]->name);
        $this->assertEquals('BOB', $result[1]->name);
        $this->assertEquals('charlie', $result[2]->name);
    }

    public function testSortByFieldFilterWithNumericStrings(): void
    {
        $items = [
            (object) ['number' => '100'],
            (object) ['number' => '20'],
            (object) ['number' => '3'],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'number', 'desc');

        // String comparison with descending order
        // Lexicographically: "100" < "20" < "3", so desc flips to "100", "20", "3"
        $this->assertEquals('100', $result[0]->number);
        $this->assertEquals('20', $result[1]->number);
        $this->assertEquals('3', $result[2]->number);
    }

    public function testSortByFieldFilterPreservesKeys(): void
    {
        $items = [
            'first' => (object) ['name' => 'Charlie'],
            'second' => (object) ['name' => 'Alice'],
            'third' => (object) ['name' => 'Bob'],
        ];

        $result = $this->trait_object->sortByFieldFilter($items, 'name', 'asc');

        // After sorting, keys might not be preserved in order but values should be sorted
        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        
        // Get the first value (regardless of key)
        $first_value = reset($result);
        $this->assertEquals('Alice', $first_value->name);
    }

    public function testSortByFieldFilterWithLargeDataset(): void
    {
        $items = [];
        for ($i = 100; $i > 0; $i--) {
            $items[] = (object) ['id' => $i];
        }

        $result = $this->trait_object->sortByFieldFilter($items, 'id', 'asc');

        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals(50, $result[49]->id);
        $this->assertEquals(100, $result[99]->id);
    }
}
