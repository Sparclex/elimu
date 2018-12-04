<template>
    <div class="flex justify-center items-centers">
        <div class="w-full max-w-xl">
            <heading class="flex mb-3">Export Result Data</heading>
            <p class="text-90 leading-tight mb-8">Choose an assay to view or export all related results:</p>

            <div class="w-1/3">
                <multiselect :value="assay" :options="assays" keyed-by="value" label="display" @input="selectAssay"></multiselect>
            </div>

        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect';
    export default {
        components: {Multiselect},
        data() {
            return {
                assay: null,
                assays: [],
            }
        },
        mounted() {
            Nova.request()
            .get('/nova-api/results/associatable/assay?first=false&search=&withTrashed=false')
            .then(({data}) => this.assays = data.resources);
        },
        methods: {
            selectAssay(assay) {
                let link = document.createElement('a');
                link.href = '/nova-vendor/lims/result-overview/' + assay.value;
                link.download = assay.display;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }
</script>
