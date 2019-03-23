<template>
    <div v-if="dependenciesMet">
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>

        <div class="p-2">
            <select-control
                    :dusk="`${filter.name}-filter-select`"
                    class="block w-full form-control-sm form-select"
                    :value="value"
                    @change="handleChange"
                    :options="filter.options"
                    label="name"
            >
                <option value="" selected>&mdash;</option>
            </select-control>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            resourceName: {
                type: String,
                required: true,
            },
            filterKey: {
                type: String,
                required: true,
            },
            lens: String,
        },

        created() {
        },

        methods: {
            handleChange(event) {
                this.$store.commit(`${this.resourceName}/updateFilterState`, {
                    filterClass: this.filterKey,
                    value: event.target.value,
                })

                this.$emit('change')
            },

            getFilter(filterKey) {
                return this.$store.getters[`${this.resourceName}/getFilter`](filterKey)
            },

            dependencyValueIsValid(filter, allowedValues) {
                for (let value of allowedValues) {
                    if (filter.currentValue == value) {
                        return true;
                    }
                }

                return false;
            },
        },

        computed: {
            filter() {
                return this.getFilter(this.filterKey)
            },

            value() {
                return this.filter.currentValue
            },

            dependenciesMet() {
                if (!this.filter) {
                    return false;
                }

                let dependencies = this.filter.dependsOn;


                if (Array.isArray(dependencies)) {
                    // all dependencies have no value restriction
                    for (let filter of dependencies) {
                        if (this.getFilter(filter).currentValue === '') {
                            return false;
                        }
                    }
                } else {
                    for (let filter of Object.keys(dependencies)) {
                        if (Array.isArray(dependencies[filter])) {
                            // the dependency has value restrictions
                            if (!this.dependencyValueIsValid(this.getFilter(filter), dependencies[filter])) {
                                return false;
                            }
                        } else {
                            // there are dependencies without value restriction
                            if (this.getFilter(dependencies[filter]).currentValue === '') {
                                return false;
                            }
                        }
                    }
                }

                return true;
            }
        },
    }
</script>
