<template>
    <loading-view :loading="initialLoading">
        <div class="w-full max-w-xl">
            <heading class="flex mb-3">Results</heading>
            <p class="text-90 leading-tight mb-8">Choose an assay to view or export all related results:</p>
        </div>
        <div class="flex -mx-4 items-center mb-6">
            <div class="w-1/3 px-4">
                <multiselect :value="assay" :options="assays" keyed-by="value" label="display"
                             @input="selectAssay"></multiselect>
            </div>
            <div class="w-1/3" v-if="response">
                <button class="btn btn-default btn-primary" @click.prevent="download">Download</button>
            </div>
        </div>

        <div class="flex" v-if="assay">
            <!-- Search -->
            <div
                    class="relative h-9 mb-6 flex-no-shrink"
            >
                <icon type="search" class="absolute search-icon-center ml-3 text-70"/>

                <input
                        data-testid="search-input"
                        class="appearance-none form-control form-input w-search pl-search"
                        :placeholder="__('Search')"
                        type="search"
                        v-model="search"
                        @keydown.stop="performSearch"
                        @search="performSearch"
                />
            </div>
        </div>

        <loading-card :loading="loading" v-if="assay">
            <div class="py-3 flex items-center border-b border-50">
                <div class="ml-auto px-3">
                    <dropdown
                            class-whitelist="flatpickr-calendar"
                            v-if="assay"
                    >
                        <dropdown-trigger
                                slot-scope="{ toggle }"
                                :handle-click="toggle"
                                class="bg-30 px-3 border-2 border-30 rounded"
                                :class="{ 'bg-primary border-primary': filtersAreApplied }"
                        >
                            <icon type="filter" :class="filtersAreApplied ? 'text-white' : 'text-80'"/>
                            <span v-if="filtersAreApplied" class="ml-2 font-bold text-white text-80">
                                {{ activeFilterCount }}
                            </span>
                        </dropdown-trigger>

                        <dropdown-menu slot="menu" width="290" direction="rtl" :dark="true">
                            <scroll-wrap :height="350">
                                <div v-if="filtersAreApplied" class="bg-30 border-b border-60">
                                    <button
                                            @click="clearFilters"
                                            class="py-2 w-full block text-xs uppercase tracking-wide text-center text-80 dim font-bold focus:outline-none"
                                    >
                                        {{ __('Reset Filters') }}
                                    </button>
                                </div>
                                <div>
                                    <h3 slot="default" class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
                                        {{ __('Per Page') }}
                                    </h3>

                                    <div class="p-2">
                                        <select
                                                slot="select"
                                                dusk="per-page-select"
                                                class="block w-full form-control-sm form-select"
                                                :value="perPage"
                                                @change="updatePerPageChanged"
                                        >
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Custom Filters -->

                                <div>
                                    <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">Target</h3>

                                    <div class="p-2">
                                        <select class="block w-full form-control-sm form-select"
                                                @input="changeTarget"
                                                :value="selectedTarget"
                                        >
                                            <option :value="null" selected>&mdash;</option>

                                            <option v-for="target in targets">
                                                {{ target }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">Status</h3>

                                    <div class="p-2">
                                        <select class="block w-full form-control-sm form-select"
                                                @input="changeStatus"
                                                :value="selectedStatus">
                                            <option :value="null" selected>&mdash;</option>

                                            <option v-for="status in stati" :value="status.value">
                                                {{ status.label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </scroll-wrap>
                        </dropdown-menu>
                    </dropdown>
                </div>
            </div>
            <div v-if="results.length === 0" class="flex justify-center items-center px-6 py-8">
                <div class="text-center">
                    <svg
                            class="mb-3"
                            xmlns="http://www.w3.org/2000/svg"
                            width="65"
                            height="51"
                            viewBox="0 0 65 51"
                    >
                        <g id="Page-1" fill="none" fill-rule="evenodd">
                            <g
                                    id="05-blank-state"
                                    fill="#A8B9C5"
                                    fill-rule="nonzero"
                                    transform="translate(-779 -695)"
                            >
                                <path
                                        id="Combined-Shape"
                                        d="M835 735h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1 1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1 1v2zm-5.364125-8H817v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H785c-3.313708 0-6-2.686292-6-6v-30c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375c5.053323.501725 9 4.765277 9 9.950625 0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM799 725h16v-8h-16v8zm0 2v8h16v-8h-16zm34-2v-8h-16v8h16zm-52 0h16v-8h-16v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8h-16zm18-12h16v-8h-16v8zm34 0v-8h-16v8h16zm-52 0h16v-8h-16v8zm52-10v-4c0-2.209139-1.790861-4-4-4h-44c-2.209139 0-4 1.790861-4 4v4h52zm1 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z"
                                />
                            </g>
                        </g>
                    </svg>

                    <h3 class="text-base text-80 font-normal mb-6">
                        {{
                        __('No :resource matched the given criteria.', {
                        resource: 'Result',
                        })
                        }}
                    </h3>
                </div>
            </div>
            <div class="overflow-hidden overflow-x-auto relative" v-else>
                <!-- Resource Table -->
                <resource-table
                        :authorized-to-relate="false"
                        resource-name="results"
                        :resources="results"
                        singular-name="Result"
                        :selected-resources="[]"
                        :selected-resource-ids="[]"
                        :actions-are-available="false"
                        :should-show-checkboxes="false"
                        ref="resourceTable"
                />
            </div>

            <!-- Pagination -->
            <pagination-links
                    v-if="response"
                    resource-name="Results"
                    :resources="results"
                    :resource-response="response"
                    @previous="selectPreviousPage"
                    @next="selectNextPage"
            >
            </pagination-links>
        </loading-card>
    </loading-view>
</template>

<script>
    import Multiselect from 'vue-multiselect';
    import {Errors, InteractsWithQueryString, Paginatable, PerPageable,} from 'laravel-nova'

    export default {
        components: {Multiselect},
        mixins: [InteractsWithQueryString, PerPageable, Paginatable],
        data() {
            return {
                page: 1,
                assay: null,
                assays: [],
                results: [],
                response: null,
                filters: [],
                targets: [],
                perPage: 25,
                search: '',
                selectedStatus: null,
                selectedTarget: null,
                initialLoading: true,
                loading: false,
                stati: [
                    {
                        label: 'Errors',
                        value: 'errors'
                    },
                    {
                        label: 'Valid',
                        value: 'valid'
                    },
                    {
                        label: 'Positive',
                        value: 'positive'
                    },
                    {
                        label: 'Negative',
                        value: 'negative'
                    },
                    {
                        label: 'Repetition needed',
                        value: 'repetition'
                    },
                    {
                        label: 'Standard deviation too high',
                        value: 'stddev'
                    },
                    {
                        label: 'Not enough values',
                        value: 'replicates'
                    }
                ]
            }
        },
        async created() {
            let {data} = await Nova.request()
                .get('/nova-api/results/associatable/assay?first=false&search=&withTrashed=false');
            this.assays = data.resources;

            this.initializeSearchFromQueryString();
            this.initializeAssayFromQueryString();
            this.initializePerPageFromQueryString();
            this.initializeCurrentPageFromQueryString();
            this.initializeStatusFromQueryString();
            this.initializeTargetFromQueryString();

            this.initialLoading = false;


            if (this.assay) {
                this.loading = true;
                await [this.fetchResults(), this.fetchTargets()];
                this.loading = false;
            }

            this.$watch(
                () => {
                    return (
                        this.currentSearch +
                        this.currentPage +
                        this.currentPerPage +
                        this.currentStatus +
                        this.currentAssay +
                        this.currentTarget
                    )
                },
                () => {
                    this.initializeSearchFromQueryString();
                    this.initializeAssayFromQueryString();
                    this.initializePerPageFromQueryString();
                    this.initializeCurrentPageFromQueryString();
                    this.initializeStatusFromQueryString();
                    this.initializeTargetFromQueryString();
                    if (this.assay) {
                        this.fetchTargets()
                        this.fetchResults()
                    }
                }
            )

        },
        methods: {
            initializeStatusFromQueryString() {
                this.selectedStatus = this.currentStatus;
            },

            initializeTargetFromQueryString() {
                this.selectedTarget = this.currentTarget;
            },

            initializeCurrentPageFromQueryString() {
                this.page = this.currentPage;
            },
            initializeAssayFromQueryString() {
                this.assay = this.currentAssay;
            },
            initializeSearchFromQueryString() {
                this.search = this.currentSearch;
            },

            selectAssay(assay) {
                if (!assay) {
                    this.$router.push({query: {}});
                } else {
                    this.updateQueryString({
                        'assay': assay.value
                    });
                }
            },
            async fetchResults() {
                let {data: response} = await Nova.request().get(`/nova-vendor/lims/results/${this.assay.value}`, {
                    params: {
                        page: this.page,
                        perPage: this.perPage,
                        status: this.selectedStatus,
                        target: this.selectedTarget,
                        search: this.search,
                    }
                });
                this.response = response;
                this.results = response.resources;
            },

            async fetchTargets() {
                let {data: response} = await Nova.request().get(`/nova-vendor/lims/results/${this.assay.value}/targets`);
                this.targets = response;
            },

            changeTarget(event) {
                this.updateQueryString({
                    [this.pageParameter]: 1,
                    'target': event.target.value
                });
            },

            changeStatus(event) {
                this.updateQueryString({
                    [this.pageParameter]: 1,
                    'status': event.target.value
                });
            },

            clearFilters() {
                this.updateQueryString({
                    [this.pageParameter]: 1,
                    'status': null,
                    'target': null
                });
            },

            async download() {
                let {data: response} = await Nova.request()
                    .get(`/nova-vendor/lims/results/${this.assay.value}/request-for-download`);

                let link = document.createElement('a');
                link.href = response.download;
                link.download = response.name;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },

            performSearch(event) {
                this.debouncer(() => {
                    // Only search if we're not tabbing into the field
                    if (event.which != 9) {
                        this.updateQueryString({
                            [this.pageParameter]: 1,
                            [this.searchParameter]: this.search,
                        })
                    }
                })
            },

            updatePerPageChanged(event) {
                this.perPage = event.target.value;

                this.perPageChanged()
            },

            debouncer: _.debounce(callback => callback(), 500),
        },
        computed: {

            pageParameter() {
                return 'analyzed-results_page'
            },

            searchParameter() {
                return 'analyzed-results_search'
            },

            perPageParameter() {
                return 'analyzed-results_per-page'
            },

            /**
             * Get the current search value from the query string.
             */
            currentSearch() {
                return this.$route.query[this.searchParameter] || ''
            },

            currentAssay() {
                if (!this.$route.query['assay']) {
                    return null;
                }

                for (let assay of this.assays) {
                    if (assay.value == this.$route.query['assay']) {
                        return assay;
                    }
                }

                return null;
            },

            currentTarget() {
                return this.$route.query['target'] || ''
            },

            currentStatus() {
                return this.$route.query['status'] || '';
            },

            filtersAreApplied() {
                return this.selectedTarget || this.selectedStatus;
            },
            activeFilterCount() {
                return [this.selectedTarget, this.selectedStatus].filter(function (value) {
                    return value != null;
                }).length;
            }
        }
    }
</script>
