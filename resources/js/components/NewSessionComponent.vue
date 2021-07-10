<template>
    <div class="container"></div>
</template>

<script>
    export default {
        props: ['route'],
        mounted() {

            let fetching = false;
            let chkInterval = setInterval(() => {
                if (fetching) return;
                fetching = true;

                $.ajax({
                    url: this.route,
                }).done(function(data) {
                    if (data.success == true) {
                        clearInterval(chkInterval)
                        window.location = data.redirect;
                    }

                    fetching = false;
                });
            }, 1000);
        }
    }
</script>

