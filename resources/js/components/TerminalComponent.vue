<template>
    <div class='full-page'>
        <div id="terminal" style="height:100%"></div>
    </div>
</template>

<script>

const Terminal = require('xterm').Terminal;
const FitAddon = require('xterm-addon-fit').FitAddon;

export default {
    mounted() {
        var term = new Terminal({cursorBlink: true});
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
        term.writeln('* Documentation https://terminalmirror.com/documentation');
        term.writeln('* Usage https://terminalmirror.com/usage');
        term.writeln('* Operation https://terminalmirror.com/operation');

        term.writeln('');
        term.writeln('You are currently connected to the live terminal.')
        term.writeln('Type command to play around.');
        term.writeln('');
        term.prompt()

        let conn = new WebSocket(WS.host + ":" + WS.port + "/console-share?client=" + WS.client);

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
            term.onKey(e => {

                var printable = !e.domEvent.altKey && !e.domEvent.altGraphKey && !e.domEvent.ctrlKey && !e.domEvent.metaKey;
                var msg = JSON.stringify({
                    c: e.domEvent.keyCode,
                    k: e.key
                });
                console.log("Sending", msg);
                conn.send(msg);

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
        };
    },
    data: function () {
        return {
        }
    },
    methods: {
        startTerminal() {
        }
    }
}
</script>


