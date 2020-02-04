@extends('layout.app')

@section('content')
    <main role="main">
        <div class="container">
            <div class="row justify-content-md-center">
                <div class="col col-md-5 mt-5">
                    <textarea style="width:100%;height: 150px">{{$command}}</textarea>
                </div>
            </div>
        </div>
    </main>
@endsection
