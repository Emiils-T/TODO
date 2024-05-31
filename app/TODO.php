<?php
namespace App;

use JsonSerializable;

class TODO implements JsonSerializable
{
    private string $name;
    private string $time;
    private string $isDone;

    public function __construct(string $name, string $time, string $isDone)
    {
        $this->name = $name;
        $this->time = $time;
        $this->isDone = $isDone;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function getIsDone(): string
    {
        return $this->isDone;
    }

    public function setIsDone(string $isDone): void
    {
        $this->isDone = $isDone;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'time' => $this->time,
            'isDone' => $this->isDone,
        ];
    }
}
