@extends('master')

@section('page_title')
    User Messages
@endsection

@section('content')
    <div class="container">

        <div class="message-index-container header">
            <div> ID </div>
            <div> From </div>
            <div> Content </div>
            <div class="actions"> Actions </div>
        </div>

        @foreach ($messages as $m)
            <div class="message-index-container">
                <div>
                    {{ $m->id }}
                </div>

                <div>
                    <strong> {{ $m->name }} </strong><br />
                    <a href="mailto:{{ $m->email }}"> {{ $m->email }}</a>
                </div>


                <div>
                    {{ $m->message }}
                </div>

                <div class="actions">

                    <form method="POST" action={{ route('messages.destroy', ['message' => $m->id]) }}>
                        @csrf
                        @method('DELETE')
                        <button type="submit"> Remove </button>
                    </form>
                </div>

            </div>
        @endforeach


    </div>
@endsection
