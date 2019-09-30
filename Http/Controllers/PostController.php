<?php

namespace App\Http\Controllers;

use App\Post;
use App\Category;
use App\Tag;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Session;
use DB;
use Mail;

class PostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $posts = Post::where(auth()->user()->id,user_id)->paginate(5);


        if(auth()->user()->admin==1){
            $posts=Post::paginate(5);
        }
        else{
             $posts = DB::table('posts')->where('user_id', auth()->user()->id)->paginate(5);
        }

        
        
        
        return view ('admin.posts.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        if($categories->count() == 0)

        {
            Session::flash('info','You must have a category before attempting to create a post.');
            return redirect()->back();
        }

        if($tags->count() == 0)

        {
            Session::flash('info','You must have a tag before attempting to create a post.');
            return redirect()->back();
        }


        return view('admin.posts.create',compact('categories','tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[

            'title' => 'required|max:255',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'category_id' => 'required',
            'content' => 'required',
            'tags' => 'required'


        ]);

        $post = new Post;

        $post_image = $request->featured_image;

        $post_image_new_name = time() . $post_image->getClientOriginalName();


        $image_resize = Image::make($post_image->getRealPath());              
        $image_resize->resize(800, 600);
        $image_resize->save(public_path('uploads/posts/' .$post_image_new_name));

        $post = Post::create([

            'title' => $request->title,
            'featured_image' => 'uploads/posts/' . $post_image_new_name,
            'category_id' => $request->category_id,
            'content' => $request->content,
            'user_id' => auth()->user()->id,
            'slug' => str_slug($request->title)

        ]);

        $post->tags()->attach($request->tags);

        $post->save();

        Session::flash('success', 'Post created.');

       // $data= array(
         //   'title' => $request->title,
         //   'featured_image' => 'uploads/posts/' . $post_image_new_name,
         //   'content' => $request->content,
         //   'user' => $post->user->name

       // );

      //  Mail::to('allannjiru1@gmail.com')->send(new \TukoRieng\Mail\PostNotification($data));

        return redirect()->route('posts.index');
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \TukoRieng\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \TukoRieng\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $post = Post::find($id);

        return view('admin.posts.edit',compact('categories','post','tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \TukoRieng\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $post = Post::find($id);


        $this->validate($request,[

            'title' => 'required|max:255',
            'category_id' => 'required',
            'content' => 'required',
            'tags' => 'required'

        ]);

        

        if($request->hasFile('featured_image'))
        {
        $post_image = $request->featured_image;

        $post_image_new_name = time() . $post_image->getClientOriginalName();


        $image_resize = Image::make($post_image->getRealPath());              
        $image_resize->resize(800, 600);
        $image_resize->save(public_path('uploads/posts/' .$post_image_new_name));

        $post->featured_image = 'uploads/posts/' . $post_image_new_name;
        }

        $post->title = $request->title;
        $post->category_id = $request->category_id;
        $post->content = $request->content;

        $post->save();

        Session::flash('success', 'Post updated successfully.');

        return redirect()->route('posts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \TukoRieng\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        $post = Post::find($id);

        if(file_exists($post->featured_image))
        {
            unlink($post->featured_image);
        }

        $post->delete();


        Session::flash('success','The post was just deleted');


        return redirect()->back();
    }
}
