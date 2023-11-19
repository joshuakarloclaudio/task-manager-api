<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct(
        private User $model
    ) { }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }
}
