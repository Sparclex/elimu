<template>
    <div>
        <field-wrapper v-for="(value, key) in field.value" :key="key">
            <div class="w-1/4 py-4">
                <form-label :for="field.name">
                    {{key.charAt(0).toUpperCase() + key.slice(1)}}
                </form-label>
            </div>
            <div class="w-3/4 py-4">
                {{value}}
            </div>
        </field-wrapper>

    </div>
</template>

<script>
    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],
    }
</script>
