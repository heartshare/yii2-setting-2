<?php

class SettingFileStoreTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Yii::$app->set('cache', 'yii\caching\FileCache');
        Yii::$app->set('setting', [
            'class' => 'cszchen\setting\Setting',
            'storages' => [
                'db' => [
                    'class' => 'cszchen\setting\FileStorage',
                    'format' => 'json',
                ]
            ]
        ]);
        Yii::$app->cache->delete(Yii::$app->setting->cacheKey);
    }

    /**
     * @param $key
     * @param $value
     * @param null|string $section
     * @dataProvider settings
     */
    public function testSet($key, $value, $section = null)
    {
        $success = Yii::$app->setting->set($key, $value, $section);
        $this->assertTrue($success);
        return [$key, $value, $section];
    }

    public function testGet()
    {
        $name = Yii::$app->setting->get('file.name');
        $this->assertEquals($name, 'title');

        $url = Yii::$app->setting->get('url');
        $this->assertEquals($url, 'https://www.google.com');
    }

    public function testUpdate()
    {
        $success = Yii::$app->setting->set('file.name', 'new title');
        $this->assertTrue($success);

        $name = Yii::$app->setting->get('file.name');
        $this->assertEquals($name, 'new title');
    }

    public function testGetSection()
    {
        $web = Yii::$app->setting->get('file.*');
        $this->assertCount(2, $web);
        $this->assertArrayHasKey('name', $web);
        $this->assertArrayHasKey('desc', $web);
    }

    public function testCache()
    {
        $this->assertEquals(Yii::$app->setting->cacheKey, 'cszche/setting/cache');
        $cacheData = Yii::$app->cache->get(Yii::$app->setting->cacheKey);
        $data = Yii::$app->setting->getData(true);
        $this->assertEquals($cacheData, $data);
    }

    public function testClearCache()
    {
        Yii::$app->setting->clearCache();
        $cacheData = Yii::$app->cache->get(Yii::$app->setting->cacheKey);
        $this->assertTrue(!$cacheData);
    }

    public function settings()
    {
        return [
            ['file.name', 'title'],
            ['desc', 'this is a test', 'file'],
            ['url', 'https://www.google.com']
        ];
    }

}
