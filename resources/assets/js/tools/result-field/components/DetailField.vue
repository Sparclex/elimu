<template>
    <component :is="field.value.type.toLowerCase() + '-result'" :data="field.value.data"></component>
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],
}
</script>
