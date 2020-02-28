@extends('layout.app')

@section('content')
<div id="debug"></div>

<script>
    setInterval(function() {
        $.ajax({
            url: "/debug/files-data",
            context: document.body
        }).done(function(data) {
            $( "#debug" ).html(data)
        });
    }, 1000);
</script>
@endsection
