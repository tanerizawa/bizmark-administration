@extends('landing.layout')

@section('title', $vacancy->title . ' - Karir Bizmark.ID')
@section('meta_description', Str::limit(strip_tags($vacancy->description), 160))

@section('content')
@include('career.partials.show-content')
@endsection

