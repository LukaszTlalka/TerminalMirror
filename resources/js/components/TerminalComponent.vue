<template>
    <div
        class='full-page'
        v-on:keyup.esc.exact.prevent.stop="copy"
        v-on:keyup.ctrl.shift.86.prevent.stop="paste"
        @paste="paste"
    >
        <div id="terminal" style="height:100%"></div>
    </div>
</template>

<script>
const Terminal = require('xterm').Terminal;
const FitAddon = require('xterm-addon-fit').FitAddon;
import { Events } from '../events';

export default {
    mounted() {
        // Initialize Terminal
        var term = (this.terminal = new Terminal({ cursorBlink: true }));
        term.onResize(this.onResize);

        const fitAddon = new FitAddon();
        term.loadAddon(fitAddon);

        // Fit the terminal initially and periodically
        setInterval(() => {
            fitAddon.fit();
        }, 1000);

        setTimeout(() => {
            fitAddon.fit();
        }, 10);

        term.prompt = () => {
            term.write('\r\n');
        };

        // Open the terminal in the specified DOM element
        term.open(document.getElementById('terminal'));
        term.writeln('Welcome to TerminalMirror.com terminal emulator');
        term.writeln('Use at your own risk!');
        term.writeln('* Documentation https://github.com/LukaszTlalka/TerminalMirror');
        term.writeln('* Operation https://github.com/LukaszTlalka/TerminalMirror');
        term.writeln('');
        term.writeln('You are currently connected to the live terminal.');
        term.writeln('Type commands to interact.');
        term.writeln('');
        term.prompt();

        // Initialize WebSocket connection
        let conn = (this.wsConn = new WebSocket(
            WS.host + (WS.port ? ':' + WS.port : '') + '/console-share?client=' + WS.client
        ));

        conn.onmessage = (e) => {
            let msg = JSON.parse(e.data);

            if (msg.c == 8) {
                term.write('\b \b');
            } else if (msg.c === 13) {
                term.prompt();
            } else {
                term.write(msg.k);
            }
        };

        conn.onopen = (e) => {
            term.onData((data) => {
                this.sendInput(data);
                return;
            });
        };

        // Attach the beforeunload event listener for confirmation
        window.addEventListener('beforeunload', this.handleBeforeUnload);
    },
    beforeUnmount() {
        // Remove the beforeunload event listener to prevent memory leaks
        window.removeEventListener('beforeunload', this.handleBeforeUnload);
    },
    data() {
        return {
            terminal: null,
            wsConn: null,
            selection: null,
        };
    },
    methods: {
        onSelection(selection) {
            this.selection = selection;
        },
        copy(event) {
            navigator.clipboard.writeText(this.selection);
        },
        paste(event) {
            navigator.clipboard.readText().then((cliptext) => {
                this.sendInput(cliptext);
            });
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
            var msg = JSON.stringify({ m: input });
            this.wsConn.send(msg);
        },
        handleBeforeUnload(event) {
            // Customize the condition under which the confirmation should appear
            // For example, only prompt if the WebSocket is still open
            if (this.wsConn && this.wsConn.readyState === WebSocket.OPEN) {
                event.preventDefault();
                event.returnValue = ''; // Required for Chrome to show the prompt
                // The return statement is optional and can be omitted
                return '';
            }
            // If no prompt is needed, do nothing
        },
    },
};
</script>

<style scoped>
.full-page {
    width: 100%;
    height: 100%;
    /* Add any additional styling as needed */
}
</style>
