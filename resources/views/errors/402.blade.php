@extends('errors::layout')

@section('title', 'Payment Required')
@section('code', '402')
@section('message', 'Payment is required to access this resource.')

@section('help')
    This feature requires a payment to be completed. Please complete the payment process to continue.
