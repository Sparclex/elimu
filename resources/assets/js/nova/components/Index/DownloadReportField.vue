<template>
    <div v-if="downloadable">
        <a @click="onClick"
           class="impersonate cursor-pointer text-70 hover:text-primary no-underline flex items-center"
           style="width:22px"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline
                    points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
        </a>
        <download-report-modal @close="handleClose" v-if="modalOpened" :field="field"></download-report-modal>
    </div>
</template>

<script>
    export default {
        props: ['resourceName', 'field'],
        data() {
            return {
                modalOpened: false,
            }
        },
        mounted() {
            let parentElement = this.$el.parentElement;
            parentElement.classList.add('td-fit');
            parentElement.style.paddingRight = '0px';
        },
        methods: {
            handleClose() {
                this.modalOpened = false;
            },
            onClick() {
                this.modalOpened = true;
            },
        },
        computed: {
            downloadable() {
                return this.field.fields.length > 0;
            }
        }
    }
</script>
<style scoped>
    .w-600 {
        width: 600px;
    }
</style>
