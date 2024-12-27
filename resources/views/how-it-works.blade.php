@extends('layout.app')

@section('content')
    <main role="main" class='page row w-100 justify-content-center m-0' id='consoleShareApp'>
        <div class='col-10 col-md-9 col-lg-8'>
            <div class="art pt-5">
                <random-art-component></random-art-component>
            </div>
            <article class="pt-5">
                <h1 class="title">About <span class="text-light">TerminalMirror</span></h1>
                
                <h3 class="subtitle">Our Motivation</h3>
                <p class="content">
                    As a programmer, I've often felt the frustration of setting up remote access tools that require juggling multiple software installations or navigating through complex configurations. This hassle inspired me to develop a command-line tool that simplifies the entire process, making remote access as seamless and straightforward as possible.
                </p>
                <p class="content">
                    I wanted to design a tool that eliminates the need for any downloads or installations on either side of the connection. This led me to the innovative idea of using a web browser as the console. By leveraging the widespread availability and user-friendliness of web browsers, I was able to create a solution that anyone can use effortlessly, regardless of their technical expertise.
                </p>
                
                <h3 class="subtitle">How It Works</h3>
                <p class="content">
                    TerminalMirror streamlines terminal session sharing by leveraging widely available tools like <code>curl</code> and <code>script</code>. Here's how it works:
                </p>
                <ul class="content">
                    <li><strong>Transmit Data:</strong> Your terminal inputs and outputs are securely transmitted to the TerminalMirror website using an authorization token.</li>
                    <li><strong>Access Anywhere:</strong> Share the generated link with others, allowing them to view your terminal session directly in their web browser.</li>
                    <li><strong>Control Sharing:</strong> You maintain full control over the session and can stop sharing at any time by pressing <code>Ctrl + C</code>.</li>
                </ul>
                <p class="content">
                    TerminalMirror is designed with security and simplicity in mind, ensuring that your terminal sessions are both safe and easy to share.
                </p>
                <p class="content">
                    Interested in customizing or self-hosting? TerminalMirror is open source! Fork the project and deploy it on your own server by visiting <a href="https://github.com/LukaszTlalka/TerminalMirror" class="highlight link">our GitHub repository</a>.
                </p>
                
                <h3 class="subtitle">Get in Touch</h3>
                <p class="content">
                    We’d love to hear from you! Whether you have feedback, encounter an issue, or have a question, your input helps us improve TerminalMirror. Please reach out by creating a GitHub issue at <a href="https://github.com/LukaszTlalka/TerminalMirror" class="highlight link">https://github.com/LukaszTlalka/TerminalMirror</a>.
                </p>
            </article>

        </div>
    </main>
@endsection
