<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model implements Authenticatable
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;
    protected $guarded = ['id'];
    protected $fillable = [
        'email',
        'username',
        'password',
        'token'
    ];

    public function notes(): HasMany{
        return $this->hasMany(Note::class, 'user_id', 'id');
    }

    function getAuthIdentifierName(){
        return 'email';
    }
    function getAuthIdentifier(){
        return $this->email;
    }
    function getAuthPassword(){
        return $this->password;
    }
    function getRememberToken(){
        return $this->token;
    }
    function setRememberToken($value){
        $this->token = $value;
    }
    function getRememberTokenName(){
        return 'token';
    }
}
