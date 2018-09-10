<template>
    <div style="max-height: 300px; overflow: scroll" class="w-full">
        <table cellpadding="0" cellspacing="0" class="table w-full overflow-x-scroll">
            <thead>
            <tr>
                <th v-for="header in headers" class="text-left">{{header}}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row in rows">
                <td v-for="column in row">{{column}}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    export default {
        props: ['data'],
        data() {
            return {
                rows: this.data,
            }
        },
        computed: {
            headers() {
                if(!this.rows.length) return [];
                return Object.keys(this.rows[0]);
            }
        }
    }
</script>
