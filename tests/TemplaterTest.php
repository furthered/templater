<?php

namespace Templater\Test;

use Templater\Format\Format;

require_once 'route.php';

class TemplaterTest extends \PHPUnit_Framework_TestCase {

    /** @test */
    public function it_can_format_a_phone_number()
    {
        $formatted = (new Format)->phone('1234567890');

        $this->assertSame('(123) 456-7890', $formatted);
    }

    /** @test */
    public function it_can_format_a_phone_number_with_an_extension()
    {
        $formatted = (new Format)->phone('1234567890x987');

        $this->assertSame('(123) 456-7890 ext. 987', $formatted);
    }

    /** @test */
    public function it_will_leave_alone_a_format_it_does_not_recognize()
    {
        $formatted = (new Format)->phone('11234567890');

        $this->assertSame('11234567890', $formatted);
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

    /** @test */
    public function it_can_remove_the_third_item_in_a_list_if_matches_exactly()
    {
        $list = [
            ['name' => 'Joe'],
            ['name' => 'Damian'],
            ['name' => 'Gary'],
        ];

        $result = (new Format)->toList($list, 'name', 17);

        $this->assertSame('Joe, Damian&hellip;', $result);
    }

    /** @test */
    public function it_will_trim_excess_space_before_ellipses()
    {
        $list = [
            ['name' => 'Joe'],
            ['name' => 'Damian'],
            ['name' => 'Gary LastName'],
        ];

        $result = (new Format)->toList($list, 'name', 23);

        $this->assertSame('Joe, Damian, and Gary&hellip;', $result);
    }

    /** @test */
    public function it_can_create_a_linked_list()
    {
        $list = [
            ['name' => 'Joe', 'id' => 1],
            ['name' => 'Damian', 'id' => 2],
            ['name' => 'Gary', 'id' => 3],
        ];

        $result = (new Format)->toListLinks($list, 'name', 'user', 'id');

        $this->assertSame('<a href="/user/1">Joe</a>, <a href="/user/2">Damian</a>, and <a href="/user/3">Gary</a>', $result);
    }

    /** @test */
    public function it_can_create_a_truncated_linked_list()
    {
        $list = [
            ['name' => 'Joe', 'id' => 1],
            ['name' => 'Damian', 'id' => 2],
            ['name' => 'Gary', 'id' => 3],
        ];

        $result = (new Format)->toListLinks($list, 'name', 'user', 'id', ['truncate' => 20]);

        $this->assertSame('<a href="/user/1">Joe</a>, <a href="/user/2">Damian</a>, and <a href="/user/3">Ga&hellip;</a>', $result);
    }

    /** @test */
    public function it_can_create_a_linked_list_with_something_appended()
    {
        $list = [
            ['name' => 'Joe', 'id' => 1, 'company' => 'Joe Co'],
            ['name' => 'Damian', 'id' => 2, 'company' => 'Damian Co'],
            ['name' => 'Gary', 'id' => 3, 'company' => 'Gary Co'],
        ];

        $result = (new Format)->toListLinks($list, 'name', 'user', 'id', ['append_plain' => ' <span class="also">({company})</span>']);

        $str = '<a href="/user/1">Joe</a> <span class="also">(Joe Co)</span>, '
                . '<a href="/user/2">Damian</a> <span class="also">(Damian Co)</span>, '
                . 'and <a href="/user/3">Gary</a> <span class="also">(Gary Co)</span>';

        $this->assertSame($str, $result);
    }

    /** @test */
    public function it_can_create_a_linked_list_with_something_prepended()
    {
        $list = [
            ['name' => 'Joe', 'id' => 1, 'company' => 'Joe Co'],
            ['name' => 'Damian', 'id' => 2, 'company' => 'Damian Co'],
            ['name' => 'Gary', 'id' => 3, 'company' => 'Gary Co'],
        ];

        $result = (new Format)->toListLinks($list, 'name', 'user', 'id', ['prepend_plain' => '<span class="also">({company})</span> ']);

        $str = '<span class="also">(Joe Co)</span> <a href="/user/1">Joe</a>, '
                . '<span class="also">(Damian Co)</span> <a href="/user/2">Damian</a>, '
                . 'and <span class="also">(Gary Co)</span> <a href="/user/3">Gary</a>';

        $this->assertSame($str, $result);
    }

}
