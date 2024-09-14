<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class StatisticsController extends Controller
{
    public function project_action(Request $request)
    {
        $project = Project::findOrFail($request->id);

        if ($request->action == "view_home") {
            $project->home_clicks += 1;
        }
        if ($request->action == "click_home") {
            $project->home_link_clicks += 1;
        }
        if ($request->action == "view_page") {
            $project->dedicated_page_clicks += 1;
        }
        if ($request->action == "click_page") {
            $project->dedicated_page_link_clicks += 1;
        }
        $project->save();
    }



}
