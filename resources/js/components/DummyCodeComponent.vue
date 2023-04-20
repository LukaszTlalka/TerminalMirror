<style scoped>

.bg-console {
    opacity: 0.3;
    filter: blur(2px);
    user-select: none;
    overflow-y: hidden;
    position: absolute;
    padding: 1rem;
    bottom: 0;
    width:100%;
}
.bg-console > p {
    padding: 0;
    margin: 0.5rem 0 0 0;
    text-indent: 0;
    line-height: normal;
    
}

</style>
<template>
    <div :style="`text-align: ${align}`" class="bg-console">
        <p v-for="row in console" :key="row.id">{{row.line}} <br v-if="row.line.length == 0"></p>
    </div>
</template>
<script>
import Scenerio from '../tutorial/dummy-code-scenerio.js'
export default {

    props: {
        masterData: { },
        role: { },
    },
    computed: {
        align() {
            return 'left'; //this.role == "slave" ? "right" : "left";
        },
    },
    
    data: () => {
        return {
            console: [{id: 0, line: ""}],
            scenerio: '',
        }
    },
    watch: {
        'console': function(val, oldVal){
            if (this.role == "master")
                this.$emit('masterUpdate', val);
        },
        'masterData': function(val, oldVal){
            if(this.role == "slave")
                this.console = val;

        }
    },
    methods: {
        async writeLetter(index, element) {
            await this.delay(100);
            this.console[index].line += element;
        },

        async writeLine(line) {
            let index = this.console.length;
            this.console.push({id: this.console[index-1].id+1, line: ""});
            switch(line.type){
                case "command":
                    this.console[index].line += line.start + " ";
                    for(let i = 0; i < line.content.length; i++) {
                        await this.writeLetter(index, line.content[i]);
                    }
                    break;
                case "server_require":
                    this.console[index].line += line.response + " ";
                    for(let i = 0; i < line.content.length; i++) {
                        await this.writeLetter(index, line.content[i]);
                    }
                    break;
                case "break":
                    await this.delay(50);
                    break;
                case "response":
                    this.console[index].line += line.response;
                    break;
            }
            await this.delay(100);
        },

        delay(ms) {
            ms = Math.floor(Math.random() * (ms - ms * 0.5)) + ms * 0.5;
            return new Promise(resolve => setTimeout(resolve, ms));
        },
    },
    
    async mounted() {
        if (this.role == "master")
            while(true) {
                this.scenerio = Scenerio.scenerios[Math.floor(Math.random() * Scenerio.scenerios.length)];
                for(let i = 0; i < this.scenerio.length; i++) {
                    if (this.console.length >= 50) this.console.shift();
                    await this.writeLine(this.scenerio[i]);
                }
            }
    },
}
</script>
