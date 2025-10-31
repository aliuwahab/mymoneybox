@extends('errors::layout')

@section('title', 'Service Unavailable')
@section('code', '503')
@section('message', 'MyPiggyBox is temporarily unavailable.')

@section('help')
    We're performing scheduled maintenance or experiencing high traffic. Please check back in a few minutes.
