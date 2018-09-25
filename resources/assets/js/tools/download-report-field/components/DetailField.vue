<template>
    <field-wrapper>
        <div class="w-1/4 py-4">
            <form-label :for="field.name">
                Report
            </form-label>
        </div>
        <div class="w-3/4 py-4">
            <a href="#" @click.prevent="onClick" class="dim text-primary no-underline cursor-pointer flex items-center font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="mr-2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline
                    points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg> <span>Download</span>
            </a>
        </div>
        <download-report-modal @close="handleClose" v-if="modalOpened" :field="field"></download-report-modal>
    </field-wrapper>
</template>

<script>
    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],
        data() {
            return {
                modalOpened: false,
            }
        },
        methods: {
            handleClose() {
                this.modalOpened = false;
            },
            onClick() {
                this.modalOpened = true;
            },
        }
    }
</script>
