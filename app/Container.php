<?php
class Container
{
    private $services = [];
    private $instances = [];

    public function set(string $key, callable $factory): void
    {
        $this->services[$key] = $factory;
    }

    public function get(string $key)
    {
        if (!isset($this->instances[$key])) {
            if (!isset($this->services[$key])) {
                throw new InvalidArgumentException("Service '$key' not found");
            }
            $this->instances[$key] = $this->services[$key]($this);
        }
        return $this->instances[$key];
    }
}
