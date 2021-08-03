@extends('layout.app')

@section('content')
    <main role="main" id='consoleShareApp' class="full-height console-tutorial">
        <new-session-component route="{{ route('check-curl-session', $ref) }}"></new-session-component>
        <div class="full-page">
            <div class="spleet"><div class="half-screen screen1"></div><div class="half-screen screen2"></div></div>
            <div class="main-message">
                <div class="container">
                    <p>
                        Run the following command in your terminal. Make sure you have
                        <code class="hljs"><span class="hljs-built_in">curl</span></code> and
                        <code class="hljs"><span class="hljs-built_in">script</span></code>
                        installed on your system.
                    </p>
                    <pre><code id="terminalShareCommand" class="language-bash hljs">{{$command}}</code></pre>
                    <p><clipboard-button-component target="terminalShareCommand" text="Copy command"></clipboard-button-component></p>
                </div>
            </div>
        </div>
    </main>
@endsection
