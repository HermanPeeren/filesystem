<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Buffer;

/**
 * Test class for JBuffer.
 *
 * @since  1.0
 */
class BufferTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var JBuffer
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Buffer;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
	}

	/**
	 * Test cases for the stream_open test
	 *
	 * @return array
	 */
	public function casesOpen()
	{
		return array(
			'basic' => array(
				'http://www.example.com/fred',
				null,
				null,
				null,
				'www.example.com',
			),
		);
	}

	/**
	 * testing stream_open().
	 *
	 * @param   string  $path         The path to buffer
	 * @param   string  $mode         The mode of the buffer
	 * @param   string  $options      The options
	 * @param   string  $opened_path  The path
	 * @param   string  $expected     The expected test return
	 *
	 * @dataProvider casesOpen
	 * @return void
	 */
	public function testStreamOpen($path, $mode, $options, $opened_path, $expected)
	{
		$this->object->stream_open($path, $mode, $options, $opened_path);
		$this->assertThat(
			$expected,
			$this->equalTo($this->object->name)
		);
	}

	/**
	 * Test cases for the stream_read test
	 *
	 * @return array
	 */
	public function casesRead()
	{
		return array(
			'basic' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				30,
				10,
				'EFGHIJKLMN',
			),
		);
	}

	/**
	 * testing stream_read().
	 *
	 * @param   string  $buffer    The buffer to perform the operation upon
	 * @param   string  $name      The name of the buffer
	 * @param   int     $position  The position in the buffer of the current pointer
	 * @param   int     $count     The movement of the pointer
	 * @param   bool    $expected  The expected test return
	 *
	 * @dataProvider casesRead
	 * @return void
	 */
	public function testStreamRead($buffer, $name, $position, $count, $expected)
	{
		$this->object->name = $name;
		$this->object->position = $position;
		$this->object->buffers[$name] = $buffer;

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->stream_read($count))
		);
	}

	/**
	 * Test cases for the stream_write test
	 *
	 * @return array
	 */
	public function casesWrite()
	{
		return array(
			'basic' => array(
				'abcdefghijklmnop',
				'www.example.com',
				5,
				'ABCDE',
				'abcdeABCDEklmnop',
			),
		);
	}

	/**
	 * testing stream_write().
	 *
	 * @param   string  $buffer    The buffer to perform the operation upon
	 * @param   string  $name      The name of the buffer
	 * @param   int     $position  The position in the buffer of the current pointer
	 * @param   string  $write     The data to write
	 * @param   bool    $expected  The expected test return
	 *
	 * @dataProvider casesWrite
	 * @return void
	 */
	public function testStreamWrite($buffer, $name, $position, $write, $expected)
	{
		$this->object->name = $name;
		$this->object->position = $position;
		$this->object->buffers[$name] = $buffer;
		$output = $this->object->stream_write($write);

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->buffers[$name])
		);
	}

	/**
	 * Testing stream_tell.
	 *
	 * @return void
	 */
	public function testStreamTell()
	{
		$pos = 10;
		$this->object->position = $pos;

		$this->assertThat(
			$pos,
			$this->equalTo($this->object->stream_tell())
		);
	}

	/**
	 * Test cases for the stream_eof test
	 *
	 * @return array
	 */
	public function casesEOF()
	{
		return array(
			'~EOF' => array(
				'abcdefghijklmnop',
				'www.example.com',
				5,
				false,
			),
			'EOF' => array(
				'abcdefghijklmnop',
				'www.example.com',
				17,
				true,
			),
		);
	}

	/**
	 * Testing stream_eof.
	 *
	 * @param   string  $buffer    The buffer to perform the operation upon
	 * @param   string  $name      The name of the buffer
	 * @param   int     $position  The position in the buffer of the current pointer
	 * @param   bool    $expected  The expected test return
	 *
	 * @dataProvider casesEOF
	 * @return void
	 */
	public function testStreamEOF($buffer, $name, $position, $expected)
	{
		$this->object->name = $name;
		$this->object->position = $position;
		$this->object->buffers[$name] = $buffer;

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->stream_eof())
		);
	}

	/**
	 * Test cases for the stream_seek test
	 *
	 * @return array
	 */
	public function casesSeek()
	{
		return array(
			'basic' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				10,
				SEEK_SET,
				true,
				10,
			),
			'too_early' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-10,
				SEEK_SET,
				false,
				5,
			),
			'off_end' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				100,
				SEEK_SET,
				false,
				5,
			),
			'is_pos' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				10,
				SEEK_CUR,
				true,
				15,
			),
			'is_neg' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-100,
				SEEK_CUR,
				false,
				5,
			),
			'from_end' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-10,
				SEEK_END,
				true,
				42,
			),
			'before_beg' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-100,
				SEEK_END,
				false,
				5,
			),
			'bad_seek_code' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-100,
				100,
				false,
				5,
			),
		);
	}

	/**
	 * Testing stream_seek.
	 *
	 * @param   string  $buffer       The buffer to perform the operation upon
	 * @param   string  $name         The name of the buffer
	 * @param   int     $position     The position in the buffer of the current pointer
	 * @param   int     $offset       The movement of the pointer
	 * @param   int     $whence       The buffer seek op code
	 * @param   bool    $expected     The expected test return
	 * @param   int     $expectedPos  The new buffer position pointer
	 *
	 * @dataProvider casesSeek
	 * @return void
	 */
	public function testStreamSeek($buffer, $name, $position, $offset, $whence, $expected, $expectedPos)
	{
		$this->object->name = $name;
		$this->object->position = $position;
		$this->object->buffers[$name] = $buffer;

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->stream_seek($offset, $whence))
		);
		$this->assertThat(
			$expectedPos,
			$this->equalTo($this->object->position)
		);
	}
}
