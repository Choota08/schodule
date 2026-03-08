<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class UserPhotoController extends Controller
{
    /**
     * Upload user photos from a ZIP file
     * Extracts photos and assigns them to users based on filename (kode_user)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'photos' => 'required|file|mimes:zip',
        ]);

        $zip = new ZipArchive;
        $path = $request->file('photos')->getRealPath();

        if ($zip->open($path) !== true) {
            return response()->json([
                'message' => 'Failed to open ZIP file',
            ], 422);
        }

        $uploadedCount = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            // Skip directories
            if (substr($filename, -1) === '/') {
                continue;
            }

            $fileContent = $zip->getFromIndex($i);
            $kodeUser = pathinfo($filename, PATHINFO_FILENAME);
            $user = User::where('kode_user', $kodeUser)->first();

            if ($user) {
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $photoName = $kodeUser . '.' . $extension;
                $photoPath = 'profiles/' . $photoName;

                Storage::disk('public')->put($photoPath, $fileContent);
                $user->update(['profile_photo' => $photoPath]);
                $uploadedCount++;
            }
        }

        $zip->close();

        return response()->json([
            'message' => 'Photos uploaded successfully',
            'uploaded_count' => $uploadedCount,
        ]);
    }
}
