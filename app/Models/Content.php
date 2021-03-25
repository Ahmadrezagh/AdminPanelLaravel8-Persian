<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['score'];

    public function doc(): BelongsTo
    {
        return $this->belongsTo(Doc::class);
    }

    public function score(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function getDictionaryUrlAttribute(): string
    {
        return 'https://cve.mitre.org/cgi-bin/cvename.cgi?name=' . $this->doc->slug;
    }
}
