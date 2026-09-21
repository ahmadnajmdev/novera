@extends('site.layout')

@section('content')
    @foreach ($page->visibleSections as $section)
        @include('site.sections.render', ['section' => $section])
    @endforeach
@endsection
