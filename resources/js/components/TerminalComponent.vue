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
        term.writeln('Welcome to ConsoleShare.com terminal emulator');
        term.writeln('* Documentation https://consoleshare.com/documentation');
        term.writeln('* Usage https://consoleshare.com/usage');
        term.writeln('* Operation https://consoleshare.com/operation');

        term.writeln('');
        term.writeln('You are currently connected to the live terminal.')
        term.writeln('Type command to play around.');
        term.writeln('');
        term.prompt()

        term.onKey(e => {
            const printable = !e.domEvent.altKey && !e.domEvent.altGraphKey && !e.domEvent.ctrlKey && !e.domEvent.metaKey;

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


