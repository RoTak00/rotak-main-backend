@extends('master')

@section('page_title')
    Create Blog Post
@endsection
@section('content')
    <div class="container">

        <form method="POST" action={{ route('blog.store') }} enctype="multipart/form-data">

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
                    <label for="header"> Header </label>
                    <input type="text" name="header" placeholder="Header" />
                    <label for="date"> Blog Post Date </label>
                    <input type="text" name="date" placeholder="Date"/>
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image"
                        accept="image/png,image/jpg,image/jpeg,image/bmp,image/gif,image/webp" required>
                    <div id="image_preview">

                    </div>
                </div>

                <div class="wrapper">
                    <label for="content">Content</label>
                    <textarea name="content" placeholder="Content" required></textarea>
                </div>
            </div>


        </form>

    </div>


    <script>
        var imageInput = document.querySelector('input#image');
        var imagePreview = document.querySelector('div#image_preview');

        imageInput.addEventListener('change', function() {
            var file = this.files[0];

            img = document.createElement('img');
            img.src = window.URL.createObjectURL(file);

            imagePreview.innerHTML = '';
            imagePreview.appendChild(img);
        });
    </script>
@endsection
