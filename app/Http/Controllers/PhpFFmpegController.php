<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TempFileController;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Http\Request;

class PhpFFmpegController extends Controller
{
    public static function GetWidth($filePath) {
        $ffmpeg = FFProbe::create([
            'ffmpeg.binaries'  => exec('which ffmpeg'),
            'ffprobe.binaries' => exec('which ffprobe')
        ]);
        $dimension = $ffmpeg->streams($filePath)->videos()->first()->getDimensions();
        return $dimension->getWidth();
    }

    public static function GetHeight($filePath) {
        $ffmpeg = FFProbe::create([
            'ffmpeg.binaries'  => exec('which ffmpeg'),
            'ffprobe.binaries' => exec('which ffprobe')
        ]);
        $dimension = $ffmpeg->streams($filePath)->videos()->first()->getDimensions();
        return $dimension->getHeight();
    }
}
