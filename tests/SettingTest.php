<?php

class SettingTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Yii::$app->set('cache', 'yii\caching\FileCache');
        Yii::$app->set('setting', [
            'class' => 'cszchen\setting\Setting',
            'storages' => [
                'db' => [
                    'class' => 'cszchen\setting\DbStorage',
                    'sections' => ['db', 'web'],
                ],
                'file' => [
                    'class' => 'cszchen\setting\FileStorage',
                    'format' => 'array',
                    'sections' => ['file', '']
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
        $name = Yii::$app->setting->get('file.path');
        $this->assertEquals($name, 'path/to/me');

        $url = Yii::$app->setting->get('url');
        $this->assertEquals($url, 'https://www.baidu.com');
    }

    public function testUpdate()
    {
        $success = Yii::$app->setting->set('file.path', 'new path');
        $this->assertTrue($success);

        $name = Yii::$app->setting->get('file.path');
        $this->assertEquals($name, 'new path');
    }

    public function testGetSection()
    {
        $fileVar = Yii::$app->setting->get('file.*');
        $this->assertCount(1, $fileVar);
        $this->assertArrayHasKey('path', $fileVar);
        $this->assertArrayNotHasKey('desc', $fileVar);
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
    
    public function testSetSection()
    {
        $package = [
            'name' => 'cszchen/yii2-setting',
            'desc' => ''
        ];
        $success = Yii::$app->setting->setSection('package', $package);
        $this->assertTrue(!$success);

        Yii::$app->set('setting', 'cszchen\setting\Setting');
        $success = Yii::$app->setting->setSection('package', $package);
        $this->assertTrue($success);

        $this->assertEquals($package, Yii::$app->setting->get('package.*'));
    }

    public function settings()
    {
        return [
            ['file.path', 'path/to/me'],
            ['host', 'localhost', 'db'],
            ['url', 'https://www.baidu.com']
        ];
    }

}
