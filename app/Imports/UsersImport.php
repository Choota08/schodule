<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class UsersImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    use SkipsFailures;

    protected string $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {

            $user = User::create([
                'name'      => $row['name'],
                'kode_user' => $row['kode_user'],
                'role'      => $this->role,
                'password'  => Hash::make($row['password']),
            ]);

            // Auto create relasi
            if ($this->role === 'student') {
                $user->student()->create([]);
            }

            if ($this->role === 'teacher') {
                $user->teacher()->create([]);
            }

            return $user;
        });
    }

    public function rules(): array
    {
        return [
            '*.name'      => 'required|string|max:255',
            '*.kode_user' => 'required|string|unique:users,kode_user',
            '*.password'  => 'required|string|min:6',
        ];
    }
}
