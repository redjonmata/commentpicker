<?php
namespace App\Contracts;

interface Exportable
{

    public function setRequestData(array $requestData);

    public function export(): void;

}