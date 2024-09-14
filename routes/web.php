<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserMessageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GalleryPostsController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\ProjectIdeaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::post('/api/contact', [UserMessageController::class, 'store']);
Route::get('/api/projects/page/{page}/limit/{limit}', [ProjectController::class, 'api_get_paginated']);
Route::get('/api/projects/{limit?}', [ProjectController::class, 'api_get']);
Route::post('/api/stats/projects', [StatisticsController::class, 'project_action']);


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware("auth");

Route::resource('projects', ProjectController::class)->middleware("auth");
Route::post('projects/add_tag', [ProjectController::class, 'add_tag'])->middleware("auth")->name('projects.add_tag');
Route::post('projects/remove_tag', [ProjectController::class, 'remove_tag'])->middleware("auth")->name('projects.remove_tag');
Route::post('projects/reorder', [ProjectController::class, 'reorder'])->middleware("auth")->name('projects.reorder');

Route::resource('blog', BlogPostController::class)->middleware("auth");
Route::post('blog/add_tag', [BlogPostController::class, 'add_tag'])->middleware("auth")->name('blog.add_tag');
Route::post('blog/remove_tag', [BlogPostController::class, 'remove_tag'])->middleware("auth")->name('blog.remove_tag');
Route::post('blog/reorder', [BlogPostController::class, 'reorder'])->middleware("auth")->name('blog.reorder');

Route::resource('project_ideas', ProjectIdeaController::class)->middleware("auth");
Route::post('project_ideas/add_tag', [ProjectIdeaController::class, 'add_tag'])->middleware("auth")->name('project_ideas.add_tag');
Route::post('project_ideas/remove_tag', [ProjectIdeaController::class, 'remove_tag'])->middleware("auth")->name('project_ideas.remove_tag');
Route::post('project_ideas/reorder', [ProjectIdeaController::class, 'reorder'])->middleware("auth")->name('project_ideas.reorder');


Route::resource('gallery', GalleryPostsController::class)->middleware("auth");
Route::post('gallery/upload_file', [GalleryPostsController::class, 'upload_file'])->middleware("auth")->name('gallery.upload_file');
Route::post('gallery/delete_file', [GalleryPostsController::class, 'delete_file'])->middleware("auth")->name('gallery.delete_file');




Route::get('/messages', [UserMessageController::class, 'index'])->middleware("auth")->name('messages.index');
Route::delete('/messages/{message}', [UserMessageController::class, 'destroy'])->middleware("auth")->name('messages.destroy');