<?php
require_once(__DIR__.'/../files/extension.php');
require_once(__DIR__.'/../files/tree_orm.php');
require_once(__DIR__.'/../files/fairy_orm.php');
require_once(__DIR__.'/../files/namespaced_orm.php');


/**
 * Generated by PHPUnit_SkeletonGenerator on 2013-02-08 at 21:28:15.
 */
class ORM_Model_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ORM
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
	
		$this->db_file = tempnam(sys_get_temp_dir(), 'test.sqlite');
		$this->conf_file = tempnam(sys_get_temp_dir(), 'test.conf');
		file_put_contents($this->db_file, '');
		$db = new PDO('sqlite:'.$this->db_file);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec("CREATE TABLE fairies(id INT PRIMARY_KEY,name VARCHAR(255),tree_id INT)");
		$db->exec("CREATE TABLE trees(id INT PRIMARY_KEY,name VARCHAR(255),protector_id INT)");
		$db->exec("CREATE TABLE friends(fairy_id INT,friend_id INT)");

		$db->exec("INSERT INTO fairies(id,name,tree_id) VALUES (1,'Tinkerbell',1)");
		$db->exec("INSERT INTO fairies(id,name,tree_id) VALUES (2,'Trixie',2)");

		$db->exec("INSERT INTO trees(id,name,protector_id) VALUES (1,'Oak',2)");
		$db->exec("INSERT INTO trees(id,name,protector_id) VALUES (2,'Willow',2)");

		$db->exec("INSERT INTO friends(fairy_id,friend_id) VALUES (1,2)");

		$this->pixie = $this->getMock('\PHPixie\Pixie',array('find_file'));
		$this->pixie->expects($this->any())
                 ->method('find_file')
                 ->will($this->returnValue($this->conf_file));
		$this->pixie->db = new \PHPixie\DB($this->pixie);
		$this->pixie-> orm = new \PHPixie\ORM($this->pixie);
				 
		$this->pixie->config->set('db.orm.connection', 'sqlite:'.$this->db_file);
		$this->pixie->config->set('db.orm.driver', 'pdo');

		$this->fairies = new \Model\Fairy($this->pixie);
		$this->trees = new \Model\Tree($this->pixie);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{	
		$db = $this->pixie->db->get('orm');
		$db->conn = null;
		unlink($this->db_file);
		unlink($this->conf_file);
	}

	/**
	 * @covers ORM::__call
	 * @todo   Implement test__call().
	 */
	public function test__call()
	{
		$this->fairies->limit(6);
		$this->fairies->offset(5);
		$this->assertEquals(6, $this->fairies->limit());
		$this->assertEquals(5, $this->fairies->offset());
		$this->fairies->where('id', 8);
		$this->fairies->order_by('id', 'desc');
	}

	/**
	 * @covers ORM::find_all
	 * @todo   Implement testFind_all().
	 */
	public function testFind_all()
	{

		$this->assertEquals('Trixie', $this->pixie->orm->get('fairy')->where('id', 2)->find_all()->current()->name);
	}

	/**
	 * @covers ORM::find
	 * @todo   Implement testFind().
	 */
	public function testFind()
	{
		$this->assertEquals('Trixie', $this->fairies->where('id', 2)->find()->name);
	}

	/**
	 * @covers ORM::count_all
	 * @todo   Implement testCount_all().
	 */
	public function testCount_all()
	{
		$this->assertEquals(2, $this->fairies->count_all());
		$this->assertEquals(0, $this->fairies->find()->protects->count_all());
		$this->assertEquals(1, $this->fairies->where('id', 2)->count_all());
		$this->assertEquals(2, $this->pixie->orm->get('fairy')->where('id', 2)->protects->count_all());
	}

	/**
	 * @covers ORM::loaded
	 * @todo   Implement testLoaded().
	 */
	public function testLoaded()
	{
		$this->assertEquals(false, $this->fairies->loaded());
		$this->assertEquals(true, $this->fairies->find()->loaded());
	}

	/**
	 * @covers ORM::as_array
	 * @todo   Implement testAs_array().
	 */
	public function testAs_array()
	{
		$arr = $this->fairies->find()->as_array();
		$this->assertEquals('Tinkerbell', $arr['name']);
	}

	/**
	 * @covers ORM::query
	 * @todo   Implement testQuery().
	 */
	public function testQuery()
	{
		$this->fairies->limit(5);
		$this->assertEquals(5, $this->fairies->query()->limit());
	}

	/**
	 * @covers ORM::get
	 * @todo   Implement testGet().
	 */
	public function testGet()
	{
		$dd = array('fff' => 44);

		$fairy = $this->fairies->find();
		$fairy->test;
		$this->assertEquals(5, $fairy->cached['test']);
	}

	/**
	 * @covers ORM::__get
	 * @todo   Implement test__get().
	 */
	public function test__get()
	{
		$this->assertEquals('Oak', $this->fairies->tree->find_all()->current()->name);
		$this->assertEquals('Oak', $this->fairies->find()->tree->name);
		$this->assertEquals('Tinkerbell', $this->trees->find()->fairy->name);
		$this->assertEquals('Tinkerbell', $this->trees->find()->fairy->name);
		$protects = $this->fairies->where('id', 2)->find()->protects->find_all();
		$this->assertEquals('Oak', $protects->current()->name);
		$protects->next();
		$this->assertEquals('Willow', $protects->current()->name);
		$this->assertEquals('Trixie', $this->trees->protector->find()->name);

		$this->assertEquals('Trixie', $this->pixie->orm->get('fairy')->find()->friends->find()->name);
	}

	/**
	 * @covers ORM::add
	 * @todo   Implement testAdd().
	 */
	public function testAdd()
	{
		$fairy = $this->pixie->orm->get('fairy')->find();
		$fairy->add('tree', $this->pixie->orm->get('tree')->where('id', 2)->find());
		$fairy->save();
		$this->assertEquals('Willow', $this->pixie->orm->get('fairy')->find()->tree->name);

		$fairy = $this->pixie->orm->get('fairy');
		$fairy->add('tree', $this->pixie->orm->get('tree')->where('id', 2)->find());
		$fairy->id = 3;
		$fairy->save();
		$fairy = $this->pixie->orm->get('fairy', 3);
		$this->assertEquals('Willow', $fairy->tree->name);

		$tree = $this->pixie->orm->get('tree')->find();
		$fairy = $this->pixie->orm->get('fairy');
		$tree->fairy = $fairy;
		$fairy->id = 4;
		$fairy->save();
		$this->assertEquals('Oak', $this->pixie->orm->get('fairy', 4)->tree->name);


		$fairy = $this->pixie->orm->get('fairy')->where('id', 2)->find();
		$fairy->add('friends', $this->pixie->orm->get('fairy')->where('id', 1)->find());
		$this->assertEquals('Tinkerbell', $this->pixie->orm->get('fairy')->where('id', 2)->find()->friends->find()->name);
	}

	/**
	 * @covers ORM::__set
	 * @todo   Implement test__set().
	 */
	public function test__set()
	{
		$fairy = $this->fairies->find();
		$fairy->name = 'test';
		$this->assertEquals('test', $fairy->name);

		$fairy = $this->pixie->orm->get('fairy')->where('id', 2)->find();
		$fairy->friends = $this->pixie->orm->get('fairy')->where('id', 1)->find();
		$this->assertEquals('Tinkerbell', $this->pixie->orm->get('fairy')->where('id', 2)->find()->friends->find()->name);

		$this->pixie->orm->get('tree')->where('id', 2)->find()->fairy = $this->pixie->orm->get('fairy')->find();
		$this->pixie->orm->get('tree')->find()->fairy = $this->pixie->orm->get('fairy')->where('id', 2)->find();

		$this->assertEquals('Trixie', $this->pixie->orm->get('tree')->find()->fairy->name);
	}

	/**
	 * @covers ORM::remove
	 * @todo   Implement testRemove().
	 */
	public function testRemove()
	{
		$fairy = $this->pixie->orm->get('fairy')->find();
		$fairy->remove('tree');

		$this->assertEquals(false, $fairy->tree->loaded());

		$fairy->remove('friends', $this->pixie->orm->get('fairy')->where('id', 2)->find());
		$this->assertEquals(false, $fairy->friends->find()->loaded());

		$fairy = $this->pixie->orm->get('fairy')->where('id', 2)->find();
		$fairy->remove('protects', $this->pixie->orm->get('tree')->where('id', 1)->find());
		$this->assertEquals('Willow', $fairy->protects->find()->name);
	}
	
	public function testIsset() {
		$fairy = $this->fairies->find();
		$this->assertEquals(true, isset($fairy->id));
		$this->assertEquals(true, isset($fairy->protects));
		$this->assertEquals(false, isset($fairy->bogus));
		$this->assertEquals(true, isset($fairy->test));
	}
	/**
	 * @covers ORM::columns
	 * @todo   Implement testColumns().
	 */
	public function testColumns()
	{
		$cols = $this->fairies->columns();
		$this->assertEquals('id', $cols[0]);
		$this->assertEquals('name', $cols[1]);
		$this->assertEquals('tree_id', $cols[2]);
	}

	/**
	 * @covers ORM::id
	 */
	public function testId()
	{
		$fairy = $this->fairies->find();
		$this->assertEquals(1,$fairy->id());
	}
	/**
	 * @covers ORM::with
	 * @todo   Implement testWith().
	 */
	public function testWith()
	{
		$res = $this->fairies->with ('tree', 'tree.protector')->find();
		$this->assertEquals('Oak', $res->cached['tree']->name);
		$this->assertEquals('Trixie', $res->cached['tree']->cached['protector']->name);
	}

	/**
	 * @covers ORM::delete
	 * @todo   Implement testDelete().
	 */
	public function testDelete()
	{
		$this->fairies->find()->delete();
		$this->assertEquals('Trixie', $this->fairies->find()->name);
	}

	/**
	 * @covers ORM::delete
	 * @todo   Implement testDelete().
	 */
	public function testDeleteException()
	{
		$except = false;
		try {
			$this->fairies->delete();
		} catch (Exception $e) {
			$except = true;
		}
		$this->assertEquals(true, $except);
	}

	/**
	 * @covers ORM::delete_all
	 * @todo   Implement testDelete_all().
	 */
	public function testDelete_all()
	{
		$this->fairies->delete_all();
		$this->assertEquals(false, $this->fairies->find()->loaded());
	}

	/**
	 * @covers ORM::save
	 * @todo   Implement testSave().
	 */
	public function testSave()
	{
		$fairy = $this->fairies->find();
		$fairy->name = 'test';
		$fairy->save();
		$this->assertEquals('test', $this->fairies->find()->name);

		$fairy = $this->pixie->orm->get('fairy');
		$fairy->name = 'test2';
		$fairy->id = 3;
		$fairy->save();
		$this->assertEquals('test2', $this->fairies->order_by('id', 'desc')->find()->name);
	}

	/**
	 * @covers ORM::values
	 * @todo   Implement testValues().
	 */
	public function testValues()
	{
		$fairy = $this->fairies->find();
		$fairy->values(array('id' => 1, 'name' => 'Trixie'));
		$this->assertEquals('Trixie', $fairy->name);
	}
	
	public function testExtension() {
		$fairy = $this->fairies->find();
		$fairy->extension->id = 1;
		$this->assertEquals(1,$fairy->extension->id);
	}
	/**
	 * @covers $this->pixie->orm->get
	 * @todo   Implement testFactory().
	 */
	public function testFactory()
	{
		$this->assertEquals('fairy', $this->pixie->orm->get('fairy')->model_name);
	}
	
	public function testModel_key() {
		$this->assertEquals('forest_fairy_id', $this->pixie->orm->get('fairy')->model_key('forest\fairy'));
	}
	
	public function testNamespaceTable() {
		$this->assertEquals('forest_fairies', $this->pixie->orm->get('forest\fairy')->table);
	}

}
