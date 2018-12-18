<template>
    <div>
        <default-field :field="field">
            <form-label for="assay">
                Assay
            </form-label>
            <template slot="field">
                <multiselect :options="field.assays" :value="assay" @input="changeAssay" keyed-by="id" label="name"
                             id="assay" :allow-empty="false"></multiselect>
            </template>
        </default-field>
        <div v-if="assay && !loading">
            <div v-if="reagents.length">
                <default-field :field="field">
                    <form-label for="useExisting">
                        Use existing
                    </form-label>
                    <template slot="field">
                        <checkbox
                            class="py-2"
                            @input="useExisting = !useExisting"
                            id="useExisting"
                            name="useExisting"
                            :checked="useExisting"
                        />
                    </template>
                </default-field>
                <default-field :field="field" v-if="useExisting">
                    <form-label for="reagents">
                        Reagent
                    </form-label>
                    <template slot="field">
                        <multiselect :options="reagents" v-model="reagent" keyed-by="id" :custom-label="reagentLabel"
                                     id="reagents"></multiselect>
                    </template>
                </default-field>
            </div>
            <div v-if="!reagents.length || !useExisting" v-for="childField in field.reagentFields" :is="'form-' + childField.component"
                 :field="childField" ref="fields" :errors="errors"></div>
        </div>
        <div class="py-6" v-if="loading">
            <loader></loader>
        </div>
    </div>
</template>
<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova'
    import Multiselect from 'vue-multiselect';

    export default {
        props: ['errors'],
        mixins: [HandlesValidationErrors, FormField],
        components: {
            Multiselect
        },
        data() {
            return {
                assay: null,
                useExisting: true,
                reagents: [],
                loading: false,
                reagent: null
            }
        },
        methods: {
            /**
             * Provide a function that fills a passed FormData object with the
             * field's internal value attribute. Here we are forcing there to be a
             * value sent to the server instead of the default behavior of
             * `this.value || ''` to avoid loose-comparison issues if the keys
             * are truthy or falsey
             */
            fill(formData) {
                if(!this.assay || !this.useExisting) {
                    return;
                }
                formData.append('form[assay]', this.assay.id);
                formData.append('form[useExisting]', this.reagents.length && this.useExisting ? '1' : '0');
                if(this.useExisting && this.reagent) {
                    formData.append('form[reagent]', this.reagent.id);
                }
                if(!this.useExisting) {
                    for(let field of this.$refs.fields) {
                        field.fill(formData);
                    }
                }
            },

            changeAssay(assay) {
                this.loading = true;
                this.assay = assay;
                Nova.request().get('/nova-vendor/lims/assays/' + assay.id + '/reagents')
                    .then(({data}) => {
                        this.reagents = data;
                        this.loading = false;
                    });
            },

            reagentLabel({lot, name}) {
                return `${name} (${lot})`;
            }
        }
    }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
