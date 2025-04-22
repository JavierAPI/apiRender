<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    public function download(Request $request)
    {
        $url = $request->input('url');
        $format = strtolower($request->input('format', 'mp4'));
        $quality = $request->input('quality', '720');

        // Validación y creación de directorio temporal
        if (empty($url)) {
            return response()->json(['status' => 'error', 'message' => 'URL requerida']);
        }

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Nombres de archivos temporales
        $filename = 'download_' . uniqid() . '.' . $format;
        $filepath = $tempDir . '/' . $filename;
        $tempVideo = $tempDir . '/video_' . uniqid() . '.mp4';
        $tempAudio = $tempDir . '/audio_' . uniqid() . '.m4a';

        try {
            if ($format === 'mp3') {
                // Descarga solo audio
                $command = "yt-dlp -x --audio-format mp3 -o \"$filepath\" \"$url\" 2>&1";
            } else {
                // Descarga video + audio por separado y combina
                $command = "yt-dlp -f \"bestvideo[height<={$quality}]\" -o \"{$tempVideo}\" \"{$url}\" && " .
                    "yt-dlp -f \"bestaudio\" -o \"{$tempAudio}\" \"{$url}\" && " .
                    "ffmpeg -i \"{$tempVideo}\" -i \"{$tempAudio}\" -c:v copy -c:a aac -shortest \"{$filepath}\" && " .
                    "rm -f \"{$tempVideo}\" \"{$tempAudio}\" 2>&1";
            }

            $output = shell_exec($command);
            
            // Verifica si el archivo final existe
            if (file_exists($filepath)) {
                // Prepara la respuesta para descarga
                return response()->download($filepath, $filename, [
                    'Content-Type' => 'application/octet-stream',
                ])->deleteFileAfterSend(true);
            } else {
                throw new \Exception("Error al generar el archivo: " . $output);
            }
        } catch (\Exception $e) {
            // Limpieza de archivos temporales en caso de error
            @unlink($tempVideo);
            @unlink($tempAudio);
            @unlink($filepath);

            return response()->json([
                'status' => 'error',
                'message' => 'Error al descargar el video',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
