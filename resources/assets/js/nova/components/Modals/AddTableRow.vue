<template>
    <modal
            tabindex="-1"
            role="dialog"
            @modal-close="handleClose"
    >
        <form
                ref="form"
                autocomplete="off"
                @keydown="handleKeydown"
                @submit.prevent.stop="handleConfirm"
                class="bg-white rounded-lg shadow-lg overflow-hidden w-action-fields"
        >
            <div class="p-8">
                <heading :level="2">Add row</heading>
            </div>
            <div v-for="field in fields">
                <component
                        :is="'form-' + field.component"
                        :errors="errors"
                        :resource-name="field.attribute"
                        :field="field"
                />
            </div>

            <div class="bg-30 px-6 py-3 flex">
                <div class="ml-auto">
                    <button
                            type="button"
                            data-testid="cancel-button"
                            dusk="cancel-delete-button"
                            @click.prevent="handleClose"
                            class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button
                            id="confirm-delete-button"
                            ref="confirmButton"
                            data-testid="confirm-button"
                            type="submit"
                            class="btn btn-default btn-primary"
                    >
                        {{ __('Submit') }}
                    </button>
                </div>
            </div>
        </form>
    </modal>
</template>
<script>
    import {Errors} from 'laravel-nova'
    import _ from 'lodash';

    export default {
        props: {
            fields: {
                type: Array,
                required: true,
            }
        },
        data() {
            return {
                errors: new Errors(),
            }
        },
        methods: {
            handleClose() {
                this.$emit('close');
            },

            handleConfirm() {
                let form = new class {
                    constructor() {
                        this.items = {};
                    }

                    all() {
                        return this.items;
                    }

                    append(key, value) {
                        this.items[key] = value;
                    }
                };
                _.tap(form, formData => {
                    _.each(this.fields, field => {
                        field.fill(formData)
                    })
                });

                if (Object.values(form.all()).filter(value => value).length == 0) {
                    this.$emit('close');
                } else {
                    this.$emit('confirm', {
                        ...form.all()
                    });
                }
            },

            /**
             * Stop propogation of input events unless it's for an escape or enter keypress
             */
            handleKeydown(e) {
                if (['Escape', 'Enter'].indexOf(e.key) !== -1) {
                    return
                }

                e.stopPropagation()
            },
        }
    }
</script>