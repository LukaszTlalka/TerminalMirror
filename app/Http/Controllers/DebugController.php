<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage as FileStorage;

class DebugController extends Controller
{
    public function files()
    {
        return view('debug.files');
    }

    public function filesData()
    {
        $dirPath = storage_path('console-sessions');

        $fileContents = [];

        if ($handle = opendir($dirPath)) {
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                if (is_file($dirPath."/".$entry)) {
                    $fileContents[$entry] = file_get_contents($dirPath."/".$entry);
                }
            }

            closedir($handle);
        }

        return view('debug.files-data', compact('fileContents'));
    }
}
