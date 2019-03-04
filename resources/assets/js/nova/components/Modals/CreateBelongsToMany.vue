<template>
    <modal
            class="modal"
            tabindex="-1"
            role="dialog"
            @modal-close="$emit('close')"
    >
        <form
                @keydown="handleKeydown"
                @submit.prevent.stop="handleConfirm"
                class="bg-white rounded-lg shadow-lg overflow-hidden w-action-fields"
        >
            <div>
                <heading :level="2" class="pt-8 px-8">{{__('Attach ')}} {{ resourceName }}</heading>
                <div>
                    <!-- Validation Errors -->
                    <validation-errors :errors="errors"/>
                    <div
                            class="action"
                            v-for="field in fields"
                            :key="field.attribute"
                    >
                        <component
                                ref="fieldComponents"
                                :is="'form-' + field.component"
                                :field="field"
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
                            type="submit"
                            class="btn btn-default btn-primary"
                    >
                        <span>{{__('Attach')}}</span>
                    </button>
                </div>
            </div>
        </form>
    </modal>
</template>
<script>
    import {Errors} from 'laravel-nova'

    export default {
        props: ['fields', 'resourceName'],
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
                let values = {};

                for(let field of this.fields) {
                    let f = new FormData();
                    field.fill(f);
                    values[field.attribute] = f.get(field.attribute);
                }
                this.$emit('confirm', values);
            }
        }
    }
</script>