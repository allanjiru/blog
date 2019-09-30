<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Post;
use App\Tag; 

class PagesController extends Controller
{

	public function index(){

	$categories = Category::all();
    $tags = Tag::all();
    $posts = Post::orderBy('created_at','desc')->paginate(6);
    $first_posts = Post::orderBy('created_at','desc')->take(3)->get();
    $title = 'Latest Kenyan News: Number one for news, opinion, sport , celebrity gossip | Rieng';

    return view ('welcome',compact('posts','categories','tags','first_posts','title'));

	}


	public function singlePost($slug){

		$post =Post::where('slug',$slug)->first();
		
		$next_id = Post::where('id', '>', $post->id )->min('id');

		$prev_id = Post::where('id', '<', $post->id )->max('id');


    	



		return view('single')->with('post',$post)
							 ->with('title',$post->title)
							 ->with('next', Post::find($next_id))
							 ->with('prev', Post::find($prev_id))
							 ->with('categories',Category::take(5)->get());
	}

	public function filter_categories($id){

           $category=Category::find($id);

           $posts=Category::find($id)->posts()->paginate(6);

           $title=$category->name;

           $first_posts = Post::orderBy('created_at','desc')->take(3)->get();



           




           return view("category",compact('category','posts','title'))->with('first_posts',$first_posts)
           													->with('categories',Category::take(5)->get());
    }

    public function filter_tags($id){

    	   $tag=Tag::find($id);

           $posts=Tag::find($id)->posts()->paginate(6);

           $title=$tag->name;	

           $first_posts = Post::orderBy('created_at','desc')->take(3)->get();



           return view("tag",compact('tag','posts','title'))->with('first_posts',$first_posts)
           													->with('categories',Category::take(5)->get());


    }

    public function search(){


      $posts = Post::where('title','like', '%' . request('query'). '%')->paginate(6);

      $title = 'Search results';


       return view('results',compact('title'))->with('title','Search results :' . request('query'))
          ->with('query', request('query'))
          ->with('posts', $posts)
          ->with('first_posts', Post::orderBy('created_at','desc')->take(3)->get())
          ->with('categories', Category::take(5)->get());
    }
	

}
