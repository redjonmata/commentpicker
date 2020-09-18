<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

interface Searchable
{

    public function __construct(Builder $collection, array $requestData, User $loggedUser);

    public function search(): Builder;

}