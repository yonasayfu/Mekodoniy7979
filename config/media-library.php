<?php

return [
    /*
     * The disk on which to store added files and derived images by default.
     * Choose one of the disks you've configured in config/filesystems.php.
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * The maximum file size of an item in bytes.
     * Adding a larger file will result in a Spatie\MediaLibrary\Exceptions\File\FileIsTooBig exception.
     */
    'max_file_size' => 1024 * 1024 * 10,

    /*
     * This queue will be used to generate derived and responsive images.
     * Leave empty to use the default queue.
     */
    'queue_name' => '',

    /*
     * The class name of the media model that should be used.
     */
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * The class name of the model to be used for media conversions.
     */
    'media_conversion_model' => Spatie\MediaLibrary\MediaCollections\Models\MediaConversion::class,

    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * When enabled, media library will force downloads of files to the browser instead of displaying them.
     * This can be useful for preventing browsers from opening PDF files directly, for example.
     */
    'force_download' => false,

    'image_generators' => [
        Spatie\MediaLibrary\Conversions\ImageGenerators\Image::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Webp::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,
        Spatie\MediaLibrary\Conversions\ImageGenerators\Video::class,
    ],

    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),

    'temporary_directory_path' => null,

    'remote' => [
        /*
         * Any extra headers that should be sent when retrieving the remote file.
         */
        'extra_headers' => [
            'User-Agent' => 'Mozilla/5.0',
        ],
    ],

    'responsive_images' => [
        'width_calculator' => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator::class,
        'use_tiny_placeholders' => true,
        'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred::class,
    ],

    /*
    * When using the addMediaFromUrl method you may want to media library to temporary store the file.
    * Here you can specify the base path where the temporary files will be stored.
    */
    'temporary_uploads_directory' => storage_path('media-library/temp'),

    /*
     * This is the class that is responsible for naming generated files.
     */
    'file_namer' => Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class,

    /*
     * The class that is responsible for determining the file's mime type.
     */
    'mime_type_determiner' => Spatie\MediaLibrary\Support\MimeTypes\FinfoMimeTypeDeterminer::class,

    /*
     * List of allowed mime types.
     * An empty array means all mime types are allowed.
     */
    'allowed_mime_types' => [],
];
