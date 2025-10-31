@extends('errors::layout')

@section('title', 'Page Expired')
@section('code', '419')
@section('message', 'Your session has expired.')

@section('help')
    For security reasons, your session has timed out. Please refresh the page and try again.
