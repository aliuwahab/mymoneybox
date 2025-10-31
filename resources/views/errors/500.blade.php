@extends('errors::layout')

@section('title', 'Server Error')
@section('code', '500')
@section('message', 'Whoops! Something went wrong on our end.')

@section('help')
    We're experiencing technical difficulties. Our team has been notified and is working to resolve the issue. Please try again later.
