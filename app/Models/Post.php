<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
       'user_id', 'title', 'slug', 'content', 'excerpt',
        'featured_image', 'status', 'is_featured', 'allow_comments',
        'view_count', 'published_at'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
    ];

    //Relationships
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class, 'category_post');
    }

    public function tags(){
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function likedBy()
    {
        return $this->hasMany(Like::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function meta(){
        return $this->hasMany(PostMeta::class);
    }

    public function views(){
        return $this->hasMany(PostView::class);
    }

    public function related(){
        return $this->belongsToMany(
            Post::class,
            'related_posts',
            'post_id',
            'related_post_id'
        );
    }

    public function bookmarkedBy()
    {
        return $this->hasMany(Bookmark::class);
    }

}
