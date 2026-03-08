<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Subject;
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

            if ($this->role === 'teacher') {

                $kodeUser = trim($row['id_pengajar'] ?? '');
                $name = trim($row['nama_pengajar'] ?? '');
                $password = (string) ($row['password_default'] ?? '123456');
                $subjectInput = trim($row['mapel'] ?? '');

            } else {

                $kodeUser = trim($row['id_siswa'] ?? '');
                $name = trim($row['nama_siswa'] ?? '');
                $password = (string) ($row['password_default'] ?? '123456');
                $subjectInput = null;
            }

            if (!$kodeUser) {
                return null;
            }

            $user = User::create([
                'kode_user' => $kodeUser,
                'name'      => $name,
                'role'      => $this->role,
                'password'  => Hash::make($password),
            ]);

            if ($this->role === 'student') {

                $user->student()->create([
                    'year' => now()->year
                ]);
            }

            if ($this->role === 'teacher') {

                $subject = null;

                if ($subjectInput) {

                    $normalized = strtolower($subjectInput);

                    $subject = Subject::whereRaw(
                        'LOWER(TRIM(name)) = ?',
                        [$normalized]
                    )->first();

                    if (!$subject) {
                        $subject = Subject::create([
                            'name' => $subjectInput
                        ]);
                    }
                }

                $user->teacher()->create([
                    'subject_id' => $subject?->id
                ]);
            }

            return $user;
        });
    }

    public function rules(): array
    {
        if ($this->role === 'teacher') {

            return [
                '*.id_pengajar' => 'required|string|distinct|unique:users,kode_user',
                '*.nama_pengajar' => 'required|string|max:255',
                '*.password_default' => 'nullable|min:3',
                '*.mapel' => 'required|string'
            ];
        }

        return [
            '*.id_siswa' => 'required|string|distinct|unique:users,kode_user',
            '*.nama_siswa' => 'required|string|max:255',
            '*.password_default' => 'nullable|min:3'
        ];
    }
}
