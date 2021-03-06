<?php
  
namespace App\Http\Controllers;
  
use App\Post;
use Illuminate\Http\Request;
  
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest()->paginate(5);
  
        return view('post.index',compact('posts'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('post.create');
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  

 
public function store(Request $request)
    {
        $request->validate([
            'name'    =>  'required',
                'description'     =>  'required',
                'city'     =>  'required',
                'coords'         =>  'required'
        ]);
  
        Post::create($request->all());
   
        return redirect()->route('post.index')
                        ->with('success','Post created successfully.');
    }
  
    /**
     * Display the specified resource.
     *
     * @param  \App\Geomlng  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('post.show',compact('post'));
    }
   
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Geomlng  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('post.edit',compact('post'));
    }
  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Geomlng  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'name'    =>  'required',
                'description'     =>  'required',
                'city'     =>  'required',
                'coords'         =>  'required'

        ]);
  
        $post->update($request->all());
  
        return redirect()->route('post.index')
                        ->with('success','Geomlng updated successfully');
    } 
     
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Geomlng  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
  
        return redirect()->route('post.index')
                        ->with('success','Post deleted successfully');
    }
}