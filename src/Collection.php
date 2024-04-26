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
}