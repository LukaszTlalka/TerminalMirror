<style scoped>

.art-block {
    background: linear-gradient(270deg, rgba(2,0,36,0.75) 0%, #0d0a18 100%), url(https://grainy-gradients.vercel.app/noise.svg);
    padding: 1rem;
    
}
.bar {
  height: 1rem;
  background: rgb(68, 60, 104);
  background: linear-gradient(90deg, rgb(68, 60, 104, 0.6) 0%, rgb(68, 60, 104) 100%), url(https://grainy-gradients.vercel.app/noise.svg);
  margin: 1rem 5px;
}

.input {
    color: rgb(99, 89, 133);
    font-weight: 900;
    display: flex;
    gap: 1rem;
    align-items: center;

}
.input-bar {
    display: block;
    background: rgb(99, 89, 133);
    background: linear-gradient(90deg, rgb(99, 89, 133, 0.6) 0%, rgb(99, 89, 133) 100%), url(https://grainy-gradients.vercel.app/noise.svg);
    height: 1rem;
}

.console-input {
    display: block;
}

</style>

<template>
    <div>
    <div class="user-select-none art-block">
        <div v-for="line in lines" :key="line" class="bar" :style="`width: max(10%, ${line*100}%)`">
        </div>
        <div class="input">
            <span class="console-input"><i class="fas fa-terminal"></i></span>
            <div class="input-bar" :style="`width: max(15%, ${input*100}%)`"></div>
        </div>
    </div>
</div>
</template>

<script>

export default {
    data: () => {
        return {
            lines: [],
            input: 0.2,
        }
    },
    mounted() {
        for (let i =0; i < 6; i++) {
            this.lines.push(Math.random())
        }
        this.input = Math.random();
    },
    methods: {

    }
    
}
</script>