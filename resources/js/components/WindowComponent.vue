<template>
    <span class="window zoomIn animated" v-bind:class="[windowData.class, windowData.active ? 'active' : '']" v-if="windowData.show" v-bind:id="windowData.name + '-window'">
        <div class="title">{{windowData.title}} <span class="float-right"><i class="far fa-window-minimize"></i> &nbsp; <i class="far fa-times-circle"></i></span></div>
        <span v-if='windowData.type == "terminal"'>
            <div class='terminal win-border'>
                <span v-for="n in 50"><span v-bind:id="windowData.name + '-' + n"></span></span>
            </div>
        </span>
        <div class="chat win-border" v-if='windowData.type == "chat"'>
            <div class="messages">
                <div v-for="message in messages"><strong>{{ message.from }}:</strong> {{ message.text }}</div>
            </div>

            <div class="input-group">
                <div class="form-control"><span class="type-message"></span></div>
                <div class="input-group-append">
                    <button class="btn btn-success" type="button"><i class="fas fa-comments"></i></button>
                </div>
            </div>
        </div>
        <div class="browser win-border" v-if='windowData.type == "browser"'>
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
                <div class="page page-console" v-if="windowData.browser.showTerminal">
                    <b><i class="fas fa-terminal"></i> ConsoleShare</b>
                    <div class='terminal'>
                        <span v-for="n in 50"><span v-bind:id="windowData.name + '-' + n"></span></span>
                    </div>
                </div>
                <div class="page page-new-session" v-if="windowData.browser.showNewSession">
                    <div class="text-center">
                        <h6><i class="fas fa-terminal"></i> ConsoleShare</h6>
                        <button class="btn btn-success show-command" v-bind:class="{'pulse animated' : windowData.browser.runButtonAnimation}" v-if="windowData.browser.showButton">Create Session</button>
                        <div v-if="windowData.browser.showNewSessionCreated">
                            <div class="alert alert-info" role="alert">
                                New session has been created. To start please <b>run</b>:
                            </div>
                            <div class='code'><span class="selected" v-html='windowData.browser.bashBeg.replace(/\n/g, "<br>")'></span><span v-html='windowData.browser.bashEnd.replace(/\n/g, "<br>")'></span></div>
                        </div>
                        <div class="spinner-border" role="status" v-if='windowData.browser.showSpinner'>
                            <span class="sr-only">Creating new session</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
const Typed = require('typed.js');
export default {
    props: ['windowData', 'speedRun'],
    methods: {
        scroll() {
            setTimeout( () => {
                $(".terminal").animate({ scrollTop: 100000 }, 1000);
                $(".messages").animate({ scrollTop: 100000 }, 1000);
            }, 100);
        },
        pushToTerm(options = { typeSpeed: 0 }) {
            let lineID = this.windowData.name + '-' + ++this.lineCounter;

            var self = this;

            return new Promise((resolve) => {
                if (options.typeSpeed && this.speedRun == false) {
                    let typed = new Typed("#" + lineID, Object.assign({
                        onComplete: function() {
                            setTimeout( () => {
                                $("#"+lineID).siblings('.typed-cursor').remove();
                            }, 200);
                            self.scroll();

                            resolve();
                        }
                    }, options));
                } else {
                    options.strings.map( (line) => {
                        $("#" + lineID).html(line);
                    })
                    this.scroll();

                    resolve();
                }
            });
        },
        sendChatMessage(from, text, otherWindow = null, typeSpeed = 20) {


            if (otherWindow == null) {
                this.messages.push({ text, from })
                this.scroll();
                return Promise.resolve();
            }

            return new Promise((resolve) => {
                let typeMessageId = "#" + this.windowData.name + '-window .type-message';

                if (this.speedRun) {
                    this.messages.push({ text, from: 'Me' })
                    this.scroll();
                    resolve();
                    return;
                }

                let typed = new Typed(typeMessageId, {
                    strings: [text],
                    typeSpeed: typeSpeed,
                    onComplete: () => {
                        setTimeout( () => {
                            otherWindow.sendChatMessage(from, text);
                            typed.destroy();
                            this.messages.push({ text, from: 'Me' })
                            this.scroll();

                            resolve();
                        }, 1000);
                    }
                });
            });
        },
        setBrowserUrl(url, delay = 50) {
            let typeMessageId = "#" + this.windowData.name + '-window .type-message';

            if (delay == 0 || this.speedRun) {
                $(typeMessageId).html(url);

                return Promise.resolve();
            }

            return new Promise((resolve) => {
                $(typeMessageId).html("");

                let typed = new Typed(typeMessageId, {
                    strings: [url],
                    typeSpeed: delay,
                    onComplete: () => {
                        typed.destroy();
                        $(typeMessageId).html(url);
                        resolve()
                    }
                });
            });
        }
    },
    data: function() {
        return {
            lineCounter: 0,
            messages: [],
        }
    },

    mounted() {
        this.windowData.pushToTerm = this.pushToTerm;

        this.windowData.sendChatMessage = this.sendChatMessage;
        this.windowData.setBrowserUrl = this.setBrowserUrl;
    }
}
</script>
