@extends('master')

@section('page_title')
Projects
@endsection

@section('content')
<div class="container">

    <div class="project-index-container header">
        <div> ID </div>
        <div> Title </div>
        <div> Ordering </div>
        <div> Stats </div>

        <div class="actions"> Actions </div>
        <div> Up </div>
    </div>

    @foreach ($projects as $p)
    <?php
            $statusClass = 'project-deleted';
            if ($p['status'] === 'inactive') {
                $statusClass = 'project-inactive';
            }
            if ($p['status'] === 'active') {
                $statusClass = 'project-active';
            }
            
            ?>
    <div class="project-index-container">
        <div>
            {{ $p->id }}
        </div>

        <div>
            <strong> {{ $p->title }} </strong>
        </div>

        <div>
            <strong>{{$p->ordering}}</strong>
            <form method="POST" action={{ route('projects.reorder') }}>
                @csrf
                <input type="hidden" name="id" value={{ $p->id }}>
                <select name="new_order" id="new_order">
                    @foreach ($projects as $p_ordering)
                    <?php if ($p_ordering->id == $p->id) continue; ?>

                    <option value="{{(int)$p_ordering->ordering + 1}}" <?=$p_ordering->ordering == $p->ordering - 1 ?
                        'selected' : '' ?>
                        >Before {{ $p_ordering->title }}</option>
                    @endforeach
                    <option value="1" <?=$p->ordering == 1 ? 'selected' : '' ?>> Last </option>
                </select>
                <button type="submit"> Change </button>
            </form>
        </div>

        <div>
            {{ $p->home_clicks }} clicks
        </div>

        <div class="actions">

            <form action={{ route('projects.edit', ['project'=> $p->id]) }}>
                <button type="submit"> Edit
                </button>
            </form>


            <form method="POST" action={{ route('projects.destroy', ['project'=> $p->id]) }}>
                @csrf
                @method('DELETE')
                <button type="submit"> Remove </button>
            </form>
        </div>

        <div class="{{ $statusClass }}">

        </div>
    </div>
    @endforeach

    <div class="project-index-container new">
        <div> </div>
        <div> <a href="{{ route('projects.create') }}">Create new Project</a> </div>
        <div class="actions"> </div>
        <div> </div>
    </div>
</div>
@endsection