<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{User,ForumTopic,ForumCategory,ForumMessage,ForumView};
use App\App;

class ForumTheme extends Model{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * автор темы
     */
    public function autor()
    {
        return $this->hasOne(User::class, 'id', 'id_autor');
    }
    /**
     * пользователь оставивший последнее сообщение
     */
    public function lastUser()
    {
        return $this->hasOne(User::class, 'id', 'id_last');
    }
    /**
     * топик темы
     */
    public function topic()
    {
        return $this->hasOne(ForumTopic::class, 'id', 'id_topic');
    }
    /**
     * категория темы
     */
    public function category()
    {
        return $this->hasOne(ForumCategory::class, 'id', 'id_category');
    }
    /**
     * сообщения темы
     */
    public function messages()
    {
        return $this->hasMany(ForumMessage::class, 'id_theme');
    }
    /**
     * просмотры
     */
    public function views()
    {
        return $this->hasMany(ForumView::class, 'id_theme');
    }
    /**
     * количество сообщений с учетом прав на просмотр
     */
    public function getCountMessagesAttribute(){
        return $this->messages->where('group_show', '<=', App::user()->group)->count();

        // return $this->whereHas('messages', function ($query) {
        //     $query->group(\App\App::user());
        // })->count();
    }
    /**
     * количество просмотров
     */
    public function getCountViewsAttribute(){
        return $this->views->count();
    }

    /**
     * данные пользователей написавших первое и последнее сообщение
     */
    public function getLastUsersAttribute(){
        return ($this->autor->id != $this->lastUser->id ? $this->autor->login . '/' . $this->lastUser->login : $this->autor->login);
    }
    /**
     * иконка темы
     */
    public function getIconAttribute(){
        $is_open = (int)($this->attributes['group_write'] <= $this->topic->group_write);
        return "forum.theme.{$this->attributes['top']}.{$is_open}";
    }

    public function scopeGroup($query, $user)
    {
        return $query->where('group_show', '<=', $user->group)
            ->whereHas('topic', function ($query) use ($user) {
                $query->group($user);
        });
    }
}