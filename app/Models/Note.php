<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $table = 'notes';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable =[
        'title',
        'content',
        'user_id'
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(Note::class, 'user_id', 'id');
    }
}


