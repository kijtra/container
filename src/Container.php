<?php
namespace Kijtra;

class Container implements \IteratorAggregate, \ArrayAccess, \Countable
{
    private static $storage;
    private static $names = array();
    private static $counter = array();

    public function __construct($data = array())
    {
        if (null === self::$storage) {
            self::$storage = new \SplObjectStorage();
            self::$storage->attach($this, null);
            self::$names[self::$storage->getHash($this)] = '!root';
        }

        if (!empty($data) && is_array($data)) {
            foreach($data as $key => $val) {
                if ($this->isValidName($key)) {
                    if (is_array($val) || $val instanceof \stdClass) {
                        $this->$key = $this->setObject($key, $val);
                    } else {
                        $this->$key = $val;
                    }
                }
            }
        }
    }

    public function name()
    {
        return self::$names[self::$storage->getHash($this)];
    }

    public function parent()
    {
        return self::$storage->offsetGet($this);
    }

    public function has($name)
    {
        return $this->__isset($name);
    }

    public function arr()
    {
        return $this->toArray($this);
    }

    public function count()
    {
        return count(get_object_vars($this));
    }

    public function storage()
    {
        return self::$storage;
    }


    public function __get($name)
    {
        return $this->{$name} = $this->setObject($name);
    }

    public function __set($name, $value)
    {
        if (is_null($name)) {
            $hash = self::$storage->getHash($this);
            if (!array_key_exists($hash, self::$counter)) {
                self::$counter[$hash] = 0;
            }
            $this->{self::$counter[$hash]} = $value;
            self::$counter[$hash]++;
        } elseif ($this->isValidName($name)) {
            if (is_array($value) || $value instanceof \stdClass) {
                $this->$name = $this->setObject($name, $value);
            } else {
                $this->$name = $value;
            }
        }
    }

    public function __isset($name)
    {
        return property_exists($this, $name);
    }

    public function __toString()
    {
        return '';
    }

    public function offsetSet($offset, $value) {
        $this->__set($offset, $value);
    }

    public function offsetExists($offset) {
        return $this->__isset($offset);
    }

    public function offsetUnset($offset) {
        if ($this->__isset($offset)) {
            $target = $this->$offset;
            if ($target instanceof self) {
                self::$storage->offsetUnset($target);
            }
            unset($this->$offset);
        }
    }

    public function offsetGet($offset) {
        if ($this->__isset($offset)) {
            return $this->$offset;
        } else {
            return $this->__get($offset);
        }
    }

    public function getIterator() {
        return new \ArrayIterator($this);
    }


    private function setObject($name, $value = array())
    {
        if ($value instanceof \stdClass) {
            $value = get_object_vars($value);
        }
        $object = new self($value, $name, self::$storage);
        self::$storage->attach($object, $this);
        self::$names[self::$storage->getHash($object)] = $name;
        return $object;
    }

    private function isValidName($value)
    {
        return (null !== $value && !is_array($value) && !is_object($value));
    }

    private function toArray($data)
    {
        if ($data instanceof self) {
            if (!empty($data)) {
                $arr = array();
                foreach(get_object_vars($data) as $key => $val) {
                    $arr[$key] = $this->toArray($val);
                }

                if (!empty($arr)) {
                    return $arr;
                }
            }
        } else {
            return $data;
        }
    }
}
