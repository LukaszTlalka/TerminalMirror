<template>
    <div class='full-page'>
        <div class='spleet'>
            <div class='half-screen screen1'>

                <window-component v-bind:windowData="display.noob.terminalWindow"></window-component>
                <window-component v-bind:windowData="display.noob.chatWindow"></window-component>
                <window-component v-bind:windowData="display.noob.browserWindow"></window-component>

                <!--
                <span class="window" id='tutor-terminal-window' style="display:none">
                    <div class="title">Terminal session <span class="float-right"><i class="far fa-window-minimize"></i> &nbsp; <i class="far fa-times-circle"></i></span></div>
                    <div class='terminal'></div>
                </span>
                -->

                <span class="window" id='tutor-chat-window' style="display:none">
                    <div class="title"><span class='title-text'></span> <span class="float-right"><i class="far fa-window-minimize"></i> &nbsp; <i class="far fa-times-circle"></i></span></div>

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

                <span class="window" id='tutor-browser-window' style="display:none">
                    <div class="title"><span class='title-text'></span> <span class="float-right"><i class="far fa-window-minimize"></i> &nbsp; <i class="far fa-times-circle"></i></span></div>

                    <div class="browser">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button class="btn btn-info no-radius" type="button"><i class="fas fa-arrow-left"></i></button>
                            </div>
                            <div class="input-group-prepend">
                                <button class="btn btn-info no-radius" type="button"><i class="fas fa-arrow-right"></i></button>
                            </div>
                            <div class="form-control"><span class="type-message"></span></div>
                            <div class="input-group-append">
                                <button class="btn btn-info no-radius" type="button"><i class="fas fa-sync-alt"></i></button>
                            </div>
                        </div>
                        <div class="messages">
                            <div class="page page-console">
                                <b><i class="fas fa-terminal"></i> ConsoleShare</b>
                                <div class='terminal'></div>
                            </div>
                            <div class="page page-new-session">
                                <div class="text-center">
                                    <h6><i class="fas fa-terminal"></i> ConsoleShare</h6>
                                    <button class="btn btn-success show-command">Create Session</button>
                                    <div class="new-session-created">

                                        <div class="alert alert-info" role="alert">
                                            New session has been created. To start please <b>run</b>:
                                        </div>
                                        <div class='code'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </span>
            </div>
            <div class='half-screen screen2'>
                <window-component v-bind:windowData="display.guru.chatWindow"></window-component>
                <window-component v-bind:windowData="display.guru.browserWindow"></window-component>

                <span class="window" id='tutor-chat-second-window' style="display:none">
                    <div class="title"><span class='title-text'></span> <span class="float-right"><i class="far fa-window-minimize"></i> &nbsp; <i class="far fa-times-circle"></i></span></div>

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
        <div class="main-message animated fadeIn" v-if="display.mainMessage.show">
            <b><i class="fas fa-terminal"></i> ConsoleShare</b>
            <div id='motive' v-if="display.mainMessage.showMotive" class="animated fadeIn slow">
                <h1>Helps you share<br>access to terminals</h1>
                <div><button v-on:click="startTutorial" class="btn btn-info">Watch Preview?</button></div>
            </div>
        </div>
    </div>
</template>

<script>
const RandomScenario = require('../tutorial/scenario-google-down.js');

export default {
    mounted() {
        this.scenario = new RandomScenario(this);

        this.startTutorial();
    },
    data: function () {
        return {
            scenario: null,
            display: {
                mainMessage: {
                    show: true,
                    showMotive: true
                },
                noob: {
                    terminalWindow: {
                        name: 'noob-term',
                        type: 'terminal',
                        title: 'Termianl session',
                        show: false,
                        active: false
                    },
                    chatWindow: {
                        name: 'noob-chat',
                        type: 'chat',
                        title: 'chat',
                        show: false,
                        class: 'chat-window-50 offset-small',
                        active: false
                    },
                    browserWindow: {
                        name: 'noob-browser',
                        type: 'browser',
                        title: 'Web Browser',
                        show: true,
                        class: 'offset-large',
                        active: true,
                        browser: {
                            showNewSession: false,
                            showNewSessionCreated: false,
                            showSpinner: false,
                            showTerminal: true,
                            runButtonAnimation: false,
                            showButton: false,
                            bashBeg: '',
                            bashEnd: '',
                        }
                    }
                },
                guru: {
                    chatWindow : {
                        name: 'guru-chat',
                        type: 'chat',
                        title: 'chat',
                        show: false,
                        class: 'chat-window-guru',
                        active: false
                    },
                    browserWindow: {
                        name: 'guru-browser',
                        type: 'browser',
                        title: 'Web Browser',
                        show: true,
                        class: 'offset-large',
                        active: true,
                        browser: {
                            showNewSession: false,
                            showNewSessionCreated: false,
                            showSpinner: false,
                            showTerminal: true,
                            runButtonAnimation: false,
                            showButton: false,
                            bashBeg: '',
                            bashEnd: '',
                        }
                    }
                },
            }
        }
    },
    methods: {
        startTutorial() {
            this.display.mainMessage.show = false;

            this.scenario.startTutorial(this);
        }
    }
}
</script>

