<template>

        <panel-item :field="field">
            <template slot="value">
                <div class="max-w-full overflow-auto">
                    <table class="table min-w-full">
                        <thead>
                        <tr>
                            <th class="text-left" v-for="header in field.fields">{{header.name}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="row in field.value">
                            <td class="text-left" v-for="header in field.fields">
                                {{ row[header.attribute] }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </template>
        </panel-item>
</template>
<script>
    export default {
        props: ['field']
    }
</script>