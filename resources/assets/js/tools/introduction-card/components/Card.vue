<template>
    <div class="flex justify-center items-centers">
        <div class="w-full max-w-xl">
            <heading class="flex mb-3">Get Started</heading>
            <p class="text-90 leading-tight mb-8">Welcome to LIMS! Get familiar with LIMS and explore the common process:</p>

            <card>
                <ol class="list-reset">
                    <li>
                        <router-link :to="{name: 'create', params: {resourceName: 'studies'}}" class="no-underline dim flex p-6 border-b border-50">
                            <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                            </div>

                            <div>
                                <heading :level="3" class="mb-3">Studies</heading>
                                <p class="text-90 leading-normal">Create a new Study. You are able to change the study information later on.</p>
                            </div>
                        </router-link>
                    </li>
                    <li>
                        <router-link :to="{name: 'index', params: {resourceName: 'users'}}" class="no-underline dim flex p-6 border-b border-50">
                            <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-unlock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>
                            </div>

                            <div>
                                <heading :level="3" class="mb-3">Authorize Users</heading>
                                <p class="text-90 leading-normal">Per default no user has access to a study and its data. Therefore you need to allow your team members to manage the data by granting them access. Click on the <strong>eye</strong> on the specific users and attach your newly created study.</p>
                            </div>
                        </router-link>
                    </li>
                    <li>
                        <div class="no-underline flex p-6 border-b border-50">
                            <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>

                            <div>
                                <heading :level="3" class="mb-3">Choose Study</heading>
                                <p class="text-90 leading-normal">Choose the study from the dropdown field in the topbar.</p>
                                <p class="text-80 leading-normal font-bold text-sm">Note: All newly authorized users need to refresh the page in order to be able to see the study in the dropdown.</p>
                            </div>
                        </div>
                    </li>
                    <li>
                        <router-link :to="{name: 'index', params: {resourceName: 'sample-informations'}}" class="no-underline dim flex p-6 border-b border-50">
                            <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-folder-plus"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                            </div>

                            <div>
                                <heading :level="3" class="mb-3">Insert Data</heading>
                                <dl class="text-90 leading-normal list-reset flex flex-wrap">
                                  <dt class="w-1/4 font-semibold">Sample Information</dt>
                                  <dd class="w-3/4">General data which applies on all types of a sample: Sample ID, Subject ID etc.</dd>
                                  <dt class="w-1/4 font-semibold">Sample</dt>
                                  <dd class="w-3/4">The different types of a sample which are connected to the sample information</dd>
                                </dl>
                            </div>
                        </router-link>
                    </li>
                    <li>
                        <router-link :to="{name: 'index', params: {resourceName: 'storages'}}" class="no-underline dim flex p-6 border-b border-50">
                            <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                            </div>

                            <div>
                                <heading :level="3" class="mb-3">Storage</heading>
                                <p class="text-90 leading-normal mb-1">The sample gets a storage place assigned by specifing a quantity when inserting a sample.</p>
                            </div>
                        </router-link>
                    </li>
                    <li>
                        <router-link :to="{name: 'lens', params: {resourceName: 'samples', lens: 'sample-registry'}}" class="no-underline dim flex p-6 border-b border-50">
                            <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            </div>

                            <div>
                                <heading :level="3" class="mb-3">Export</heading>
                                <p class="text-90 leading-normal mb-1">To export the whole inventory list choose <i>Sample Registry</i> from the lens dropdown in the sample overview, select all entries and choose <i>Download Excel</i> from the appearing dropdown menu.</p>
                            </div>
                        </router-link>
                    </li>
                    <li>
                        <div class="no-underline flex p-6 border-b border-50">
                            <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </div>

                            <div>
                                <heading :level="3" class="mb-3">Experiment</heading>
                                <p class="text-90 leading-normal mb-1">Before creating an experiment you need to check if the desired assay already exists.
                                Additionally you need to define the input parameters. These input parameters are used to validate the results.</p>
                                <p class="text-90 leading-normal">To create an experiment choose the included samples and select <i>Request Experiment</i> from the appearing dropdown.</p>
                                <p class="text-90 leading-normal">You can add results to an experiment by editing and uploading the result file. (At the moment only rdmls are working).</p>
                            </div>
                        </div>
                    </li>
                     <li>
                        <router-link :to="{name: 'index', params: {resourceName: 'samples'}}" class="no-underline dim flex p-6 border-b border-50">
                            <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            </div>

                            <div>
                                <heading :level="3" class="mb-3">Validating results</heading>
                                <p class="text-90 leading-normal mb-1">All results connected to a sample are visible in the sample overview. You can choose to decline a result data to ignore it.</p>
                            </div>
                        </router-link>
                    </li>
                </ol>
                <!--<table class="w-full" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="align-top border-r border-b border-50">
                            <a href="resources" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Resources</heading>
                                    <p class="text-90 leading-normal">Nova's resource manager allows you to quickly view and manage your Eloquent model records directly from Nova's intuitive interface.</p>
                                </div>
                            </a>
                        </td>

                        <td class="align-top w-1/2 border-b border-50">
                            <a href="actions" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44"><g id="Page-1" fill="none" fill-rule="evenodd"><g id="icon-actions" fill="var(--primary)" fill-rule="nonzero"><path id="Shape" d="M22 44C9.8497355 44 0 34.1502645 0 22S9.8497355 0 22 0s22 9.8497355 22 22-9.8497355 22-22 22zm0-2c11.045695 0 20-8.954305 20-20S33.045695 2 22 2 2 10.954305 2 22s8.954305 20 20 20zm3-24h5c.3638839-.0007291.6994429.1962627.8761609.5143551.176718.3180924.1666987.707072-.0261609 1.0156449l-10 16C20.32 36.38 19 36 19 35v-9h-5c-.3638839.0007291-.6994429-.1962627-.8761609-.5143551-.176718-.3180924-.1666987-.707072.0261609-1.0156449l10-16C23.68 7.62 25 8 25 9v9zm3.2 2H24c-.5522847 0-1-.4477153-1-1v-6.51L15.8 24H20c.5522847 0 1 .4477153 1 1v6.51L28.2 20z"/></g></g></svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Actions</heading>
                                    <p class="text-90 leading-normal">Actions perform tasks on a single record or an entire batch of records. Have an action that takes a while? No problem. Nova can queue them using Laravel's powerful queue system.</p>
                                </div>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-top w-1/2 border-r border-b border-50">
                            <a href="filters" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38"><g id="Page-1" fill="none" fill-rule="evenodd"><g id="icon-filters" fill="var(--primary)" fill-rule="nonzero"><path id="Shape" d="M36 4V2H2v6.59l13.7 13.7c.1884143.1846305.296243.4362307.3.7v11.6l6-6v-5.6c.003757-.2637693.1115857-.5153695.3-.7L36 8.6V6H19c-.5522847 0-1-.44771525-1-1s.4477153-1 1-1h17zM.3 9.7C.11158574 9.51536954.00375705 9.26376927 0 9V1c0-.55228475.44771525-1 1-1h36c.5522847 0 1 .44771525 1 1v8c-.003757.26376927-.1115857.51536954-.3.7L24 23.42V29c-.003757.2637693-.1115857.5153695-.3.7l-8 8c-.2857003.2801197-.7108712.3629755-1.0808485.210632C14.2491743 37.7582884 14.0056201 37.4000752 14 37V23.4L.3 9.71V9.7z"/></g></g></svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Filters</heading>
                                    <p class="text-90 leading-normal">Write custom filters for your resource indexes to offer your users quick glances at different segments of your data.</p>
                                </div>
                            </a>
                        </td>

                        <td class="align-top w-1/2 border-b border-50">
                            <a href="lenses" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36"><g id="Page-1" fill="none" fill-rule="evenodd"><g id="icon-lenses" fill="var(--primary)" fill-rule="nonzero"><path id="Shape" d="M4 8C1.790861 8 0 6.209139 0 4s1.790861-4 4-4 4 1.790861 4 4-1.790861 4-4 4zm0-2c1.1045695 0 2-.8954305 2-2s-.8954305-2-2-2-2 .8954305-2 2 .8954305 2 2 2zm0 16c-2.209139 0-4-1.790861-4-4s1.790861-4 4-4 4 1.790861 4 4-1.790861 4-4 4zm0-2c1.1045695 0 2-.8954305 2-2s-.8954305-2-2-2-2 .8954305-2 2 .8954305 2 2 2zm0 16c-2.209139 0-4-1.790861-4-4s1.790861-4 4-4 4 1.790861 4 4-1.790861 4-4 4zm0-2c1.1045695 0 2-.8954305 2-2s-.8954305-2-2-2-2 .8954305-2 2 .8954305 2 2 2zm9-31h22c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1H13c-.5522847 0-1-.44771525-1-1s.4477153-1 1-1zm0 14h22c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1H13c-.5522847 0-1-.4477153-1-1s.4477153-1 1-1zm0 14h22c.5522847 0 1 .4477153 1 1s-.4477153 1-1 1H13c-.5522847 0-1-.4477153-1-1s.4477153-1 1-1z"/></g></g></svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Lenses</heading>
                                    <p class="text-90 leading-normal">Need to customize a resource list a little more than a filter can provider? No problem. Add lenses to your resource to take full control over the entire Eloquent query.</p>
                                </div>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-top w-1/2 border-r border-b border-50">
                            <a href="metrics" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="37" height="36" viewBox="0 0 37 36"><g id="Page-1" fill="none" fill-rule="evenodd"><g id="icon-metrics" fill="var(--primary)" fill-rule="nonzero"><path id="Shape" d="M2 27h3c1.1045695 0 2 .8954305 2 2v5c0 1.1045695-.8954305 2-2 2H2c-1.1045695 0-2-.8954305-2-2v-5c0-1.1.9-2 2-2zm0 2v5h3v-5H2zm10-11h3c1.1045695 0 2 .8954305 2 2v14c0 1.1045695-.8954305 2-2 2h-3c-1.1045695 0-2-.8954305-2-2V20c0-1.1.9-2 2-2zm0 2v14h3V20h-3zM22 9h3c1.1045695 0 2 .8954305 2 2v23c0 1.1045695-.8954305 2-2 2h-3c-1.1045695 0-2-.8954305-2-2V11c0-1.1.9-2 2-2zm0 2v23h3V11h-3zM32 0h3c1.1045695 0 2 .8954305 2 2v32c0 1.1045695-.8954305 2-2 2h-3c-1.1045695 0-2-.8954305-2-2V2c0-1.1.9-2 2-2zm0 2v32h3V2h-3z"/></g></g></svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Metrics</heading>
                                    <p class="text-90 leading-normal">Nova makes it painless to quickly display custom metrics for your application. To put the cherry on top, we’ve included query helpers to make it all easy as pie.</p>
                                </div>
                            </a>
                        </td>

                        <td class="align-top w-1/2 border-b border-50">
                            <a href="cards" class="no-underline dim flex p-6">
                                <div class="flex justify-center w-11 flex-no-shrink mr-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36"><g id="Page-1" fill="none" fill-rule="evenodd"><g id="icon-cards" fill="var(--primary)" fill-rule="nonzero"><path id="Shape" d="M29 7h5c.5522847 0 1 .44771525 1 1s-.4477153 1-1 1h-5v5c0 .5522847-.4477153 1-1 1s-1-.4477153-1-1V9h-5c-.5522847 0-1-.44771525-1-1s.4477153-1 1-1h5V2c0-.55228475.4477153-1 1-1s1 .44771525 1 1v5zM4 0h8c2.209139 0 4 1.790861 4 4v8c0 2.209139-1.790861 4-4 4H4c-2.209139 0-4-1.790861-4-4V4c0-2.209139 1.790861-4 4-4zm0 2c-1.1045695 0-2 .8954305-2 2v8c0 1.1.9 2 2 2h8c1.1045695 0 2-.8954305 2-2V4c0-1.1045695-.8954305-2-2-2H4zm20 18h8c2.209139 0 4 1.790861 4 4v8c0 2.209139-1.790861 4-4 4h-8c-2.209139 0-4-1.790861-4-4v-8c0-2.209139 1.790861-4 4-4zm0 2c-1.1045695 0-2 .8954305-2 2v8c0 1.1.9 2 2 2h8c1.1045695 0 2-.8954305 2-2v-8c0-1.1045695-.8954305-2-2-2h-8zM4 20h8c2.209139 0 4 1.790861 4 4v8c0 2.209139-1.790861 4-4 4H4c-2.209139 0-4-1.790861-4-4v-8c0-2.209139 1.790861-4 4-4zm0 2c-1.1045695 0-2 .8954305-2 2v8c0 1.1.9 2 2 2h8c1.1045695 0 2-.8954305 2-2v-8c0-1.1045695-.8954305-2-2-2H4z"/></g></g></svg>
                                </div>

                                <div>
                                    <heading :level="3" class="mb-3">Cards</heading>
                                    <p class="text-90 leading-normal">Nova offers CLI generators for scaffolding your own custom cards. We’ll give you a Vue component and infinite possibilities.</p>
                                </div>
                            </a>
                        </td>
                    </tr>
                </table>-->
            </card>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Help',

        props: {
            card: Object,
        },
    }
</script>
