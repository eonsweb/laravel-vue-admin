<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostMeta extends Model
{
    use HasFactory;

    protected $table = 'post_meta';

    protected $fillable = ['post_id', 'meta_key', 'meta_value'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
