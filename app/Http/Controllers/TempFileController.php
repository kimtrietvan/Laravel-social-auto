<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TempFileController extends Controller
{
    public static function SaveAs(string $name, $file) {
        return Storage::disk("temp")->putFileAs('', $file, $name);
    }

    public static function GetRootPath() {
        return Storage::disk("temp")->path('');
    }

    public static function GetFile(string $name) {
        return Storage::disk("temp")->get($name);
    }

    public static function GetPath(string $name) {
        return Storage::disk("temp")->path($name);
    }
    public static function DeleteFile(string $name) {
        if (Storage::disk("temp")->exists($name)) {
            Storage::disk("temp")->delete($name);
        }
    }
}
