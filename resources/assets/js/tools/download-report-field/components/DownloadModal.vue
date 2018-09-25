<template>
    <transition name="fade">
        <modal
            class="modal"
            tabindex="-1"
            role="dialog"
            @modal-close="$emit('close')"
        >
            <form
                @keydown="handleKeydown"
                @submit.prevent.stop="handleConfirm"
                class="bg-white rounded-lg shadow-lg overflow-hidden w-600"
            >
                <div>
                    <heading :level="2" class="pt-8 px-8">{{__('Generate Report')}}</heading>
                    <div>
                        <!-- Validation Errors -->
                        <validation-errors :errors="errors"/>

                        <!-- Action Fields -->
                        <div
                            class="action"
                            v-for="childField in field.fields"
                            :key="childField.attribute"
                        >
                            <component
                                :is="'form-' + childField.component"
                                :resource-name="resourceName"
                                :field="childField"
                                :errors="errors"
                            />
                        </div>
                    </div>
                </div>
                <div class="bg-30 px-6 py-3 flex">
                    <div class="flex items-center ml-auto">
                        <button type="button" @click.prevent="$emit('close')"
                                class="btn text-80 font-normal h-9 px-3 mr-3 btn-link">Cancel
                        </button>

                        <button
                            :disabled="working"
                            type="submit"
                            class="btn btn-default btn-primary"
                        >
                            <loader v-if="working" width="30"></loader>
                            <span v-else>{{__('Download')}}</span>
                        </button>
                    </div>
                </div>
            </form>
        </modal>
    </transition>
</template>
<script>
    import {Errors} from 'laravel-nova'

    export default {
        props: ['field'],
        data() {
            return {
                working: false,
                errors: new Errors(),
            }
        },
        methods: {
            /**
             * Stop propogation of input events unless it's for an escape or enter keypress
             */
            handleKeydown(e) {
                if (['Escape', 'Enter'].indexOf(e.key) !== -1) {
                    return
                }

                e.stopPropagation()
            },
            handleConfirm() {
                this.working = true;
                Nova.request({
                    method: 'post',
                    url: `/nova-vendor/lims/samples/${this.field.id}/report/`,
                    data: this.actionFormData(),
                })
                    .then(({data}) => {
                        this.$emit('close');
                        if (data.download) {
                            let link = document.createElement('a')
                            link.href = data.download
                            link.download = data.name
                            link.click()
                        }
                        this.working = false
                    })
                    .catch(error => {
                        this.working = false

                        if (error.response.status == 422) {
                            this.errors = new Errors(error.response.data.errors)
                        }
                    })
            },
            actionFormData() {
                return _.tap(new FormData(), formData => {
                    _.each(this.field.fields, field => {
                        field.fill(formData)
                    })
                })
            },
        }
    }
</script>
