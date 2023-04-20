<template>
    <div class='full-page' v-on:keyup.esc.exact.prevent.stop="copy" v-on:keyup.ctrl.shift.86.prevent.stop="paste" @paste="paste">
        <div id="terminal" style="height:100%"></div>
    </div>
</template>

<script>

const Terminal = require('xterm').Terminal;
const FitAddon = require('xterm-addon-fit').FitAddon;
import {Events} from '../events';

export default {
    mounted() {
        var term = this.terminal = new Terminal({cursorBlink: true});
        term.onResize(this.onResize);

        const fitAddon = new FitAddon();
        term.loadAddon(fitAddon);

        setInterval(() => {
            fitAddon.fit();
        }, 1000);

        setTimeout(() => {
            fitAddon.fit();
        }, 10);

        term.prompt = () => {
            term.write('\r\n');
        };

        term.open(document.getElementById('terminal'));
        term.writeln('Welcome to TerminalMirror.com terminal emulator');
        term.writeln('Use at your own risk!');
        term.writeln('* Documentation https://github.com/LukaszTlalka/TerminalMirror');
        term.writeln('* Operation https://github.com/LukaszTlalka/TerminalMirror');

        term.writeln('');
        term.writeln('You are currently connected to the live terminal.')
        term.writeln('Type command to play around.');
        term.writeln('');
        term.prompt()

        let conn = this.wsConn = new WebSocket(WS.host + (WS.port ? (":" + WS.port) : '' ) + "/console-share?client=" + WS.client);

        conn.onmessage = (e) => {
            console.log("Received: " + e.data);
            let msg = JSON.parse(e.data);

            if (msg.c == 8) {
                term.write('\b \b');
            } else if (msg.c === 13) {
                term.prompt()
            } else {
                term.write(msg.k)
            }
        };

        conn.onopen = (e) => {

            term.onData(data => {
                this.sendInput(data);

                return;
            });

            /** This method does not allow to have input other than key up
            term.onKey(e => {
                // var printable = !e.domEvent.altKey && !e.domEvent.altGraphKey && !e.domEvent.ctrlKey && !e.domEvent.metaKey;
                this.sendInput(e.key);
                    return;
                if (e.domEvent.keyCode === 13) {
                    term.prompt()
                } else if (e.domEvent.keyCode === 8) {
                    // Do not delete the prompt
                    if (term._core.buffer.x > 2) {
                        term.write('\b \b');
                    }
                } else if (printable) {
                    term.write(e.key);
                }
            });
            */
        };
    },
    data: function () {
        return {
            terminal: null,
            wsConn: null,
            selection: null,
        }
    },
    watch: {

    },
    methods: {
        onSelection(selection) {
            this.selection = selection;
        },
        copy(event) {
            console.log(this.terminal.getSelection())
            navigator.clipboard
                .writeText(this.selection);
        },
        paste(event) {
            navigator.clipboard
                .readText()
                .then((cliptext) => {
                    this.sendInput(cliptext);
                })
        },

        onResize(size) {
            Events.$emit('terminalResized', size);
        },

        async run(command) {
            this.terminal.focus();

            if (command.charAt(command.length - 1) !== '\n') {
                command += '\n';
            }

            this.sendInput(command);
        },

        sendInput(input) {
            var msg = JSON.stringify({m: input});
            console.log("Sending", msg);
            this.wsConn.send(msg);
        },
    }
}
</script>


