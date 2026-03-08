<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controller Dasar Aplikasi
 *
 * Ini adalah tas ajaib yang berisi 2 alat siap pakai:
 * 1. AuthorizesRequests = Alat pembaca hati untuk cek "boleh atau tidak boleh"
 * 2. ValidatesRequests = Alat pemeriksa barang untuk cek "data benar atau salah"
 *
 * Setiap controller yang dibuat di aplikasi ini akan mewarisi tas ajaib ini,
 * sehingga bisa langsung pakai kedua alat tersebut tanpa perlu buat ulang.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * AuthorizesRequests
     * Alat untuk mengecek apakah pengguna diizinkan melakukan aksi atau tidak
     * Contoh: boleh tidak pengguna menghapus data? boleh tidak guru mengubah jadwal?
     */

    /**
     * ValidatesRequests
     * Alat untuk mengecek apakah data yang dikirim pengguna itu benar atau salah
     * Contoh: email harus format email, nama tidak boleh kosong, umur harus angka
     */
}

