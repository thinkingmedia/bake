<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         1.3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\Console\Command;

use Cake\Console\Command\BakeShellShell;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

class BakeShellTest extends TestCase {

/**
 * fixtures
 *
 * @var array
 */
	public $fixtures = array('core.comment');

/**
 * setup test
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('Cake\Console\ConsoleOutput', [], [], '', false);
		$in = $this->getMock('Cake\Console\ConsoleInput', [], [], '', false);

		$this->Shell = $this->getMock(
			'Cake\Console\Command\BakeShell',
			['in', 'out', 'hr', 'err', 'createFile', '_stop'],
			[$out, $out, $in]
		);
		Configure::write('App.namespace', 'TestApp');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Shell);
	}

/**
 * test bake all
 *
 * @return void
 */
	public function testAllWithModelName() {
		$this->Shell->Model = $this->getMock('Cake\Console\Command\Task\ModelTask');
		$this->Shell->Controller = $this->getMock('Cake\Console\Command\Task\ControllerTask');
		$this->Shell->View = $this->getMock('Cake\Console\Command\Task\ModelTask');

		$this->Shell->Model->expects($this->once())
			->method('bake')
			->with('Comments')
			->will($this->returnValue(true));

		$this->Shell->Controller->expects($this->once())
			->method('bake')
			->with('Comments')
			->will($this->returnValue(true));

		$this->Shell->View->expects($this->once())
			->method('execute');

		$this->Shell->expects($this->at(0))
			->method('out')
			->with('Bake All');

		$this->Shell->expects($this->at(2))
			->method('out')
			->with('<success>Bake All complete</success>');

		$this->Shell->connection = '';
		$this->Shell->params = [];
		$this->Shell->args = ['Comment'];
		$this->Shell->all();

		$this->assertEquals('Comments', $this->Shell->View->args[0]);
	}

/**
 * Test the main function.
 *
 * @return void
 */
	public function testMain() {
		$this->Shell->expects($this->at(0))
			->method('out')
			->with($this->stringContains('The following commands'));
		$this->Shell->expects($this->at(3))
			->method('out')
			->with('model');
		$this->Shell->main();
	}

/**
 * Test loading tasks from core and app directories.
 *
 * @return void
 */
	public function testLoadTasks() {
		$this->Shell->loadTasks();
		$expected = [
			'Behavior',
			'Component',
			'Controller',
			'Fixture',
			'Helper',
			'Model',
			'Plugin',
			'Project',
			'Test',
			'View'
		];
		$this->assertEquals($expected, $this->Shell->tasks);
	}

/**
 * Test loading tasks from plugins
 *
 * @return void
 */
	public function testLoadTasksPlugin() {
	}

}
