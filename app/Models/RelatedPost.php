<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RelatedPost extends Model
{
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'related_posts';

    protected $fillable = ['post_id', 'related_post_id'];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function related()
    {
        return $this->belongsTo(Post::class, 'related_post_id');
    }
}
