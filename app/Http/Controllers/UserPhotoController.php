<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserPhotoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'photos' => 'required|file|mimes:zip'
        ]);

        $zip = new ZipArchive;
        $path = $request->file('photos')->getRealPath();

        if ($zip->open($path) === TRUE) {

            for ($i = 0; $i < $zip->numFiles; $i++) {

                $filename = $zip->getNameIndex($i);

                // Skip folder
                if (substr($filename, -1) == '/') {
                    continue;
                }

                $fileContent = $zip->getFromIndex($i);

                // Ambil kode_user dari nama file
                $kodeUser = pathinfo($filename, PATHINFO_FILENAME);

                // Cari user
                $user = User::where('kode_user', $kodeUser)->first();

                if ($user) {

                    $extension = pathinfo($filename, PATHINFO_EXTENSION);

                    $photoName = $kodeUser.'.'.$extension;

                    $photoPath = "profiles/".$photoName;

                    // Simpan foto
                    Storage::disk('public')->put($photoPath, $fileContent);

                    // Update database
                    $user->update([
                        'photo' => $photoPath
                    ]);
                }
            }

            $zip->close();

            return back()->with('success', 'Foto berhasil diupload');
        }

        return back()->with('error', 'ZIP tidak bisa dibuka');
    }
}
