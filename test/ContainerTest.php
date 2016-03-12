<?php
class ContainerTest  extends PHPUnit_Framework_TestCase{
    public function setUp()
    {
        $this->sample = array(
            'integer' => 1,
            'string' => 'string value',
            'array' => array(
                'value1',
                'value2' => 'value2 value'
            ),
            'object' => (object)array(
                'value1',
                'value2' => 'value2 value'
            ),
            'closure' => function() {
                return true;
            },
        );
    }

    public function testCheckConstruct(){
        $container = new \Kijtra\Container($this->sample);
        foreach($this->sample as $key => $val) {
            if ('array' == $key || 'object' == $key) {
                continue;
            }
            $this->assertContains($val, $container);
        }

        $this->assertTrue($container->array instanceof \Kijtra\Container);
        $this->assertTrue($container->object instanceof \Kijtra\Container);
    }

    public function testSetGetValue(){
        $container = new \Kijtra\Container;
        $target = $this->sample['object'];

        $container->value1 = $target;
        $this->assertSame($container->value1, $target);

        $container['value2'] = $target;
        $this->assertSame($container['value2'], $target);

        $this->assertSame($container->value1, $container['value2']);
    }

    public function testSetGetValueHirarchical(){
        $container = new \Kijtra\Container;
        $target = $this->sample['array']['value2'];

        $container->array->value2 = $target;
        $this->assertSame($container->array->value2, $target);

        $container['array']['value2'] = $target;
        $this->assertSame($container['array']['value2'], $target);
    }

    public function test_name_Method(){
        $container = new \Kijtra\Container($this->sample);
        $this->assertSame('value1', $container->array->value1->name());
        $this->assertSame('value1', $container->object->value1->name());
    }

    public function test_parent_Method(){
        $container = new \Kijtra\Container($this->sample);
        $this->assertSame($container->array, $container->array->value1->parent());
    }

    public function test_has_Method(){
        $container = new \Kijtra\Container($this->sample);
        foreach($this->sample as $key => $val) {
            $this->assertTrue($container->has($key));
        }
    }

    public function test_arr_Method(){
        $sample = $this->sample;
        unset($sample['object']);
        $container = new \Kijtra\Container($sample);
        $this->assertSame($sample, $container->arr());
        $this->assertSame($sample['array'], $container->array->arr());
    }

    public function test_count_Method(){
        $container = new \Kijtra\Container($this->sample);
        $this->assertSame(count($this->sample), $container->count());
    }

    public function testIsSet(){
        $container = new \Kijtra\Container($this->sample);
        $this->assertTrue(isset($container->array));
        $this->assertTrue(isset($container['array']));
        $this->assertFalse(isset($container->none));
        $this->assertFalse(isset($container['none']));
        $this->assertEmpty($container->none);
        $this->assertEmpty($container['none']);
        $this->assertEmpty(new \Kijtra\Container);
    }

    public function testAddFlatArray(){
        $sample = array();
        $sample[] = 'value0';
        $sample[] = 'value1';

        $container1 = new \Kijtra\Container;
        $container1[] = 'value0';
        $container1[] = 'value1';

        $container2 = new \Kijtra\Container;
        $container2[] = 'value0';
        $container2[] = 'value1';

        $this->assertSame($sample, $container1->arr());
        $this->assertSame($sample, $container2->arr());
    }

    public function testToString(){
        $container = new \Kijtra\Container;
        ob_start();
        echo $container;
        $content = ob_get_clean();

        $this->assertSame('', $content);
    }

    public function testInvalidName(){
        $sample = array();
        $sample['valid'] = 'valid';

        $container = new \Kijtra\Container;
        $container[(object)array()] = 'invalid';
        $container[array()] = 'invalid';
        $container['valid'] = 'valid';
        $this->assertSame($sample, $container->arr());
    }

    public function testCheckStorageStacked(){
        // Get Method count in current class
        $current = new ReflectionClass(__CLASS__);
        $count = 0;
        foreach ($current->getMethods() as $val) {
            if ($val->class == __CLASS__) {
                $count++;
            }
        }

        $container = new \Kijtra\Container;
        $this->assertGreaterThan($count, $container->storage()->count());
    }
}
