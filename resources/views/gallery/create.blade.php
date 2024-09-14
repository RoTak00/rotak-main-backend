@extends('master')

@section('page_title')
    Create Gallery Post
@endsection
@section('content')
    <div class="container">

        <form method="POST" action={{ route('gallery.store') }}>

            @csrf
            <div class="buttons">
                <input type="submit" name="create_and_add_images" value="Save and Add Images">
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
                    <label for="date"> Date </label>
                    <input type="date" id="date" name="date" placeholder="Date" required />
                </div>

                <div class="wrapper">
                    <label for="description">Description</label>
                    <textarea name="description" placeholder="Description" required></textarea>
                </div>
            </div>


        </form>

    </div>


    <script>
        /*
                            var imageInput = document.querySelector('input#image');
                            var imagePreview = document.querySelector('div#image_preview');

                            imageInput.addEventListener('change', function() {
                                var file = this.files[0];

                                img = document.createElement('img');
                                img.src = window.URL.createObjectURL(file);

                                imagePreview.innerHTML = '';
                                imagePreview.appendChild(img);
                            });*/
    </script>
@endsection
