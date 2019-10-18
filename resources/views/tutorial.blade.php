@extends('layout.app')

@section('content')
        <main role="main" class='console-tutorial'>

            <div class='spleet'>
                <div class='half-screen screen1'>
                    <span class="window" id='tutor-terminal-window'>
                        <div class="title">Terminal session <span class="float-right"><i class="far fa-window-minimize"></i> &nbsp; <i class="far fa-times-circle"></i></span></div>
                        <div class='terminal'></div>
                    </span>

                    <span class="window" id='tutor-chat-window'>
                        <div class="title"><span class='title'></span> <span class="float-right"><i class="far fa-window-minimize"></i> &nbsp; <i class="far fa-times-circle"></i></span></div>

                        <div class="chat">
                            <div class="messages"></div>
                            <div class="input-group">
                                <div class="form-control"><span class="type-message"></span></div>
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button"><i class="fas fa-comments"></i></button>
                                </div>
                            </div>
                        </div>
                    </span>
                </div>
                <div class='half-screen screen2'>
                    <span class="window" id='tutor-chat-second-window'>
                        <div class="title"><span class='title'></span> <span class="float-right"><i class="far fa-window-minimize"></i> &nbsp; <i class="far fa-times-circle"></i></span></div>

                        <div class="chat">
                            <div class="messages"></div>
                            <div class="input-group">
                                <div class="form-control"><span class="type-message"></span></div>
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button"><i class="fas fa-comments"></i></button>
                                </div>
                            </div>
                        </div>
                    </span>
                </div>
            </div>
            <div class="main-message">
                <b><i class="fas fa-terminal"></i> ConsoleShare</b>
                <div id='motive'>
                    <h1>Helps you share<br>access to terminals</h1>
                    <div><button id="watch-preview" class="btn btn-info">Watch Preview?</button></div>
                </div>
            </div>
        </main>


@endsection
