<?php

namespace App\Providers;

use App\Models\AdminUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\User as UserContract;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CustomUserProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \stdClass|null
     */
    public function retrieveById($identifier)
    {
        // Consulta directa a la base de datos
        return DB::table('admin_users')->where('id', $identifier)->first();
    }

    /**
     * Retrieve a user by their unique identifier and password.
     *
     * @param  array  $credentials
     * @return \stdClass|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // Consulta directa a la base de datos
        $username = $credentials['username'] ?? null;
        $user = DB::table('admin_users')->where('username', $username)->first();
        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \stdClass  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials($user, array $credentials)
    {
        return AdminUser::where('username', $credentials['username'])
                        ->exists();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \stdClass|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // Implementación opcional si utilizas tokens de "remember me"
        return null;
    }

    /**
     * Update the "remember me" token for the given user.
     *
     * @param  \Illuminate\Contracts\Auth\User  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Implementar si usas remember tokens
        DB::table('admin_users')->where('id', $user->getAuthIdentifier())->update(['remember_token' => $token]);
    }
}
