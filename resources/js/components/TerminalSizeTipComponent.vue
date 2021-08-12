<template>
    <div class="alert alert-warning alert-dismissible fade" :class="{show: this.shown}" role="alert">
        <strong>ⓘ Tip</strong>
        <p>
            If some applications are only using a small part of the browser window, you can adjust the size of the terminal
            like this: <br>
            <code>{{ command }}</code>
        </p>
        <button type="button" class="btn btn-warning btn-sm" @click="runResizeCommand">Run</button>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click.stop="hide">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>

<script>

import {Events} from "../events";

export default {
    name: "TerminalSizeTipComponent",

    props: {
        rows: {
            type: Number,
            default: 48,
        },
        cols: {
            type: Number,
            default: 160,
        },
    },

    data() {
        return {
            dataRows: this.rows,
            dataCols: this.cols,
            shown: true,
        };
    },

    computed: {
        command() {
            return `stty rows ${this.dataRows} cols ${this.dataCols}`;
        },
    },

    methods: {
        show() {
            this.shown = true;
        },

        hide() {
            this.shown = false;
        },

        runResizeCommand() {
            this.$root.$refs.terminal.run(this.command);
        },
    },

    created() {
        Events.$on('terminalResized', size => {
            this.dataRows = size.rows;
            this.dataCols = size.cols;
            this.show();
        });
    }
}

</script>
