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
            <div>
                <heading :level="2" class="pt-8 px-8">Import</heading>

                <div>
                    <!-- Validation Errors -->
                    <div class="bg-danger mx-8 py-6 rounded text-white my-4" v-if="errors.length > 0">
                        <ul>
                            <li v-for="error in errors">{{error[0]}}</li>
                        </ul>
                    </div>

                    <!-- Action Fields -->
                    <div class="action">
                        <default-field :field="{name: 'Template'}">
                            <template slot="field">
                                <a
                                        :href="templateUri"
                                        class="btn text-primary font-normal h-9 px-3 mr-3 btn-link"
                                >Download</a>
                            </template>
                        </default-field>
                    </div>
                    <div
                            class="action"
                            v-for="field in fields"
                            :key="field.attribute"
                    >
                        <component
                                :is="'form-' + field.component"
                                :errors="emptyErrors"
                                :resource-name="resourceName"
                                :field="field"
                        />
                    </div>
                </div>
            </div>

            <div class="bg-30 px-6 py-3 flex">
                <div class="flex items-center ml-auto">
                    <button
                            type="button"
                            @click.prevent="handleClose"
                            class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
                    >
                        {{ __('Cancel') }}
                    </button>

                    <button
                            ref="runButton"
                            :disabled="working"
                            type="submit"
                            class="btn btn-default btn-primary"
                    >
                        <loader v-if="working" width="30"></loader>
                        <span v-else>{{ __('Import') }}</span>
                    </button>
                </div>
            </div>
        </form>

    </modal>
</template>
<script>
import { Errors } from 'laravel-nova'
    export default {
        props: ['templateUri', 'resourceName'],
        data() {
            return {
                emptyErrors: new Errors(),
                errors: [],
                working: false,
                fields: [
                    {
                        component: 'file-field',
                        attribute: 'file',
                        name: 'Import File',
                    }
                ]
            }
        },

        methods: {
            handleClose() {
                this.$emit('close');
            },

            handleConfirm() {
                this.working = true;
                let formData = new FormData();
                for(let field of this.fields) {
                    field.fill(formData);
                }
                Nova.request()
                    .post(
                        '/nova-vendor/sparclex/nova-import-card/endpoint/' + this.resourceName,
                        formData
                    )
                    .then(({ data }) => {
                        this.$toasted.success(data.message);
                        this.$parent.$parent.$parent.getResources();
                        this.errors = new Errors();
                        this.$emit('close');
                    })
                    .catch(({ response }) => {
                        if (response.data.danger) {
                            this.$toasted.error(response.data.danger);
                            this.errors = [];
                        } else {
                            this.errors = response.data.errors;
                        }
                    })
                    .finally(() => {
                        this.working = false;
                        this.$refs.form.reset();
                    });
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
