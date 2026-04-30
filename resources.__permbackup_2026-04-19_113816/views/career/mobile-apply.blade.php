@extends('landing.layout')

@section('title', 'Lamar - ' . $vacancy->title . ' - Bizmark.ID')
@section('meta_description', 'Form lamaran untuk posisi ' . $vacancy->title . ' di Bizmark.ID')

@section('content')
@include('career.partials.apply-content')
@endsection

