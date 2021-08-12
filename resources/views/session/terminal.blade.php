@extends('layout.app')

@section('content')
    <script>
        const WS = {!! json_encode($websocket) !!};
    </script>
    <main role="main" class='full-height console-terminal' id='consoleShareApp'>
        <terminal-component ref="terminal"></terminal-component>
        <div id="alerts-fixed">
            <terminal-size-tip-component id="terminal-size-tip"></terminal-size-tip-component>
        </div>
    </main>
@endsection

