@extends('errors::layout')

@section('title', 'Access Denied')
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'You do not have permission to access this resource.'))

@section('help', 'If you believe you should have access to this page, please contact an administrator or log in with an authorized account.')
