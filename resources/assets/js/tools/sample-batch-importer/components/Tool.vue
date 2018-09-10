<template>
    <div>
        <heading class="mb-6">Laboratory Information Management System</heading>

        <loading-card :loading="loading" class="overflow-hidden">
            <h3 class="mr-3 text-base text-80 font-bold mb-4 px-4 pt-4">Sample Batch import</h3>
            <div class="flex border-b border-40">
                <div class="w-1/5 px-8 py-6">
                    <label for="study" class="inline-block text-80 h-9 pt-2">Study</label>
                </div>
                <div class="w-1/2 px-8 py-6">
                    <select
                        class="form-control form-select mb-3 w-full" id="study"
                    >
                        <option v-for="study in studies" :value="study.id">{{study.study_id}} {{study.name}}</option>
                    </select>
                </div>
            </div>
            <div class="flex">
                <div class="w-1/5 px-8 py-6">
                    <label for="sampleImporter" class="inline-block text-80 h-9 pt-2">CSV</label>
                </div>
                <div class="w-1/2 px-8 py-6">
                     <span class="form-file mr-4">
                        <input
                            ref="fileField"
                            class="form-file-input"
                            type="file"
                            id="sampleImporter"
                            name="name"
                            @change="changeFile"
                        />
                        <label for="sampleImporter" class="form-file-btn btn btn-default btn-primary">
                            {{__('Choose File')}}
                        </label>
                    </span>
                </div>
            </div>
            <div style="max-height: 300px; overflow: scroll">
                <table cellpadding="0" cellspacing="0" class="table w-full overflow-x-scroll" v-if="isDataLoaded">
                    <thead>
                    <tr>
                        <th v-for="header in headers" class="text-left">{{header}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="row in data">
                        <td v-for="column in row">{{column}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <div class="bg-30 flex px-8 py-4">
                    <loading-button @click="performImport" :progress="progress"/>
                </div>
            </div>
        </loading-card>
    </div>
</template>

<script>
    import LoadingButton from "./LoadingButton";
    import api from '../api';
    import Papa from 'papaparse';
    export default {
        components: {LoadingButton},
        data() {
            return {
                progress: 0,
                studies: [],
                data: [],
                loading: true,
                file: false
            };
        },
        mounted() {
            api.fetchAvailableStudies().then(({data}) => this.studies = data).then(() => this.loading = false);
        },
        methods: {
            changeFile() {
                this.data = [];
                this.file = this.$refs.fileField.files[0]
                Papa.parse(this.file, {
                    header: true,
                    skipEmptyLines: true,
                    step: (row) => {
                        this.data.push(row.data[0]);
                    },
                    complete: () => {
                    }
                });
            },
            performImport() {
                api.importBatchSamples(this.data).then(({data}) => {
                    this.$toasted.show(data.message, { type: 'success' })
                }).catch(({response}) => {
                    this.$toasted.show(response.data.message, { type: 'error' });
                });
                this.data = [];
                this.file = false;
            }
        },
        computed: {
            headers() {
                if (!this.data.length) {
                    return [];
                }
                return Object.keys(this.data[0]);
            },
            isDataLoaded() {
                return this.headers.length;
            }
        }
    }
</script>

<style>
    /* Scoped Styles */
</style>
