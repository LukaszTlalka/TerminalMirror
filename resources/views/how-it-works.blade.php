@extends('layout.app')

@section('content')
    <main role="main" class='page row w-100 justify-content-center m-0' id='consoleShareApp'>
        <div class='col-10 col-md-9 col-lg-8'>
            <div class="art pt-5">
                <random-art-component></random-art-component>
            </div>
            <article class="pt-5">
                <h1 class="title">About  <span class="text-light">TerminalMirror</span></h2>

                <h3 class="subtitle">Motivation</h3>
                <p class="content">
                    As a programmer, I know firsthand how frustrating it can be to set up remote access tools that require multiple software installations or complicated configurations. That's why I set out to create a command line tool that would simplify the process and make remote access as easy as possible.
                </p>
                <p class="content">
                    I wanted to create a tool that <span class="highlight">didn't require any downloads or installations on either end</span>, and that's where the idea to use a web browser as the console came in. By leveraging the ubiquity of web browsers, I could create a tool that anyone could use, regardless of their technical expertise.
                </p>
                
                
                <h3 class="subtitle">How it works</h3>
                <p class="content">
                    This tool facilitates sharing of your terminal sessions by leveraging the widely available curl and script utilities. With this tool, you can seamlessly transmit data from your console inputs and outputs to TerminalMirror, a secure website, using an authorization token. Once shared, other users can access your console session via the link provided. At any point, you have the option to stop sharing by using the exit command (ctrl + C).

                </p>
                <p class="content">

                    We care for sharing - this project is open source, you can fork it and use it on your server from <a class="highlight link" href="https://github.com/LukaszTlalka/TerminalMirror">this repo</a>.
                </p> 

                <h3 class="subtitle">Contact</h3>
                <p class="content">
                    If you cared to try it, and have feedback, or a problem/question, I would love to hear back from you. Please create a Github issue: <a class="highlight link" href="https://github.com/LukaszTlalka/TerminalMirror">https://github.com/LukaszTlalka/TerminalMirror</a>.
                </p> 
            </article>
        </div>
    </main>
@endsection