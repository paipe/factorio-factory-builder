<?php


namespace App\Base;


class Context
{

    protected $data;

    /**
     * @param array $data
     * @throws \Exception
     */
    public function fill(array $data)
    {
        if (!empty($this->data)) {
            throw new \Exception('Context is not empty!');
        }

        $this->data = $data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

}