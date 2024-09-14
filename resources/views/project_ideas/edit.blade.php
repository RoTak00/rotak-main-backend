@extends('master')

@section('page_title')
Edit Project Idea {{ $project_idea->title }}
@endsection
@section('content')
<div class="container">

    <form method="POST" action={{ route('project_ideas.update', ['project_idea'=> $project_idea->id]) }}>

        @csrf
        @method('PUT')
        <div class="buttons">
            <input type="submit" name="save" value="Save">
            <input type="submit" name="save_and_exit" value="Save and Exit">
            <input type="submit" name="save_as_copy" value="Save as Copy">
        </div>
        <div class="preform">
            <input type="checkbox" id="is_published" name="is_published" value="is_published" {{ $project_idea->status
            ==
            'active' ? 'checked' : '' }}>
            <label for="is_published"> Published </label>
        </div>
        <hr>
        <div class="form">
            <div class="wrapper">

                <label for="title"> Title </label>
                <input type="text" id="title" name="title" placeholder="Title" value="{{ $project_idea->title }}"
                    required />

                <label for="date"> Date </label>
                <input type="text" name="date" placeholder="Date" value="{{ $project_idea->date}}" />
            </div>

            <div class="wrapper">
                <label for="content">Content</label>
                <textarea name="content" placeholder="Content" required>{{ $project_idea->content }}</textarea>

                <input type="text" name="tags" id="tags" />
                <button type="button" id="add_tag">Add Tag</button>
                <div id="tags_preview">
                    @foreach ($project_idea->tag as $tag)
                    <div class="tag" id="tag-{{$tag->id}}">
                        {{$tag->tag_name}}
                        <button type="button" onclick="deleteTag({{$tag->id}})">
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


    var tagInput = document.querySelector("input#tags");
    var tagsPreview = document.querySelector("div#tags_preview");
    var tagButton = document.querySelector("button#add_tag");

    function deleteTag(id) {
        $.ajax({
            url: "{{ route('project_ideas.remove_tag') }}",
            data: {
                id: id,
                _token: "{{ csrf_token() }}",
                _method: 'POST',
            },
            method: 'POST',
            success: function (data) {
                document.querySelector("#tag-" + id).remove();
            }

        });
    }
    tagButton.addEventListener("click", function () {
        var tag = tagInput.value;
        tagInput.value = "";

        $.ajax({
            url: "{{ route('project_ideas.add_tag') }}",
            data: {
                tag: tag,
                id: {{ $project_idea-> id }},
        _token: "{{ csrf_token() }}",
        _method: 'POST',
                },
        method: 'POST',
        success: function (data) {
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