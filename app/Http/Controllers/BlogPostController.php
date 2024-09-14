<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogTag;


use Illuminate\Support\Facades\File;

class BlogPostController extends Controller
{
    public function index()
    {
        $blog_posts = BlogPost::orderBy('ordering', 'desc')->get();
        return view('blog.index', ['blog_posts'=>$blog_posts]);
    }

    public function api_get($limit = null)
    {
        $blog_posts = null;
        if($limit == null)
            $blog_posts = BlogPost::where('status', 'active')->orderBy('ordering', 'desc')->get();
        else
            $blog_posts = BlogPost::where('status', 'active')->orderBy('ordering', 'desc')->take($limit)->get();

        foreach($blog_posts as $item)
        {
            $item->image = 'http://'.$_SERVER['HTTP_HOST']."/images/blog/".$item->image;
            $item->tags = BlogTag::where('blog_post_id', $item->id)->orderBy("tag_name", 'asc')->get();
        }

        return response()->json(['data'=>$blog_posts, 'count'=>count($blog_posts)]);

    }

    public function reorder(Request $request)
    {
        $blog_posts = BlogPost::orderBy('ordering', 'asc')->get();

        $blog_post_ids = [];

        for ($i = 0; $i < count($blog_posts); $i++) {
            $blog_post_ids[] = $blog_posts[$i]->id;
        }

        $move_from_position = BlogPost::find($request->id)->ordering;
        $move_before_position = $request->new_order;

        $do_minus_one = $move_from_position < $move_before_position;


        $element = array_splice($blog_post_ids, $move_from_position -1 , 1);

        array_splice($blog_post_ids, $move_before_position - 1 - ($do_minus_one ? 1 : 0), 0, $element);

        for($i = 0; $i < count($blog_post_ids); $i++)
        {
            $blog_post = BlogPost::find($blog_post_ids[$i]);
            $blog_post->ordering = $i + 1;
            $blog_post->save();
        }

        return redirect()->back();
    }

    /**
     * Retrieves a paginated JSON response of active projects, 
     * with optional limits and page numbers for pagination.
     *
     * @param int|null $limit The number of results to return per page.
     * @param int|null $page The page number to retrieve.
     * @throws Some_Exception_Class If an error occurs during pagination.
     * @return \Illuminate\Http\JsonResponse The paginated projects and total pages as JSON.
     */
    public function api_get_paginated($page, $limit)
    {
        $blog_posts = BlogPost::where('status', 'active')->orderBy('ordering', 'desc');
        $pages = ceil($blog_posts->count() / $limit);
        $blog_posts = $blog_posts->offset(($page-1) * $limit);
        $blog_posts = $blog_posts->take($limit);
        $blog_posts = $blog_posts->get();

        foreach($blog_posts as $item)
        {
            $item->image = 'http://'.$_SERVER['HTTP_HOST']."/images/blog/".$item->image;
            $item->tags = BlogTag::where('blog_post_id', $item->id)->orderBy("tag_name", 'asc')->get();
        }


        return response()->json(['data'=>$blog_posts, 'pages'=>$pages]);

    }

    public function create()
    {
        return view('blog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $alias = GenerateAlias($request->title);

        $imageName = "";
        $imageExtension = "";
        $imageFileName = "";
        if($request->copy_image != null)
        {
            $imageFileName = $request->copy_image;
            $imageExtension = explode(".", $imageFileName)[1];
            $imageName = explode(".", $imageFileName)[0];
        }
        else
        {
            $imageName = "blog-".$alias;
            $imageExtension = $request->image->extension();
            $imageFileName = $imageName. "." . $imageExtension;
        }
        $index = 1;
        while(File::exists(public_path("images/blog/".$imageFileName)))
        {
            $imageFileName = $imageName . "-" . $index . "." . $imageExtension;
            $index += 1;
        }

        if($request->copy_image != null)
        {
            copy(public_path("images/blog/".$request->copy_image), public_path("images/blog/".$imageFileName));
        }
        else
        {
            $request->image->move(public_path("images/blog"), $imageFileName);
        }


        $newBlogPost = new BlogPost;
        $newBlogPost->title = $request->title;
        $newBlogPost->header = $request->header;
        $newBlogPost->content = $request->content;
        $newBlogPost->image = $imageFileName;
        $newBlogPost->date = $request->date;
        $newBlogPost->alias = $alias;

        $newBlogPost->ordering = (BlogPost::orderBy('ordering', 'desc')->first()->ordering??0)+ 1;

        $newBlogPost->status = "inactive";
        if($request->is_published) $newBlogPost->status = "active";

        

        $newBlogPost->save();

        $sessionSuccessMessage = "Blog Post added successfully.";
        if($request->copy_image != null)
        {
            $sessionSuccessMessage = "Blog Post copied successfully.";
            return redirect()->route('blog.index')->with('message', $sessionSuccessMessage);
        }
        if($request->save_and_exit)
            return redirect()->route('blog.index')->with('message', $sessionSuccessMessage);
        if($request->save_and_new)
            return redirect()->back()->with('message', $sessionSuccessMessage);

        return redirect()->back()->with('message', "Save ok. Error redirecting.");
    }

    public function add_tag(Request $request)
    {
        $blog_post = BlogPost::findOrFail($request->id);

        $tag = new BlogTag;
        $tag->tag_name = GenerateAlias($request->tag);
        $blog_post->tag()->save($tag);
        $tag->save();

        return json_encode( [
            "tag_name"=>$tag->tag_name,
            "id"=>$tag->id,
        ]);
    }

    public function remove_tag(Request $request)
    {
        $tag = BlogTag::find($request->id);

        if($tag)
            $tag->delete();

        return;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $blog_post = BlogPost::findOrFail($id);
        return view('blog.edit', ['blog'=>$blog_post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $blog_post = BlogPost::findOrFail($id);

        $alias = GenerateAlias($request->title);

        if($request->save_as_copy)
        {
            $request->copy_image = $blog_post->image;
            $request->is_published = 0;
            $this->store($request);
            return redirect()->route('blog.index');
        }

        $imageName = $blog_post->image;
        if($request->image != null)
        {
            $old_path = public_path("images/blog/".$imageName);
            if(File::exists($old_path))
            {
                File::delete($old_path);
            }
            $imageName = "blog-" . $alias . "." . $request->image->extension();
            $index = 1;
            while(File::exists(public_path("images/blog/".$imageName)))
            {
                $imageName = "blog-" . $alias . "-" . $index . "." . $request->image->extension();
                $index += 1;
            }
            $request->image->move(public_path("images/blog/"), $imageName);
        }

        $blog_post->title = $request->title;
        $blog_post->content = $request->content;
        $blog_post->header = $request->header;
        $blog_post->image = $imageName;
        $blog_post->date = $request->date;
        $blog_post->alias = $alias;
        $blog_post->status = "inactive";
        if($request->is_published) $blog_post->status = "active";

        $blog_post->save();

        $sessionSuccessMessage = "Blog Post edited successfully.";
        if($request->save_and_exit)
            return redirect()->route('blog.index')->with('message', $sessionSuccessMessage);
        if($request->save)
            return redirect()->back()->with('message', $sessionSuccessMessage);

        return redirect()->back()->with('message', "Save ok. Error redirecting.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog_post = BlogPost::findOrFail($id);

        $old_path = public_path("images/blog/".$blog_post->image);
        if(File::exists($old_path))
        {
            File::delete($old_path);
        }


        $blog_post->tag()->delete();
        
        $blog_post->delete();

        $blog_posts = BlogPost::orderBy('ordering', 'asc')->get();
        
        for($i = 0; $i < count($blog_posts); $i++)
        {
            $blog_posts[$i]->ordering = $i + 1;
            $blog_posts[$i]->save();
        }

        return redirect('blog');
    }
}
