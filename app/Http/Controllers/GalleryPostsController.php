<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GalleryPost;
use App\Models\GalleryImage;

use Illuminate\Support\Facades\File;

class GalleryPostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = GalleryPost::all()->reverse();

        return view('gallery.index', ['posts'=>$posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gallery.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $alias = GenerateAlias($request->title);

        $newItem = new GalleryPost;
        $newItem->title = $request->title;
        $newItem->description = $request->description;
        $newItem->date = $request->date;
        $newItem->alias = $alias;

        $newItem->status = "inactive";
        if($request->is_published) $newItem->status = "active";

        $newItem->save();

        $sessionSuccessMessage = "Post added successfully. Add some images to it!";
        
        return redirect()->route('gallery.edit', ['gallery'=>$newItem->id])->with('message', $sessionSuccessMessage);
        
    }

    public function upload_file(Request $request)
    {
        $response = [];
        
        //post_id, file
        $post = GalleryPost::findOrFail($request->post_id);

        for ($i = 0; $i < count($_FILES['files']['name']); $i++)
        {
            $image = new GalleryImage;
            $image->src = "";
            $image->description = "";
            $image->alt = "";

            $post->gallery_image()->save($image);

            
            $path = public_path("images/gallery/");
            $extension = strtolower(pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION));
            $imageName = $post->alias."-".$image->id.".".$extension;
            
            move_uploaded_file($_FILES['files']['tmp_name'][$i], public_path("images/gallery/").$imageName);

            $image->src = $imageName;
            

            $image->save();

            $response[] = [
                "id"=>$image->id,
                "src"=>$image->src
            ];
        }
    
        return json_encode($response);
        

    }

    public function delete_file(Request $request)
    {
       //post_id, file
       $galleryPost = GalleryPost::find($request->post_id);
        $galleryImage = $galleryPost->gallery_image()->find($request->image_id);
        
       if(!$galleryImage)
       {
        return json_encode(["ok"=>false]);
       }

       $old_path = public_path("images/gallery/").$galleryImage->src;
        if(File::exists($old_path))
        {
            File::delete($old_path);
        }

        $galleryImage->delete();

        return json_encode(["ok"=>1]);

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
        $post = GalleryPost::findOrFail($id);
        $images = GalleryImage::where('gallery_post_id', $id)->get();
        return view('gallery.edit', ['post'=>$post, 'images'=>$images]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $alias = GenerateAlias($request->title);

        $item = GalleryPost::findOrFail($id);
        $item->title = $request->title;
        $item->description = $request->description;
        $item->date = $request->date;
        $item->alias = $alias;

        $item->status = "inactive";
        if($request->is_published) $item->status = "active";

        $item->save();

        $sessionSuccessMessage = "Gallery Post edited successfully.";
        if($request->save_and_exit)
            return redirect()->route('gallery.index')->with('message', $sessionSuccessMessage);
        if($request->save)
            return redirect()->back()->with('message', $sessionSuccessMessage);

        return redirect()->back()->with('message', "Save ok. Error redirecting.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
