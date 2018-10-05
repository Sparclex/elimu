<template>
    <component :is="'rdml-result'" :display-data="displayData" v-if="displayData"></component>
</template>

<script>
import api from '../api';
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],
    data() {
        return {
            displayData: false,
            loading: true
        }
    },
    mounted() {
        api.fetchData(this.resourceId).then(({data}) => {
            if(data.display) {
                this.displayData = data.data;
            }
            this.loading = false;
        });
    }
}
</script>
