@extends('errors::layout')

@section('title', 'Unauthorized')
@section('code', '401')
@section('message', 'Authentication required to access this page.')

@section('help')
    Please log in to your MyPiggyBox account to continue. If you don't have an account, you can create one for free.
