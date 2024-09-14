@extends('master')

@section('page_title')
    Edit Gallery Post: {{ $post->title }}
@endsection
@section('content')
    <div class="container">

        <form method="POST" action={{ route('gallery.update', ['gallery' => $post->id]) }}>

            @csrf
            @method('PUT')
            <div class="buttons">
                <input type="submit" name="save" value="Save">
                <input type="submit" name="save_and_exit" value="Save and Exit">
            </div>
            <div class="preform">
                <input type="checkbox" id="is_published" name="is_published" value="is_published"
                    {{ $post->status == 'active' ? 'checked' : '' }}>
                <label for="is_published"> Published </label>
            </div>
            <hr>
            <div class="form">
                <div class="wrapper">

                    <label for="title"> Title </label>
                    <input type="text" id="title" name="title" placeholder="Title" value="{{ $post->title }}"
                        required />
                    <label for="date"> Date </label>
                    <input type="date" id="date" name="date" placeholder="Date" value="{{ $post->date }}"
                        required />
                </div>

                <div class="wrapper">
                    <label for="description">Description</label>
                    <textarea name="description" placeholder="Description" required>{{ $post->description }}</textarea>
                </div>
            </div>
        </form>

        <form id="image_upload" style="display: block;">

            <label for="image">Image</label>
            <input type="file" name="image" id="image"
                accept="image/png,image/jpg,image/jpeg,image/bmp,image/gif,image/webp" multiple>
        </form>

        <div id="image_preview">
            @foreach ($images as $index => $image)
                <div id="wrapper-image-{{ $image->id }}">
                    <img src="{{ asset('images/gallery/' . $image->src) }}" class="post-image" height="100"
                        width="100" />
                    <button class="delete-button" onclick="removeImage({{ $image->id }})">Remove</button>
                </div>
            @endforeach
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
        <script>
            var imageInput = $('input#image');
            var imageInputForm = document.querySelector("form#image_upload");
            var imagePreview = $('div#image_preview');

            function removeImage($id) {

                $.ajax({
                    url: "{{ route('gallery.delete_file') }}",
                    data: {
                        image_id: $id,
                        post_id: "{{ $post->id }}",
                        _token: "{{ csrf_token() }}",
                        _method: 'POST',
                    },
                    method: 'POST',
                    success: function(data) {
                        console.log("deleted")
                        $("#wrapper-image-" + $id).remove();
                    },
                    error: function(err) {
                        console.log(err);
                    }

                });
            }


            imageInput.on('change', function(e) {


                let added_files = [];

                var data = new FormData();
                $.each(e.target.files, function(index, file) {
                    data.append('files[]', file);
                });

                data.append('_token', "{{ csrf_token() }}");
                data.append('_method', 'POST');
                data.append('post_id', "{{ $post->id }}");

                $.ajax({
                    url: "{{ route('gallery.upload_file') }}",
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    success: function(response) {
                        response = JSON.parse(response);
                        console.log(response);
                        response.forEach((file) => {
                            let newDiv = $('<div></div>').attr("id", "wrapper-image-" + file.id);
                            let img = $('<img>')
                                .attr("src", "{{ asset('images/gallery/') }}/" + file.src)
                                .attr("height", "100")
                                .attr("width", "100")
                                .attr("data-id", file.id)

                            newDiv.append(img);

                            let button = $('<button>Remove</button>')
                                .addClass("delete-button")
                                .on('click', () => removeImage(file.id));

                            newDiv.append(button);

                            imagePreview.append(newDiv);
                        });
                        imageInputForm.reset();
                    },
                    error: function(err) {
                        console.log("err" + err);
                    }

                });

            });
        </script>
    @endsection
