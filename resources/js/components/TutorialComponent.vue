<template>
    <div class='full-page'>
        <div class='spleet'>
            <div class='half-screen screen1'>
                <window-component v-if="enabled" v-bind:speedRun="speedRun" v-bind:windowData="display.noob.terminalWindow"></window-component>
                <window-component v-if="enabled" v-bind:speedRun="speedRun" v-bind:windowData="display.noob.chatWindow"></window-component>
                <window-component v-if="enabled" v-bind:speedRun="speedRun" v-bind:windowData="display.noob.browserWindow"></window-component>
                <dummy-code-component role="master" @masterUpdate="slaveUpdate"></dummy-code-component>
            </div>
            <div class='half-screen screen2'>
                <span class='scenario-slider animate pulse' v-if="!display.mainMessage.show">
                    <b-form-slider :value="slider.value" :min="0" :max="slider.max"  @slide-stop="slideStop"></b-form-slider>
                </span>

                    <window-component v-if="enabled" v-bind:speedRun="speedRun" v-bind:windowData="display.guru.chatWindow"></window-component>
                    <window-component v-if="enabled" v-bind:speedRun="speedRun" v-bind:windowData="display.guru.browserWindow"></window-component>
                    <dummy-code-component role="slave" :masterData="bgCodeData"></dummy-code-component>
            </div>
        </div>
        <div class="main-message d-flex align-items-center justify-content-center" v-if="display.mainMessage.show">
            <div class="main-box">
                <div class="logo"><b class="d-flex align-items-baseline justify-content-center"><i class="fas fa-terminal"></i><span class="console-type">TerminalMirror</span><span class="console-cursor"></span></b></div>
                <div id='motive' v-if="display.mainMessage.showMotive" >
                    <h1>The easy way to share<br>access to terminals</h1>
                    <div><button v-on:click="startTutorial" class="btn btn-outline-primary">Watch Preview</button><a href="/new-session" class="btn btn-primary">Start session</a></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const RandomScenario = require('../tutorial/scenario-google-down.js');

export default {
    mounted() {
        this.scenario = new RandomScenario(this);

        this.scenario.event.on('steps:change', (step) => {
            this.slider.value = step;
        })

        this.scenario.event.on('steps:loaded', (total) => {
            this.slider.max = total;
        })

        
        document.querySelectorAll('.console-type').forEach((element) => {
            let content = [...element.innerText];
            element.innerText = content[0];

            let index = 1;
            let tempString = content[0];

            (function loop() {
                setTimeout(() => {
                    tempString += content[index];
                    element.innerHTML = tempString;
                    index ++;
                    if (content.length != index )
                        loop();
                }, 150);
            })();
        });

        /*
            this.display.mainMessage.show = false;
            this.scenario.startTutorial();
            this.scenario.jumpToStep(30)
        */
    },
    data: function () {
        return {
            bgCodeData : [],
            enabled: true,
            scenario: null,
            speedRun: false,
            slider: {
                value: 0,
                max: 100,
            },
            display: {
                mainMessage: {
                    show: true,
                    showMotive: true,
                },
                noob: {
                    terminalWindow: {
                        name: 'noob-term',
                        type: 'terminal',
                        title: 'Terminal session',
                        show: false,
                        active: false,
                    },
                    chatWindow: {
                        name: 'noob-chat',
                        type: 'chat',
                        title: 'chat',
                        show: false,
                        class: 'chat-window-50 offset-small',
                        active: false,
                    },
                    browserWindow: {
                        name: 'noob-browser',
                        type: 'browser',
                        title: 'Web Browser',
                        show: false,
                        class: 'offset-large',
                        active: true,
                        browser: {
                            showNewSession: false,
                            showNewSessionCreated: false,
                            showSpinner: false,
                            showTerminal: false,
                            runButtonAnimation: false,
                            showButton: false,
                            bashBeg: '',
                            bashEnd: '',
                        },
                    }
                },
                guru: {
                    chatWindow : {
                        name: 'guru-chat',
                        type: 'chat',
                        title: 'chat',
                        show: false,
                        class: 'chat-window-guru',
                        active: false,
                    },
                    browserWindow: {
                        name: 'guru-browser',
                        type: 'browser',
                        title: 'Web Browser',
                        show: false,
                        class: 'offset-large',
                        active: true,
                        browser: {
                            showNewSession: false,
                            showNewSessionCreated: false,
                            showSpinner: false,
                            showTerminal: false,
                            runButtonAnimation: false,
                            showButton: false,
                            bashBeg: '',
                            bashEnd: '',
                        },
                    }
                },
            }
        }
    },
    methods: {
        startTutorial() {
            this.display.mainMessage.show = false;

            this.scenario.startTutorial();
        },
        slideStop(step) {
            this.scenario.jumpToStep(step)
        },
        slaveUpdate(value) {
            this.bgCodeData = value;
        }
    }
}
</script>

