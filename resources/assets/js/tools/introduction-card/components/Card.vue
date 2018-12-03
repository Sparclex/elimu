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
