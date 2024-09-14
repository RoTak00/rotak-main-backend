@extends('master')

@section('page_title')
Create Project Idea
@endsection
@section('content')
<div class="container">

    <form method="POST" action={{ route('project_ideas.store') }}>

        @csrf
        <div class="buttons">
            <input type="submit" name="save_and_exit" value="Save and Exit">
            <input type="submit" name="save_and_new" value="Save and New">
        </div>
        <div class="preform">
            <input type="checkbox" id="is_published" name="is_published" value="is_published">
            <label for="is_published"> Publish Automatically? </label>
        </div>
        <hr>
        <div class="form">
            <div class="wrapper">

                <label for="title"> Title </label>
                <input type="text" id="title" name="title" placeholder="Title" required />

                <label for="date"> Blog Post Date </label>
                <input type="text" name="date" placeholder="Date" />

            </div>

            <div class="wrapper">
                <label for="content">Content</label>
                <textarea name="content" placeholder="Content" required></textarea>
            </div>
        </div>


    </form>

</div>

@endsection