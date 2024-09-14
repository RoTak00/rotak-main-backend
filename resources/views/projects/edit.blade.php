@extends('master')

@section('page_title')
    Edit Project {{ $project->title }}
@endsection
@section('content')
    <div class="container">

        <form method="POST" action={{ route('projects.update', ['project' => $project->id]) }} enctype="multipart/form-data">

            @csrf
            @method('PUT')
            <div class="buttons">
                <input type="submit" name="save" value="Save">
                <input type="submit" name="save_and_exit" value="Save and Exit">
                <input type="submit" name="save_as_copy" value="Save as Copy">
            </div>
            <div class="preform">
                <input type="checkbox" id="is_published" name="is_published" value="is_published"
                    {{ $project->status == 'active' ? 'checked' : '' }}>
                <label for="is_published"> Published </label>
            </div>
            <hr>
            <div class="form">
                <div class="wrapper">

                    <label for="title"> Title </label>
                    <input type="text" id="title" name="title" placeholder="Title" value="{{ $project->title }}"
                        required />
                    <label for="link"> Link </label>
                    <input type="text" name="link" placeholder="Link" value="{{ $project->link }}" required />
                    <label for="link_github"> GitHub Link </label>
                    <input type="text" name="link_github" placeholder="Github Link" value="{{ $project->link_github }}"/>
                    <label for="project_date"> Project Date </label>
                    <input type="text" name="project_date" placeholder="Project Date" value="{{ $project->project_date}}"/>
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image"
                        accept="image/png,image/jpg,image/jpeg,image/bmp,image/gif,image/webp">
                    <div id="image_preview">
                        <img src="{{ asset('/images/projects/' . $project->image) }}" />
                    </div>
                </div>

                <div class="wrapper">
                    <label for="description">Description</label>
                    <textarea name="description" placeholder="Description" required>{{ $project->description }}</textarea>

                    <input type = "text" name = "tags" id = "tags" />
                    <button type="button" id="add_tag">Add Tag</button>
                    <div id="tags_preview">
                        @foreach ($project->tag as $tag)
                            <div class="tag" id = "tag-{{$tag->id}}">
                                {{$tag->tag_name}}
                                <button type = "button" onclick = "deleteTag({{$tag->id}})">
                                X
                                </button>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>



        </form>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
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


        var tagInput = document.querySelector("input#tags");
        var tagsPreview = document.querySelector("div#tags_preview");
        var tagButton = document.querySelector("button#add_tag");

        function deleteTag(id)
        {
            $.ajax({
                url: "{{ route('projects.remove_tag') }}",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}",
                    _method: 'POST',
                },
                method: 'POST',
                success: function(data) {
                    document.querySelector("#tag-" + id).remove();
                }
                
            });
        }
        tagButton.addEventListener("click", function() {
            var tag = tagInput.value;
            tagInput.value = "";

            $.ajax({
                url: "{{ route('projects.add_tag') }}",
                data: {
                    tag: tag,
                    id: {{ $project->id }},
                    _token: "{{ csrf_token() }}",
                    _method: 'POST',
                },
                method: 'POST',
                success: function(data) {
                    data = JSON.parse(data);
                    tagsPreview.innerHTML += 
                    `<div class="tag" id = "tag-${data.id}">
                    ${data.tag_name} 
                    <button type = "button" onclick="deleteTag(${data.id})">X</button>
                    </div>`;
                }
                
            });

        });
    </script>
@endsection
