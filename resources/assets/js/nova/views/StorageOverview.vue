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
                        <div class="flex w-1/2 p-4" v-show="selectedBoxSize">
                            <div class="w-1/2">
                                <label class="inline-block text-80 pt-2 leading-tight">Number of Columns</label>
                            </div>
                            <div class="w-1/2">
                                <input type="number" class="w-full form-control form-input form-input-bordered"
                                :value="numberOfColumns" @input="changeNumberOfColumns">
                            </div>
                        </div>
                    </div>

                    <div v-if="numberOfRows > 0">
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
                                        class="p-2 border-r-2 border-40"
                                        :title="'B ' + currentSample(row, column).fields[4].value + ' P '
                                        + currentSample(row, column).fields[5].value">
                                        {{currentSample(row, column).fields[2].value}}
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


                    <div v-if="numberOfRows === -1" class="py-8 px-4">
                        <p>Invalid number of columns. {{boxSize}} can not be divided by {{numberOfColumns}}.</p>
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
                numberOfColumns: this.columns,
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

                this.setDefaultNumberOfColumns();
            },

            setDefaultNumberOfColumns() {
                let numberOfColumns = 0;

                switch (this.boxSize) {
                    case 81:
                        numberOfColumns = 9;
                        break;
                    case 96:
                        numberOfColumns = 12;
                        break;
                }

                this.changeNumberOfColumns(numberOfColumns);
            },

            changeNumberOfColumns(value) {
                this.loadingStorage = true;

                if(value.target !== undefined) {
                    this.numberOfColumns = parseInt(value.target.value);
                } else {
                    this.numberOfColumns = parseInt(value);
                }

                if(this.numberOfRows <= 0) {
                    return;
                }

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
                this.currentPage = this.currentPage - 1;
                this.fetchSamples();
            },

            selectNextPage() {
                this.currentPage = this.currentPage + 1;
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

                return this.selectedBoxSize.fields[3].value;
            },

            numberOfRows() {
                if(!this.selectedBoxSize) {
                    return 0;
                }

                if(!this.numberOfColumns) {
                    return 0;
                }

                if(this.boxSize % this.numberOfColumns !== 0) {
                    return -1;
                }

                return this.boxSize / this.numberOfColumns;
            },

            sortedSamples() {
                let sortedSamples = {};
                this.samples.forEach((sample) => {
                    if(!sortedSamples[sample.fields[4].value]) {
                        sortedSamples[sample.fields[4].value] = {};
                    }
                    sortedSamples[sample.fields[4].value][sample.fields[5].value] = sample;
                });

                return sortedSamples;
            }
        }
    }
</script>
