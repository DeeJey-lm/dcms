<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{ForumCategory,ForumTheme};

class ForumTopic extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function category()
    {
        return $this->hasOne(ForumCategory::class, 'id', 'id_category');
    }

    public function themes()
    {
        return $this->hasMany(ForumTheme::class, 'id_topic', 'id');
    }

    public function scopeGroup($query, $user)
    {
        return $query->where([
            ['theme_view', '1'],
            ['group_show', '<=', $user->group],
        ])->whereHas('category', function ($query) use ($user) {
            $query->group($user);
        });
    }
}