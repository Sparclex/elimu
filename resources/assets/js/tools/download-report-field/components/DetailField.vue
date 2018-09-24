<template>
    <div>
        <field-wrapper>
            <div class="w-1/4 py-4">
                <form-label :for="field.name">

                </form-label>
            </div>
            <div class="w-3/4 py-4">

            </div>
        </field-wrapper>

    </div>
</template>

<script>
    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],
    }
</script>
