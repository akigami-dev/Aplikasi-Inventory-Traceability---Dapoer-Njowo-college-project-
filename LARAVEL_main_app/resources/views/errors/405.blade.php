@extends('errors::minimal')

@section('title', __('Method Not Allowed'))
@section('code', '405')
@section('message', __('Oops! You can’t access this page that way.'))
