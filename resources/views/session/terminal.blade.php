@extends('layout.app')

@section('content')
    <script>
        const WS = {!! json_encode($websocket) !!};
    </script>
    <main role="main" class='full-height console-terminal' id='consoleShareApp'>
        <terminal-component></terminal-component>
    </main>
@endsection

