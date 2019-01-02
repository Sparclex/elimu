<template>
    <div class="flex w-full justify-end items-center mx-3" v-if="templateUri">
        <button class="btn btn-default btn-white" @click.prevent="modalOpened = !modalOpened">Import</button>

        <transition name="fade">
            <modals-import v-if="modalOpened"
                           @close="modalOpened = false"
                           @confirm="processImport"
                           :resourceName="resourceName"
                           :templateUri="templateUri"
            />
        </transition>
    </div>
</template>

<script>
    export default {
        props: ['resourceName'],
        data() {
            return {
                modalOpened: false,
                templateUri: '',
            }
        },

        async mounted() {
            let {data} = await Nova.request()
                .get(`/nova-vendor/lims/import-template/${this.resourceName}`);

            this.templateUri = data.template_uri;
        },

        methods: {
            processImport()
            {
                Nova.request({
                    method: 'post',
                    url: this.endpoint || `/nova-api/${this.resourceName}/action`,
                    params: this.actionRequestQueryString,
                    data: this.actionFormData(),
                })
                    .then(response => {
                        this.confirmActionModalOpened = false
                        this.handleActionResponse(response.data)
                        this.working = false
                    })
                    .catch(error => {
                        this.working = false

                        if (error.response.status == 422) {
                            this.errors = new Errors(error.response.data.errors)
                        }
                    })
            }
        }
    }
</script>
