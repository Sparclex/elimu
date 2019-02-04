<template>
    <div class="flex items-center">
        <label for="selectedStudy" class="inline-block text-80 pr-4 leading-tight">Study</label>
        <multiselect :options="studies"
                     id="selectedStudy"
                     keyed-by="id"
                     :customLabel="label"
                     placeholder="Select Study"
                     :allow-empty="false"
                     :value="selectedStudy"
                     @input="onChangeStudy"
        ></multiselect>
    </div>
</template>
<script>
    export default {
        props: ['study'],
        data() {
            return {
                studies: [],
                selectedStudy: null
            }
        },
        mounted() {
            Nova.request().get('/nova-vendor/lims/studies')
                .then(({data}) => {
                    this.studies = data;
                    this.selectedStudy = _.find(data, (study) => study.id === this.study)
                })
        },
        methods: {
            onChangeStudy(study) {
                this.selectedStudy = study;
                Nova.request()
                    .post('/nova-vendor/lims/studies/' + study.id + '/select')
                    .then(({data}) => {
                        this.$toasted.success(data.message);
                        window.location.reload();
                    });
            },
            label(study) {
                return study.study_id;
            }
        }
    }
</script>
