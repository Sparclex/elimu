@if(Auth::user()->study_id)
<h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--sidebar-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
<span class="sidebar-label">Samples</span></h3>
<ul class="list-reset mb-8">
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'index', params: {resourceName: 'sample-informations'}}" class="text-white text-justify no-underline dim">
            Sample Informations
        </router-link>
    </li>
     <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'index', params: {resourceName: 'samples'}}" class="text-white text-justify no-underline dim">
            Samples
        </router-link>
    </li>
     <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'index', params: {resourceName: 'storages'}}" class="text-white text-justify no-underline dim">
            Storage
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
        <router-link :to="{name: 'index', params: {resourceName: 'input-parameters'}}" class="text-white text-justify no-underline dim">
            Input Parameters
        </router-link>
    </li>
</ul>


<h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--sidebar-icon)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sidebar-icon"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> <span class="sidebar-label">Results</span></h3>
<ul class="list-reset mb-8">
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'result-overview'}" class="text-white text-justify no-underline dim">
            Overview
        </router-link>
    </li>
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'lens', params: {resourceName: 'results', lens: 'invalid'}}" class="text-white text-justify no-underline dim">
            Invalid
        </router-link>
    </li>
</ul>
@endif
