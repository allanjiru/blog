<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title','featured_image','category_id','content','slug','user_id'];


    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function tags(){
    	return $this->belongsToMany('App\Tag');
    }

    public function user(){

    	return $this->belongsTo('App\User');
    }
}
