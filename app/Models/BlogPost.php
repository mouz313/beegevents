<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['title', 'slug', 'excerpt', 'content', 'featured_image', 'author', 'is_published', 'published_at'])]
class BlogPost extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getExcerptAttribute($value): string
    {
        return $value ?: Str::limit(strip_tags($this->content), 200);
    }
}
