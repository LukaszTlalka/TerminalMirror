@extends('layout.app')
<script>
    const WS = {!! json_encode($websocket) !!};
</script>

@section('content')
    <main role="main" class='full-height console-terminal' id='consoleShareApp'>
        <terminal-component></terminal-component>
    </main>
@endsection

