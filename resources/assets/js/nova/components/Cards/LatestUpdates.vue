<template>
    <card class="flex flex-col">
        <h1 class="px-3 pt-3 pb-1 text-80 font-normal text-3xl">Latest updates</h1>
        <ul class="text-90 text-sm px-3 pb-3">
            <li v-for="(change, index) in card.changes"
                class="flex px-1"
                :class="{
                    'bg-40': index % 2 == 0
                }"
            >
                <span>{{change.message}}</span>
                <span class="ml-auto text-xs text-80">{{change.time}}</span>
            </li>
        </ul>
    </card>
</template>

<script>
    export default {
        props: [
            'card',
        ],

        mounted() {

        },
    }
</script>
