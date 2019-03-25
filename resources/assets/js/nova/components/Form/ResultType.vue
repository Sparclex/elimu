<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <select-control
                    :id="field.attribute"
                    v-model="value"
                    class="w-full form-control form-select"
                    :class="errorClasses"
                    :options="field.options"
            >
                <option value="" selected>{{ __('Choose an option') }}</option>
            </select-control>
            <help-text class="help-text mt-2" v-if="value">{{templateLink}}</help-text>
        </template>

    </default-field>
</template>

<script>
    import { FormField, HandlesValidationErrors } from 'laravel-nova'

    export default {
        mixins: [HandlesValidationErrors, FormField],

        methods: {
            /**
             * Provide a function that fills a passed FormData object with the
             * field's internal value attribute. Here we are forcing there to be a
             * value sent to the server instead of the default behavior of
             * `this.value || ''` to avoid loose-comparison issues if the keys
             * are truthy or falsey
             */
            fill(formData) {
                formData.append(this.field.attribute, this.value)
            },
        },

        computed: {
            templateLink()
            {
                if(!this.value) {
                    return null;
                }

                return '<a href="/nova-vendor/lims/definition-files/'+encodeURI(this.value)+'" target="_blank">' +
                    'Download Definition File Template' +
                    '</a>';
            }
        }
    }
</script>
