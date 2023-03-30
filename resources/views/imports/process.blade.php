@extends('layouts.app')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Processar arquivo {!! $filename !!}</div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="GET" action="{{ route('queue.run') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="file" value="{!! $filename !!}">
                <button type="submit" id="submit" class="btn btn-primary">Processar Fila</button>
            </form>
        </div>
    </div>
</div>
@stop
