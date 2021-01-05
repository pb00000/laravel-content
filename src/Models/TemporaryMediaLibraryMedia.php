<?php

namespace ProtoneMedia\LaravelContent\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TemporaryMediaLibraryMedia extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'temporary_media';

    public $timestamps = false;
}
