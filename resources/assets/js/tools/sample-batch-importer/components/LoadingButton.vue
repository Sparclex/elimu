<template>
    <button dusk="create-button" class="ml-auto btn btn-default btn-primary" :style="styles" @click="$emit('click')">
        {{__('Import')}}
    </button>
</template>
<script>
    export default {
        props: {
            progress: {
                type: Number,
                default: 0
            }
        },
        computed: {
            styles() {
                if(!this.progre)
                return 'background: linear-gradient(to right, var(--primary-dark) '+this.progress+'%,var(--primary) '+this.progress+'%,var(--primary) 100%);'
                /* FF3.6-15 */
             //   background: -webkit-linear-gradient(left, #ff8989 53%,#7ff8989 53%,#7db9e8 100%); /* Chrome10-25,Safari5.1-6 */
              //  background: linear-gradient(to right, #ff8989 53%,#7db9e8 53%,#7db9e8 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            }
        }
    }
</script>
