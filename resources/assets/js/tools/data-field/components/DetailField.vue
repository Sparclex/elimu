<template>
    <div>
        <heading class="mb-3">{{ field.name }}</heading>
        <card>
            <table
                class="table w-full"
                cellpadding="0"
                cellspacing="0"
                data-testid="resource-table"
            >
                <thead>
                <tr>
                    <!-- Select Checkbox -->
                    <th :class="{
                    'w-16' : shouldShowCheckboxes,
                    'w-8' : !shouldShowCheckboxes
                }">&nbsp;
                    </th>

                    <!-- Field Names -->
                    <th
                        v-for="field in fields"
                        :class="`text-${field.textAlign}`"
                    >
                        <!--@sort="requestOrderByChange(field)"-->
                        <sortable-icon
                            :resource-name="resourceName"
                            :uri-key="field.attribute"
                            v-if="field.sortable"
                        >
                            {{ field.indexName }}
                        </sortable-icon>

                        <span v-else>
                        {{ field.indexName }}
                    </span>
                    </th>

                    <th>&nbsp;<!-- View, Edit, Delete --></th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="(resource, index) in resources"
                    :testId="`${resourceName}-items-${index}`"
                    :key="resource.id.value"
                    :delete-resource="deleteResource"
                    :restore-resource="restoreResource"
                    is="resource-table-row"
                    :resource="resource"
                    :resource-name="resourceName"
                    :relationship-type="relationshipType"
                    :via-relationship="viaRelationship"
                    :via-resource="viaResource"
                    :via-resource-id="viaResourceId"
                    :via-many-to-many="viaManyToMany"
                    :checked="selectedResources.indexOf(resource) > -1"
                    :actions-are-available="actionsAreAvailable"
                    :should-show-checkboxes="shouldShowCheckboxes"
                    :update-selection-status="updateSelectionStatus"
                />
                </tbody>
            </table>
        </card>
        <resource-index
            :resource-name="field.resourceName"
            :via-resource="resourceName"
            :via-resource-id="resourceId"
            :via-relationship="field.belongsToManyRelationship"
            :relationship-type="'belongsToMany'"
            @actionExecuted="actionExecuted"
            :load-cards="false"
        />
    </div>
</template>

<script>
    export default {
        props: ['resourceName', 'resourceId', 'resource', 'field'],

        data() {
            return {
                resources: []
            }
        },
        methods: {
            /**
             * Handle the actionExecuted event and pass it up the chain.
             */
            actionExecuted() {
                this.$emit('actionExecuted')
            },
        },
    }
</script>
