<?php
include_once(__DIR__ . '/../vendor/autoload.php');

use YouTube\YouTubeDownloader;
use YouTube\Exception\YouTubeException;
use Curl\Client;
use CurlDownloader\CurlDownloader;

$youtube = new YouTubeDownloader();


try {
    $downloadOptions = $youtube->getDownloadLinks("https://www.youtube.com/watch?v=W7pFDo6Cg6k");

    if ($downloadOptions->getAllFormats()) {
        $link = $downloadOptions->getFirstCombinedFormat()->url;
    } else {
        echo 'No links found';
    }
} catch (YouTubeException $e) {
    echo 'Something went wrong: ' . $e->getMessage();
}


$browser = new Client();
$downloader = new CurlDownloader($browser);

$response = $downloader->download($link, function ($filename) {
    return $filename;
});

if ($response->status == 200) {
    // 28,851,928 bytes downloaded in 20.041231 seconds
    echo number_format($response->info->size_download) . ' bytes downloaded in ' . $response->info->total_time . ' seconds';
}

// converting

$ffmpeg = FFMpeg\FFMpeg::create();

$ffmpeg = FFMpeg\FFMpeg::create(array(
    'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
    'ffprobe.binaries' => '/usr/bin/ffprobe',
    'timeout'          => 3600, // The timeout for the underlying process
    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
));

// Open your video file
$video = $ffmpeg->open('videoplayback.3gp');
$video->save(new FFMpeg\Format\Video\X264(), 'video.mp4');

// Set an audio format
$audio_format = new FFMpeg\Format\Audio\Mp3();

// Extract the audio into a new file as mp3
$video->save($audio_format, 'audio.mp3');


// Set the audio file
$audio = $ffmpeg->open('audio.mp3');

// Create the waveform
$waveform = $audio->waveform();
$waveform->save('waveform.png');

