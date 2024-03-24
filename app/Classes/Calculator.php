<?php

namespace App\Classes;
use Illuminate\Database\Connection;

abstract class Calculator{
    protected readonly Connection $connection;

    public function __construct(Connection $connection){
        $this->connection = $connection;
    }

    abstract protected function getSQLResponse(): array;
    abstract protected function toArrayFromSQL(array $array): array;
    abstract protected function handleArray(array $array): array;
    abstract public function calculate(): array;
}