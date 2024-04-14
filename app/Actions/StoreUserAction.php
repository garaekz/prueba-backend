<?php

namespace App\Actions;

use App\Models\User;

class StoreUserAction
{
    /**
     * Execute the action to store a user.
     *
     * @param array $data The data to store.
     * @param int|null $id The ID of the user to update (optional).
     * 
     * @return null|array The stored or updated user data or null.
     */
    public function execute(array $data, $id = null)
    {
        if ($id) {
            $user = User::update($id, $data);
        } else {
            $user = User::create($data);
        }

        return $user;
    }
}
