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
                                         :value="selectedBoxSize"
                                         @input="selectBoxSize"
                                />
                            </div>
                        </div>
                    </div>

                    <div v-if="selectedBoxSize">
                        <div class="py-8" v-if="loadingStorage">
                            <loader />
                        </div>

                        <div v-else class="max-w-full overflow-x-auto">
                            <table class="min-w-full">
                                <tr>
                                    <th class="td-fit p-2 bg-30 text-80"></th>
                                    <th v-for="column in numberOfColumns" class="p-2 bg-30 text-80">{{columnLabel(column)}}</th>
                                </tr>
                                <tr v-for="row in numberOfRows" class="border-b-2 border-40">
                                    <th class="td-fit p-2 bg-30 text-80">{{row}}</th>
                                    <td v-for="column in numberOfColumns"
                                        class="p-2 border-40"
                                        :class="{
                                            'border-r-2': column != numberOfColumns
                                        }">
                                        {{currentSample(row, column).fields[1].value}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <pagination-links
                            resource-name="storages"
                            :resources="samples"
                            :resource-response="resourceResponse"
                            @previous="selectPreviousPage"
                            @next="selectNextPage"
                        >
                        </pagination-links>
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
            page: {
                default: 1
            },
            columns: {
                default: 0,
            }
        },

        data() {
            return {
                loading: true,
                boxSizes: [],
                selectedBoxSize: null,
                samples: [],
                loadingStorage: true,
                resourceResponse: null,
                currentPage: this.page,
            }
        },
        async mounted() {
            await this.fetchBoxSizes();
            for(let boxSize of this.boxSizes) {
                if(boxSize.id.value == this.sampleType) {
                    this.selectBoxSize(boxSize);
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
                return axios.get('/nova-api/storages', {
                    params: {
                        perPage: this.boxSize,
                        viaResourceId: this.selectedBoxSize.id.value,
                        viaRelationship: 'storages',
                        relationshipType: 'hasMany',
                        page: this.currentPage,
                    }
                }).then(({data}) => {
                    this.resourceResponse = data;
                    this.samples = data.resources;
                    this.loadingStorage = false;
                    this.updateRoute();
                });
            },

            boxSizeLabel(boxSizeResource) {
                return boxSizeResource.fields[1].value;
            },

            selectBoxSize(boxSize) {
                this.selectedBoxSize = boxSize;
                this.fetchSamples();
            },

            currentSample(row, column) {
                let position = (row - 1) * this.numberOfColumns + column;

                let sample = {
                    fields: [
                        {},
                        {},
                        {},
                        {},
                        {},
                        {},
                    ]
                };

                if(this.sortedSamples[this.currentPage] && this.sortedSamples[this.currentPage][position]) {
                    sample = this.sortedSamples[this.currentPage][position];
                }
                return sample;
            },

            selectPreviousPage() {
                this.currentPage = parseInt(this.currentPage) - 1;
                this.fetchSamples();
            },

            selectNextPage() {
                this.currentPage = parseInt(this.currentPage) + 1;
                this.fetchSamples();
            },

            updateRoute() {
                this.$router.replace({path: '/storage-overview', query: {
                        sampleType: this.selectedBoxSize ? this.selectedBoxSize.id.value : '',
                        page: this.currentPage,
                        columns: this.numberOfColumns,
                    }}
                );
            },

            columnLabel(column) {
                return String.fromCharCode(97 + column).toUpperCase();
            }
        },

        computed: {
            boxSize() {
                if(!this.selectedBoxSize) {
                    return 0;
                }

                return this.selectedBoxSize.fields[3].value * this.selectedBoxSize.fields[4].value;
            },

            numberOfRows() {
                if(!this.selectedBoxSize) {
                    return 0;
                }

                return this.selectedBoxSize.fields[4].value;
            },

            numberOfColumns() {
                if(!this.selectedBoxSize) {
                    return 0;
                }

                return this.selectedBoxSize.fields[3].value;
            },

            sortedSamples() {
                let sortedSamples = {};
                this.samples.forEach((sample) => {
                    if(!sortedSamples[sample.fields[2].value]) {
                        sortedSamples[sample.fields[2].value] = {};
                    }
                    sortedSamples[sample.fields[2].value][sample.fields[3].value] = sample;
                });

                return sortedSamples;
            }
        }
    }
</script>
