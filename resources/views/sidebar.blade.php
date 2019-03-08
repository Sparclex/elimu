@if(Auth::user()->study_id)
    <h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--sidebar-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
        <span class="sidebar-label">Samples</span></h3>
    <ul class="list-reset mb-8">
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'samples'}}" class="text-white text-justify no-underline dim">
                Samples
            </router-link>
        </li>
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'sample-types'}}" class="text-white text-justify no-underline dim">
                Sample Types
            </router-link>
        </li>
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'storage-overview'}" class="text-white text-justify no-underline dim">
                Storage
            </router-link>
        </li>
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'shipments'}}" class="text-white text-justify no-underline dim">
                Shipments
            </router-link>
        </li>
    </ul>

    <h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--sidebar-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg> <span class="sidebar-label">Virtual Freezer</span></h3>
    <ul class="list-reset mb-8">
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'oligos'}}" class="text-white text-justify no-underline dim">
                Oligos
            </router-link>
        </li>
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'reagents'}}" class="text-white text-justify no-underline dim">
                Reagents
            </router-link>
        </li>
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'controls'}}" class="text-white text-justify no-underline dim">
                Controls
            </router-link>
        </li>
    </ul>

    <h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="sidebar-icon" fill="none" stroke="var(--sidebar-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
        <span class="sidebar-label">Analysis</span></h3>
    <ul class="list-reset mb-8">
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'experiments'}}" class="text-white text-justify no-underline dim">
                Experiments
            </router-link>
        </li>
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'assays'}}" class="text-white text-justify no-underline dim">
                Assays
            </router-link>
        </li>
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'assay-definition-files'}}" class="text-white text-justify no-underline dim">
                Definition Files
            </router-link>
        </li>
    </ul>

    <h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--sidebar-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg> <span class="sidebar-label">Results</span></h3>
    <ul class="list-reset mb-8">
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'results'}" class="text-white text-justify no-underline dim">
                Results
            </router-link>
        </li>
    </ul>

    <h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--sidebar-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg> <span class="sidebar-label">Lab Journal</span></h3>
    <ul class="list-reset mb-8">
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'protocols'}}" class="text-white text-justify no-underline dim">
                SOPs
            </router-link>
        </li>
    </ul>

    <h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--sidebar-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> <span class="sidebar-label">Lab QC</span></h3>
    <ul class="list-reset mb-8">
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'instruments'}}" class="text-white text-justify no-underline dim">
                Instruments
            </router-link>
        </li>
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'maintenances'}}" class="text-white text-justify no-underline dim">
                Maintenances
            </router-link>
        </li>
        <li class="leading-tight mb-4 ml-8 text-sm">
            <router-link :to="{name: 'index', params: {resourceName: 'audits'}}" class="text-white text-justify no-underline dim">
                Audit
            </router-link>
        </li>
    </ul>
@endif
