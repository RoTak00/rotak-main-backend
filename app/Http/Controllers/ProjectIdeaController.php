<?php

namespace App\Http\Controllers;

use App\Models\ProjectIdeaTag;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use App\Models\ProjectIdea;

class ProjectIdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $project_ideas = ProjectIdea::orderBy('ordering', 'desc')->get();
        return view('project_ideas.index', ['project_ideas' => $project_ideas]);
    }

    /*public function api_get($limit = null)
    {
        $project_ideas = null;
        try {
            if ($limit == null)
                $project_ideas = ProjectIdea::where('status', 'active')->orderBy('ordering', 'desc')->get();
            else
                $project_ideas = ProjectIdea::where('status', 'active')->orderBy('ordering', 'desc')->take($limit)->get();
        } catch (Exception $e) {
            $timestamp = time();
            $status = "error";
            $message = $e->getMessage();

            return response()->json(['timestamp' => $timestamp, 'status' => $status, 'message' => $message]);
        }

        foreach ($project_ideas as $item) {
            $item->image = 'http://' . $_SERVER['HTTP_HOST'] . "/images/project_ideas/" . $item->image;
            $item->tags = ProjectTag::where('project_id', $item->id)->orderBy("tag_name", 'asc')->get();
        }

        $timestamp = time();
        $message = "success";

        return response()->json(['data' => $project_ideas, 'timestamp' => $timestamp, 'status' => "success"]);

    }*/

    public function reorder(Request $request)
    {
        $project_ideas = ProjectIdea::orderBy('ordering', 'asc')->get();

        $project_ids = [];

        for ($i = 0; $i < count($project_ideas); $i++) {
            $project_ids[] = $project_ideas[$i]->id;
        }

        $move_from_position = ProjectIdea::find($request->id)->ordering;
        $move_before_position = $request->new_order;

        $do_minus_one = $move_from_position < $move_before_position;


        $element = array_splice($project_ids, $move_from_position - 1, 1);

        array_splice($project_ids, $move_before_position - 1 - ($do_minus_one ? 1 : 0), 0, $element);

        for ($i = 0; $i < count($project_ids); $i++) {
            $project_idea = ProjectIdea::find($project_ids[$i]);
            $project_idea->ordering = $i + 1;
            $project_idea->save();
        }

        return redirect()->back();
    }

    /**
     * Retrieves a paginated JSON response of active project_ideas, 
     * with optional limits and page numbers for pagination.
     *
     * @param int|null $limit The number of results to return per page.
     * @param int|null $page The page number to retrieve.
     * @throws Some_Exception_Class If an error occurs during pagination.
     * @return \Illuminate\Http\JsonResponse The paginated project_ideas and total pages as JSON.
     */
    /* public function api_get_paginated($page, $limit)
     {
         $project_ideas = ProjectIdea::where('status', 'active')->orderBy('ordering', 'desc');
         $pages = ceil($project_ideas->count() / $limit);
         $project_ideas = $project_ideas->offset(($page - 1) * $limit);
         $project_ideas = $project_ideas->take($limit);
         $project_ideas = $project_ideas->get();

         foreach ($project_ideas as $item) {
             $item->image = 'http://' . $_SERVER['HTTP_HOST'] . "/images/project_ideas/" . $item->image;
             $item->tags = ProjectTag::where('project_id', $item->id)->orderBy("tag_name", 'asc')->get();
         }


         return response()->json(['data' => $project_ideas, 'pages' => $pages]);

     }
 */

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('project_ideas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $alias = GenerateAlias($request->title);

        $newProjectIdea = new ProjectIdea;
        $newProjectIdea->title = $request->title;
        $newProjectIdea->content = $request->content;
        $newProjectIdea->alias = $alias;
        $newProjectIdea->date = $request->date;


        $newProjectIdea->ordering = (ProjectIdea::orderBy('ordering', 'desc')->first()->ordering ?? 0) + 1;

        $newProjectIdea->status = "inactive";
        if ($request->is_published)
            $newProjectIdea->status = "active";



        $newProjectIdea->save();

        $sessionSuccessMessage = "Project Idea added successfully.";

        if ($request->save_and_exit)
            return redirect()->route('project_ideas.index')->with('message', $sessionSuccessMessage);
        if ($request->save_and_new)
            return redirect()->back()->with('message', $sessionSuccessMessage);

        return redirect()->back()->with('message', "Save ok. Error redirecting.");
    }

    public function add_tag(Request $request)
    {
        $project_idea = ProjectIdea::findOrFail($request->id);

        $tag = new ProjectIdeaTag;
        $tag->tag_name = GenerateAlias($request->tag);
        $project_idea->tag()->save($tag);

        return json_encode([
            "tag_name" => $tag->tag_name,
            "id" => $tag->id,
        ]);
    }

    public function remove_tag(Request $request)
    {
        $tag = ProjectIdeaTag::find($request->id);

        if ($tag)
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
        $project_idea = ProjectIdea::findOrFail($id);
        return view('project_ideas.edit', ['project_idea' => $project_idea]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project_idea = ProjectIdea::findOrFail($id);

        $alias = GenerateAlias($request->title);

        if ($request->save_as_copy) {
            $request->is_published = 0;
            $this->store($request);
            return redirect()->route('project_ideas.index');
        }

        $project_idea->title = $request->title;
        $project_idea->alias = $alias;
        $project_idea->content = $request->content;
        $project_idea->date = $request->date;
        $project_idea->status = "inactive";
        if ($request->is_published)
            $project_idea->status = "active";

        $project_idea->save();

        $sessionSuccessMessage = "Project edited successfully.";
        if ($request->save_and_exit)
            return redirect()->route('project_ideas.index')->with('message', $sessionSuccessMessage);
        if ($request->save)
            return redirect()->back()->with('message', $sessionSuccessMessage);

        return redirect()->back()->with('message', "Save ok. Error redirecting.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project_idea = ProjectIdea::findOrFail($id);

        $project_idea->tag()->delete();

        $project_idea->delete();

        $project_ideas = ProjectIdea::orderBy('ordering', 'asc')->get();

        for ($i = 0; $i < count($project_ideas); $i++) {
            $project_ideas[$i]->ordering = $i + 1;
            $project_ideas[$i]->save();
        }

        return redirect('project_ideas');
    }
}
