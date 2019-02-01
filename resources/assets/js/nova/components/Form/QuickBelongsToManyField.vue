<template>
    <div>
        <default-field :field="field">
            <form-label for="assay">
                {{field.name}}
            </form-label>
            <template slot="field">
                <div class="flex flex-wrap -mx-2 -my-2">
                    <div class="w-1/2 p-2" v-for="(data, index) in values">
                        <div class="rounded p-2 border border-40 relative cursor-pointer" @click="modify(data, index)">
                            <div class="absolute pin-t pin-r p-1 text-danger cursor-pointer" @click.stop="remove(index)"
                            >&times;
                            </div>
                            <table>
                                <tr v-for="f in field.fields">
                                    <th class="p-2 text-right">{{f.name}}</th>
                                    <td class="p-2 text-right">{{valueFor(f, data)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="w-1/2 p-2 flex flex-col">
                        <button class="hover:bg-80 hover:text-white flex-1 rounded
                        p-2 border border-dashed border-primary flex items-center
                        justify-center bg-none"
                                @click.prevent="createModalOpened = true">
                            Add
                        </button>
                    </div>
                </div>
                <button class="border-none padding-0 text-primary"></button>
            </template>
        </default-field>

        <transition name="fade">
            <modals-create-belongs-to-many
                    v-if="createModalOpened"
                    :fields="field.fields"
                    :resource-name="field.name"
                    @confirm="attach"
                    @close="createModalOpened = false"/>
        </transition>

        <transition name="fade">
            <modals-create-belongs-to-many
                    v-if="editModalOpened"
                    :fields="editFields"
                    :resource-name="field.name"
                    @confirm="update($event, editIndex)"
                    @close="editModalOpened = false"/>
        </transition>
    </div>
</template>
<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova'

    export default {
        props: ['errors'],
        mixins: [HandlesValidationErrors, FormField],
        data() {
            return {
                values: this.field.value,
                createModalOpened: false,
                editModalOpened: false,
                editIndex: -1,
                editFields: [],
            }
        },
        mounted() {
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
                formData.append(this.field.attribute, JSON.stringify(this.values));
            },

            attach(data) {
                this.values.push(data);
                this.createModalOpened = false;
            },

            valueFor(field, data) {
                if (!data[field.attribute]) {
                    return '-';
                }

                if (field.options) {
                    for (let option of field.options) {
                        if (option.value == data[field.attribute]) {
                            return option.label;
                        }
                    }
                }

                return data[field.attribute];
            },

            remove(index) {
                this.values.splice(index, 1);
            },

            modify(data, index) {
                this.editFields = [];
                for(let field of this.field.fields) {
                    this.editFields.push({...field, value: data[field.attribute]});
                }
                this.editIndex = index;
                this.editModalOpened = true;
            },

            update(data, index) {
                this.values[index] = data;
                this.editModalOpened = false;
                this.editFields = [];
                this.editIndex = -1;
            }
        },
    }
</script>
