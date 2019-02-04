<template>
    <div>
        <div class="w-full">
            <heading class="flex mb-3">Storage</heading>

            <loading-card :loading="loading">
                <div>
                    <div class="py-4 flex max-w-xl">
                        <div class="flex w-1/2 p-4">
                            <div class="w-1/2">
                                <label class="inline-block text-80 pt-2 leading-tight">Sample Type</label>
                            </div>
                            <div class="w-1/2">
                                <multiselect :options="boxSizes"
                                             :customLabel="boxSizeLabel"
                                             trackBy="id"
                                             :value="selectedSampleType"
                                             @input="selectSampleType"
                                             :allowEmpty="false"
                                />
                            </div>
                        </div>
                    </div>


                    <div v-if="selectedSampleType">
                        <div class="py-8" v-if="loadingStorage">
                            <loader/>
                        </div>
                        <div v-else>
                            <h2 class="p-4 font-normal">Plate P{{formattedPlate}}</h2>
                            <div class="max-w-full overflow-x-auto">
                                <table class="min-w-full">
                                    <tr>
                                        <th class="td-fit p-2 bg-30 text-80"></th>
                                        <th v-for="column in columns" class="p-2 bg-30 text-80">
                                            {{columnLabel(column)}}
                                        </th>
                                    </tr>
                                    <tr v-for="row in rows" class="border-b-2 border-40">
                                        <th class="td-fit p-2 bg-30 text-80">{{row}}</th>
                                        <td v-for="column in columns"
                                            class="p-2 border-40 text-center relative"
                                            :class="{
                                            'border-r-2': column != columns,
                                            'bg-40': samples[row - 1][column - 1].shipped
                                        }">
                                            <router-link :to="{name:'detail', params: {
                                                resourceName: 'samples',
                                                resourceId: samples[row - 1][column - 1].id
                                            }}"
                                            class="no-underline font-bold dim text-primary"
                                            >
                                                {{samples[row - 1][column - 1].sample_id}}
                                            </router-link>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="bg-20 rounded-b">
                            <nav class="flex justify-between items-center">
                                <button :disabled="currentPlate === 1"
                                        rel="prev"
                                        class="btn btn-link py-3 px-4"
                                        :class="{
                                            'text-80 opacity-50': currentPlate === 1,
                                            'text-primary dim': currentPlate > 1
                                        }"
                                        @click.prevent="selectPreviousPlate">
                                    Previous
                                </button>
                                <button rel="next"
                                        class="btn btn-link py-3 px-4 text-primary dim"
                                        @click.prevent="selectNextPlate">
                                    Next
                                </button>
                            </nav>
                        </div>

                    </div>

                </div>
            </loading-card>

        </div>
    </div>
</template>

<script>
    export default {
        props: {
            sampleType: {
                default: -1
            },
            plate: {
                default: 1
            }
        },

        data() {
            return {
                loading: true,
                boxSizes: [],
                selectedSampleType: null,
                samples: [],
                rows: 0,
                columns: 0,
                loadingStorage: true,
                currentPlate: this.plate,
            }
        },
        async mounted() {
            await this.fetchBoxSizes();
            for (let boxSize of this.boxSizes) {
                if (boxSize.id.value == this.sampleType) {
                    this.selectSampleType(boxSize);
                }
            }
            this.loading = false;
        },
        methods: {
            async fetchBoxSizes() {
                return axios.get('/nova-api/sample-types', {
                    params: {
                        viaResource: 'studies',
                        viaResourceId: window.selectedStudy,
                        viaRelationship: 'sampleTypes',
                        relationshipType: 'belongsToMany',
                        perPage: 100,
                    }
                }).then(({data}) => {
                    this.boxSizes = data.resources;
                });
            },

            async fetchSamples() {
                return axios.get('/nova-vendor/lims/storage/' + this.selectedSampleType.id.value, {
                    params: {
                        plate: this.currentPlate
                    }
                })
                    .then(({data}) => {
                        this.samples = data.data;
                        this.columns = data.size.columns;
                        this.rows = data.size.rows;
                        this.loadingStorage = false;
                        this.updateRoute();
                    });
            },

            boxSizeLabel(boxSizeResource) {
                return boxSizeResource.fields[1].value;
            },

            selectSampleType(sampleType) {
                this.selectedSampleType = sampleType;
                this.fetchSamples();
            },

            selectPreviousPlate() {
                if (parseInt(this.currentPlate) === 1) {
                    return;
                }
                this.currentPlate = parseInt(this.currentPlate) - 1;
                this.fetchSamples();
            },

            selectNextPlate() {
                this.currentPlate = parseInt(this.currentPlate) + 1;
                this.fetchSamples();
            },

            updateRoute() {
                this.$router.replace({
                        path: '/storage-overview', query: {
                            sampleType: this.selectedSampleType ? this.selectedSampleType.id.value : '',
                            plate: this.currentPlate,
                        }
                    }
                );
            },

            columnLabel(column) {
                return String.fromCharCode(97 + column - 1).toUpperCase();
            }
        },
        computed: {
            formattedPlate() {
                return this.currentPlate.toString().padStart(3, 0);
            }
        }
    }
</script>
