<?php

namespace App\Http\Controllers;

use App\Models\ProjectTag;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use App\Models\Project;


use Illuminate\Support\Facades\File;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::orderBy('ordering', 'desc')->get();
        return view('projects.index', ['projects'=>$projects]);
    }

    public function api_get($limit = null)
    {
        $projects = null;
        try {
            if ($limit == null)
                $projects = Project::where('status', 'active')->orderBy('ordering', 'desc')->get();
            else
                $projects = Project::where('status', 'active')->orderBy('ordering', 'desc')->take($limit)->get();
        }
        catch(Exception $e){
            $timestamp = time();
            $status = "error";
            $message = $e->getMessage();

            return response()->json(['timestamp' => $timestamp, 'status' => $status, 'message' => $message]);
        }

        foreach($projects as $item)
        {
            $item->image = 'http://'.$_SERVER['HTTP_HOST']."/images/projects/".$item->image;
            $item->tags = ProjectTag::where('project_id', $item->id)->orderBy("tag_name", 'asc')->get();
        }

        $timestamp = time();
        $message = "success";

        return response()->json(['data'=>$projects, 'timestamp' => $timestamp, 'status' => "success"]);

    }

    public function reorder(Request $request)
    {
        $projects = Project::orderBy('ordering', 'asc')->get();

        $project_ids = [];

        for ($i = 0; $i < count($projects); $i++) {
            $project_ids[] = $projects[$i]->id;
        }

        $move_from_position = Project::find($request->id)->ordering;
        $move_before_position = $request->new_order;

        $do_minus_one = $move_from_position < $move_before_position;


        $element = array_splice($project_ids, $move_from_position -1 , 1);

        array_splice($project_ids, $move_before_position - 1 - ($do_minus_one ? 1 : 0), 0, $element);

        for($i = 0; $i < count($project_ids); $i++)
        {
            $project = Project::find($project_ids[$i]);
            $project->ordering = $i + 1;
            $project->save();
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
        $projects = Project::where('status', 'active')->orderBy('ordering', 'desc');
        $pages = ceil($projects->count() / $limit);
        $projects = $projects->offset(($page-1) * $limit);
        $projects = $projects->take($limit);
        $projects = $projects->get();

        foreach($projects as $item)
        {
            $item->image = 'http://'.$_SERVER['HTTP_HOST']."/images/projects/".$item->image;
            $item->tags = ProjectTag::where('project_id', $item->id)->orderBy("tag_name", 'asc')->get();
        }


        return response()->json(['data'=>$projects, 'pages'=>$pages]);

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
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
            $imageName = "project-".$alias;
            $imageExtension = $request->image->extension();
            $imageFileName = $imageName. "." . $imageExtension;
        }
        $index = 1;
        while(File::exists(public_path("images/projects/".$imageFileName)))
        {
            $imageFileName = $imageName . "-" . $index . "." . $imageExtension;
            $index += 1;
        }

        if($request->copy_image != null)
        {
            copy(public_path("images/projects/".$request->copy_image), public_path("images/projects/".$imageFileName));
        }
        else
        {
            $request->image->move(public_path("images/projects"), $imageFileName);
        }


        $newProject = new Project;
        $newProject->title = $request->title;
        $newProject->description = $request->description;
        $newProject->image = $imageFileName;
        $newProject->link = $request->link;
        $newProject->link_github = $request->link_github;
        $newProject->project_date = $request->project_date;


        $newProject->ordering = (Project::orderBy('ordering', 'desc')->first()->ordering??0)+ 1;

        $newProject->status = "inactive";
        if($request->is_published) $newProject->status = "active";

        

        $newProject->save();

        $sessionSuccessMessage = "Project added successfully.";
        if($request->copy_image != null)
        {
            $sessionSuccessMessage = "Project copied successfully.";
            return redirect()->route('projects.index')->with('message', $sessionSuccessMessage);
        }
        if($request->save_and_exit)
            return redirect()->route('projects.index')->with('message', $sessionSuccessMessage);
        if($request->save_and_new)
            return redirect()->back()->with('message', $sessionSuccessMessage);

        return redirect()->back()->with('message', "Save ok. Error redirecting.");
    }

    public function add_tag(Request $request)
    {
        $project = Project::findOrFail($request->id);

        $tag = new ProjectTag;
        $tag->tag_name = GenerateAlias($request->tag);
        $project->tag()->save($tag);

        return json_encode( [
            "tag_name"=>$tag->tag_name,
            "id"=>$tag->id,
        ]);
    }

    public function remove_tag(Request $request)
    {
        $tag = ProjectTag::find($request->id);

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
        $project = Project::findOrFail($id);
        return view('projects.edit', ['project'=>$project]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $alias = GenerateAlias($request->title);

        if($request->save_as_copy)
        {
            $request->copy_image = $project->image;
            $request->is_published = 0;
            $this->store($request);
            return redirect()->route('projects.index');
        }

        $imageName = $project->image;
        if($request->image != null)
        {
            $old_path = public_path("images/projects/".$imageName);
            if(File::exists($old_path))
            {
                File::delete($old_path);
            }
            $imageName = "project-" . $alias . "." . $request->image->extension();
            $index = 1;
            while(File::exists(public_path("images/projects/".$imageName)))
            {
                $imageName = "project-" . $alias . "-" . $index . "." . $request->image->extension();
                $index += 1;
            }
            $request->image->move(public_path("images/projects/"), $imageName);
        }

        $project->title = $request->title;
        $project->description = $request->description;
        $project->image = $imageName;
        $project->link = $request->link;
        $project->link_github = $request->link_github;
        $project->project_date = $request->project_date;
        $project->status = "inactive";
        if($request->is_published) $project->status = "active";

        $project->save();

        $sessionSuccessMessage = "Project edited successfully.";
        if($request->save_and_exit)
            return redirect()->route('projects.index')->with('message', $sessionSuccessMessage);
        if($request->save)
            return redirect()->back()->with('message', $sessionSuccessMessage);

        return redirect()->back()->with('message', "Save ok. Error redirecting.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);

        $old_path = public_path("images/projects/".$project->image);
        if(File::exists($old_path))
        {
            File::delete($old_path);
        }

        $project->tag()->delete();

        $project->delete();

        $projects = Project::orderBy('ordering', 'asc')->get();
        
        for($i = 0; $i < count($projects); $i++)
        {
            $projects[$i]->ordering = $i + 1;
            $projects[$i]->save();
        }

        return redirect('projects');
    }
}
