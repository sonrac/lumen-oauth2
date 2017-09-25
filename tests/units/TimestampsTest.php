<?php
/**
 * Created by PhpStorm.
 * User: sonrac
 * Date: 9/1/17
 * Time: 6:20 PM
 */

namespace Tests\units;

use sonrac\lumenRest\traits\UnixTimestampsTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use sonrac\lumenRest\tests\TestCase;

/**
 * Class TimestampsTest
 * Timestamp trait test
 *
 * @package Tests\Units
 *
 * @author Donii Sergii <doniysa@gmail.com>
 */
class TimestampsTest extends TestCase
{
    /**
     * Update the model's update timestamp.
     *
     * @return bool
     */
    public function touch()
    {
        if (! $this->usesUnixTimestamps()) {
            return false;
        }

        $this->updateTimestamps();

        return $this->save();
    }

    public function usesUnixTimestamps() {
        return property_exists($this, 'unitTimestamps') && $this->unixTimestamps;
    }

    /**
     * @inheritdoc
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function setUp()
    {
        parent::setUp();

        \Schema::create('test', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->bigInteger('created_at');
            $table->bigInteger('updated_at');
            $table->bigInteger('last_login');
        });
    }

    /**
     * Test timestamp trait
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testTimestamp()
    {
        \DB::table('test')
            ->insert([
                'id' => 1,
                'created_at' => (new DateTime())->modify('-5 days')->getTimestamp(),
                'updated_at' => (new DateTime())->getTimestamp(),
                'last_login' => (new DateTime())->getTimestamp(),
                'name' => 'test',
            ]);

        $model = new BaseModel();

        $model->name = 'test name';
        $this->_testSetAttribute($model);
        $this->_testSetAttribute($model, 'updated_at');
        $this->_testSetAttribute($model, 'last_login');

        $this->assertTrue($model->save());

        $m = (array)\DB::table('test')->where(['id' => $model->getAttribute('id')])->get()->first();

        $this->assertEquals(1504281861, $m['created_at']);
        $this->assertEquals(1504281861, $m['last_login']);
        $this->assertEquals(1504281861, $m['updated_at']);
    }

    /**
     * Test auto fill attributes
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function testAutoFill() {
        $model = new BaseModel();
        $model->name = 'third test model';
        $this->assertTrue($model->save());
        foreach ($model->getTimestampAttributes() as $timestampAttribute) {
            if (in_array($timestampAttribute, $model->getTimestampAutoFillAttributes())) {
                $this->assertInstanceOf(Carbon::class, $model->getAttribute($timestampAttribute));
            }
        }
    }

    /**
     * @param BaseModel $model
     * @param string $name
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    private function _testSetAttribute($model, $name = 'created_at')
    {
        foreach (['2017-2-2', (new DateTime()), (new Carbon()), '1504281861', 1504281861] as $value) {
            $model->$name = $value;
            $this->assertInstanceOf(Carbon::class, $model->$name);
        }
    }

    /**
     * @inheritdoc
     *
     * @author Donii Sergii <doniysa@gmail.com>
     */
    public function tearDown()
    {
        \Schema::drop('test');
        parent::tearDown();
    }
}

/**
 * Class BaseModel
 * Base model timestamps
 *
 * @property int $id
 * @property string $name
 * @property string|Carbon|DateTime|null $last_login
 *
 * @package Tests\Units
 *
 * @author Donii Sergii <doniysa@gmail.com>
 */
class BaseModel extends Model
{
    use UnixTimestampsTrait;

    protected $table = 'test';

    public $timestamps = false;

    public $unixTimestamps = true;

    protected $fillable = ['last_login', 'created_at', 'updated_at', 'name'];

    public function getTimestampAttributes()
    {
        return ['created_at', 'updated_at', 'last_login'];
    }

    public function getTable()
    {
        return 'test';
    }

}