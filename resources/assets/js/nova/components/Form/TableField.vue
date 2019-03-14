<template>
    <div>
        <default-field :field="field" :full-width-content="true">
            <template slot="field">
                <div class="max-w-full overflow-auto">
                    <table class="table min-w-full">
                        <thead>
                        <tr>
                            <th class="text-left" v-for="header in field.fields">{{header.name}}</th>
                            <th class="td-fit"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="row in value">
                            <td class="text-left" v-for="header in field.fields">
                                {{ row[header.attribute] }}
                            </td>
                            <td class="td-fit">
                                <a href="#"
                                   @click.prevent="edit(row)"
                                   class="cursor-pointer text-70 hover:text-primary mr-3"
                                   :title="__('Edit')">
                                    <icon type="edit"/>
                                </a>
                                <a href="#"
                                   @click.prevent="remove(row)"
                                   class="cursor-pointer text-70 hover:text-primary mr-3"
                                   :title="__('Delete')">
                                    <icon type="delete"/>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex mt-4">
                    <button
                            class="btn btn-default btn-primary ml-auto"
                            @click.prevent="modalOpened = true">
                        Add row
                    </button>
                </div>
            </template>
        </default-field>
        <transition name="fade">
            <modals-add-table-row v-if="currentRow"
                                  @close="currentRow = null"
                                  @confirm="update"
                                  :fields="editFields"
            />
        </transition>
        <transition name="fade">
            <modals-add-table-row v-if="modalOpened"
                                  @close="modalOpened = false"
                                  @confirm="addRow"
                                  :fields="field.fields"
            />
        </transition>
    </div>
</template>
<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova'

    export default {
        mixins: [HandlesValidationErrors, FormField],

        data: () => ({
            value: [],
            currentRow: null,
            modalOpened: false,
        }),

        methods: {
            fill(formData) {
                formData.append(this.field.attribute, JSON.stringify(this.value))
            },

            addRow(row) {
                if (!Array.isArray(this.value)) {
                    this.value = [];
                }
                this.value.push(row);

                this.modalOpened = false;
            },

            remove(row) {
                this.value.splice(this.value.indexOf(row), 1);
            },

            edit(row) {
                this.currentRow = row;
            },

            update(row) {
                this.value[this.value.indexOf(this.currentRow)] = row;

                this.currentRow = null;
            }

        },

        computed: {
            editFields() {
                if(!this.currentRow) {
                    return null;
                }

                return this.field.fields.map((field) => {
                    return {
                        ...field,
                        value: this.currentRow[field.attribute]
                    };
                });
            }
        }
    }
</script>