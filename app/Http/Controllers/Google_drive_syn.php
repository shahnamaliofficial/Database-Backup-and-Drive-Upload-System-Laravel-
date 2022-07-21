<?php

namespace App\Http\Controllers;

use App\Http\Controllers\GoogleDriveApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class Google_drive_syn extends Controller
{

    public function googledrivesynfun(Request $request)
    {
        $GoogleDriveApi = new GoogleDriveApi;
        $zip = new ZipArchive();
        // scan for database file
        $dbzipfile = scandir(storage_path("app/Laravel"));
        foreach ($dbzipfile as $key => $value) {
            $path = realpath(storage_path("app/Laravel") . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $dbzipfolder[] = $value;
            }
        }
        $status = $zip->open(storage_path("app/Laravel/".$dbzipfolder[0]));
        if ($status !== true) {
            throw new \Exception($status);
        } else {
            $storageDestinationPath = storage_path("app/public/unzip/");
            $zip->extractTo($storageDestinationPath);
            $zip->close();
        }
        $file_content2 =  storage_path("app/public/unzip/db-dumps/");
        $files = scandir($file_content2);
        foreach ($files as $key => $value) {
            $path = realpath($file_content2 . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $value;
            }
        }
        $file_content =  $results[0];
        $mime_type = mime_content_type($file_content2 . $file_content);
        $file_content3 = file_get_contents($file_content2 . $file_content);
        // Get the access token
        if (!empty($_SESSION['google_access_token'])) {
            $access_token = $_SESSION['google_access_token'];
        } else {
            // GOOGLE_CLIENT_ID, REDIRECT_URI, GOOGLE_CLIENT_SECRET, $_GET['code']
            $data = $GoogleDriveApi->GetAccessToken(config('customeenv.GOOGLE_CLIENT_ID'), config('customeenv.REDIRECT_URI'), config('customeenv.GOOGLE_CLIENT_SECRET'), $request->get('code'));
            $access_token = $data['access_token'];
            $_SESSION['google_access_token'] = $access_token;
        }
        //  Upload file to Google drive
        $drive_file_id = $GoogleDriveApi->UploadFileToDrive($access_token, $file_content3, $mime_type);
        $file_meta = array(
            'name' => date('Y-m-d t-m-s')."_".basename($file_content)
        );
         // Update file metadata in Google drive
        $drive_file_meta = $GoogleDriveApi->UpdateFileMeta($access_token, $drive_file_id, $file_meta);
        return redirect()->route('/');

    }
}
