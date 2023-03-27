<template>
    <button class="btn btn-primary" @click="copy">{{ dataText }}</button>
</template>

<script>
import * as clipboard from 'clipboard-polyfill/text';

export default {
    name: "ClipboardButtonComponent",

    props: {
        target: {
            type: String,
            required: true,
        },

        text: {
            type: String,
            default: 'Copy',
        },
    },

    data() {
        return {
            dataText: this.text,
        };
    },

    methods: {
        copy() {
            clipboard.writeText(document.getElementById(this.target).innerText);
            this.dataText = 'Copied!';
            setTimeout(() => this.dataText = this.text, 1500);
        },
    },
}
</script>

