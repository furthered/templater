<?php

namespace Templater\Test;

use Templater\Format\Format;

class TemplaterTest extends \PHPUnit_Framework_TestCase {

    /** @test */
    public function it_can_do_nothing_for_now()
    {
    }

    /** @test */
    public function it_can_create_a_list_from_one_item()
    {
        $list = [
            ['name' => 'Joe'],
        ];

        $result = (new Format)->toList($list, 'name');

        $this->assertSame('Joe', $result);
    }

    /** @test */
    public function it_can_create_a_list_from_two_items()
    {
        $list = [
            ['name' => 'Joe'],
            ['name' => 'Damian'],
        ];

        $result = (new Format)->toList($list, 'name');

        $this->assertSame('Joe and Damian', $result);
    }

    /** @test */
    public function it_can_create_a_list_from_more_than_two_items()
    {
        $list = [
            ['name' => 'Joe'],
            ['name' => 'Damian'],
            ['name' => 'Gary'],
        ];

        $result = (new Format)->toList($list, 'name');

        $this->assertSame('Joe, Damian, and Gary', $result);
    }

    /** @test */
    public function it_can_truncate_a_single_item_list()
    {
        $list = [
            ['name' => 'Joe Tannenbaum'],
        ];

        $result = (new Format)->toList($list, 'name', 10);

        $this->assertSame('Joe Tanne&hellip;', $result);
    }

    /** @test */
    public function it_can_truncate_a_two_item_list()
    {
        $list = [
            ['name' => 'Joe'],
            ['name' => 'Damian'],
        ];

        $result = (new Format)->toList($list, 'name', 5);

        $this->assertSame('Joe&hellip;', $result);
    }

    /** @test */
    public function it_can_truncate_a_two_item_list_with_greater_limit()
    {
        $list = [
            ['name' => 'Joe'],
            ['name' => 'Damian'],
        ];

        $result = (new Format)->toList($list, 'name', 13);

        $this->assertSame('Joe and Dami&hellip;', $result);
    }

    /** @test */
    public function it_will_not_truncate_a_list_shorter_than_the_limit()
    {
        $list = [
            ['name' => 'Joe'],
            ['name' => 'Damian'],
        ];

        $result = (new Format)->toList($list, 'name', 50);

        $this->assertSame('Joe and Damian', $result);
    }

    /** @test */
    public function it_can_truncate_a_longer_list()
    {
        $list = [
            ['name' => 'Joe'],
            ['name' => 'Damian'],
            ['name' => 'Gary'],
        ];

        $result = (new Format)->toList($list, 'name', 10);

        $this->assertSame('Joe, Dami&hellip;', $result);
    }

    /** @test */
    public function it_can_truncate_the_third_item_in_a_list()
    {
        $list = [
            ['name' => 'Joe'],
            ['name' => 'Damian'],
            ['name' => 'Gary'],
        ];

        $result = (new Format)->toList($list, 'name', 20);

        $this->assertSame('Joe, Damian, and Ga&hellip;', $result);
    }

}
