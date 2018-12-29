<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravelista\Comments\Commentable;

class File extends Model
{
    use Commentable;
    # This property!
    protected $fillable = [
        'title',
        'subject_id',
        'level_id',
        'category_id',
        'branch_id',
        'filename',
        'user_id',
        'upvotes',
        'downvotes',
        'description',
        'keywords'
    ];
    public function subject()
    {
        return $this->BelongsTo('App\Subject');
    }
    public function level()
    {
        return $this->BelongsTo('App\Level');
    }
    public function branch()
    {
        return $this->BelongsTo('App\Branch');
    }
    public function category()
    {
        return $this->BelongsTo('App\Category');
    }
    public function user()
    {
        return $this->BelongsTo('App\User');
    }
    public function subjectId()
    {
        return $this->BelongsTo('App\Subject');
    }
    public function levelId()
    {
        return $this->BelongsTo('App\Level');
    }
    public function branchId()
    {
        return $this->BelongsTo('App\Branch');
    }
    public function categoryId()
    {
        return $this->BelongsTo('App\Category');
    }
    public function userId()
    {
        return $this->BelongsTo('App\User');
    }
}
