@extends('errors::layout')

@section('title', 'Server Error')
@section('code', '500')
@section('message', 'Server Error')
@section('description', 'Something went wrong on our end. We\'re working to fix this issue. Please try again later.')

@section('icon')
    <svg class="size-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke="#FF2D20" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
@endsection
