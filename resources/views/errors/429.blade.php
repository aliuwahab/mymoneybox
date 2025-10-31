@extends('errors::layout')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('message', 'Whoa! Slow down there.')

@section('help')
    You've made too many requests in a short period. Please wait a few moments before trying again.
