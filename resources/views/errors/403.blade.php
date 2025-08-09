@extends('errors::layout')

@section('title', 'Forbidden')
@section('code', '403')
@section('message', 'Forbidden')
@section('description', 'You don\'t have permission to access this resource. Please contact your administrator if you believe this is an error.')

@section('icon')
    <svg class="size-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke="#FF2D20" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
    </svg>
@endsection
