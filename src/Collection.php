<?php

class Collection
{
    public function __construct(protected array $_data)
    {
    }

    public function all()
    {
        return $this->_data;
    }

    public function average(mixed $index = null)
    {
        return $this->avg($index);
    }

    public function avg(mixed $index = null)
    {
        $aggregate = [];
        foreach ($this->_data as $key => $value) {
            if ($index !== null) {
                if (($value[$index])) {
                    $aggregate[] = $value[$index];
                    continue;
                }
            }
            $aggregate[] = $value;
        }
        return array_sum($aggregate) / count($aggregate);
    }

    public function chunk($size)
    {
        return new self(array_chunk($this->_data, $size));
    }

    public function chunkWhile(callable $c)
    {
        $aggregate = [];
        $buff = [$this->_data[0]];
        for ($i = 1; $i < count($this->_data); $i++) {
            if ($c($this->_data[$i], $i, new self($buff))) {
                $buff[] = $this->_data[$i];
            } else {
                $aggregate[] = $buff;
                $buff = [$this->_data[$i]];
                if (!isset($this->_data[$i + 1])) {
                    $aggregate[] = $buff;
                }
            }
        }
        return new self($aggregate);
    }

    public function last(mixed $c = null)
    {
        if (!$c) {
            if (count($this->_data) === 0)
                return null;
            return $this->_data[count($this->_data) - 1];
        }
        foreach (array_reverse($this->_data) as $key => $value) {
            if ($c($value, $key))
                return $value;
        }
    }

    public function collapse()
    {
        $aggregate = [];

        foreach ($this->_data as $value) {
            if (is_array($value)) {
                $aggregate = array_merge_recursive($aggregate, $value);
            } else
                $aggregate[] = $value;
        }
        return new self($aggregate);
    }

    public function collect()
    {
        return new self($this->_data);
    }

    public function combine(array $arr)
    {
        if (count($this->_data) !== count($arr)) {
            throw new Error("Number of items in arrays are not equal");
        }
        $aggregate = [];
        foreach ($this->_data as $key => $value) {
            $aggregate[$value] = $arr[$key];
        }
        return new self($aggregate);
    }

    public function concat(array $arr)
    {
        $aggregate = $this->_data;

        foreach ($arr as $value) {
            $aggregate[] = $value;
        }
        return new self($aggregate);
    }

    public function contains(...$needle)
    {
        $arr_len = count($needle);
        if ($arr_len > 2 || $arr_len < 1) {
            throw new Error("Collection::contains expects 1 or 2 arguments");
        }
        if ($arr_len === 2) {
            $key = $needle[0];
            $value = $needle[1];
            foreach ($this->_data as $v) {
                if (isset($v[$key]) && $v[$key] == $value)
                    return true;
            }
        }

        if (is_callable($needle[0])) {
            foreach ($this->_data as $key => $value) {
                if ($needle[0]($value, $key)) {
                    return true;
                }
            }
        } else {
            foreach ($this->_data as $value) {
                if ($needle[0] == $value) {
                    return true;
                }
            }
        }
        return false;
    }

    public function containsStrict(...$needle)
    {
        $arr_len = count($needle);
        if ($arr_len > 2 || $arr_len < 1) {
            throw new Error("Collection::contains expects 1 or 2 arguments");
        }
        if ($arr_len === 2) {
            $key = $needle[0];
            $value = $needle[1];
            foreach ($this->_data as $v) {
                if (isset($v[$key]) && $v[$key] === $value)
                    return true;
            }
        }
        if (is_callable($needle[0])) {
            foreach ($this->_data as $key => $value) {
                if ($needle[0]($value, $key)) {
                    return true;
                }
            }
        } else {
            foreach ($this->_data as $value) {
                if ($needle[0] === $value) {
                    return true;
                }
            }
        }
        return false;
    }

    public function doesntContain(...$needle)
    {
        return !$this->contains(...$needle);
    }

    public function containsOneItem()
    {
        return count($this->_data) === 1;
    }

    public function count()
    {
        return count($this->_data);
    }

    public function countBy($closure = null)
    {
        $aggregate = [];

        $closure_passed = is_callable($closure);

        foreach ($this->_data as $item) {
            if ($closure_passed)
                $item = $closure($item);
            if (isset($aggregate[$item]))
                $aggregate[$item]++;
            else
                $aggregate[$item] = 1;
        }
        return new self($aggregate);
    }

    public function dd()
    {
        var_dump($this->_data);
        die();
    }

    public function diff($arr)
    {
        $aggregate = [];
        $diff = array_diff($this->_data, $arr);
        foreach ($diff as $item)
            $aggregate[] = $item;
        return new self($aggregate);
    }

    public function diffAssoc($arr)
    {
        return new self(array_diff_assoc($this->_data, $arr));
    }

    public function diffAssocUsing($arr, $callback)
    {
        return new self(array_diff_uassoc($this->_data, $arr, $callback));
    }

    public function diffKeys($arr)
    {
        return new self(array_diff_key($this->_data, $arr));
    }

    public function dot()
    {
        $go_deeper = false;
        $aggregate = [];

        foreach ($this->_data as $key => $value) {
            if (is_array($value)) {
                $go_deeper = true;
                foreach ($value as $k => $v) {
                    $aggregate[$key . '.' . $k] = $v;
                }
            } else {
                $aggregate[$key] = $value;
            }
        }

        if ($go_deeper)
            return (new self($aggregate))->dot();
        return new self($aggregate);
    }

    public function dump()
    {
        var_dump($this->_data);
    }
}