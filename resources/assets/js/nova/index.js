import ResultOverview from './views/ResultOverview';
import StorageOverview from './views/StorageOverview';
import Multiselect from 'vue-multiselect';
require('vue-multiselect/dist/vue-multiselect.min.css');

Nova.booting((Vue, router) => {

    // Lib Components
    Vue.component('multiselect', Multiselect);

    // Components
    const files = require.context('./components', true, /\.vue$/i);
    files.keys().map(key => {
        let path = key.split('/');
        let componentGroup = path.length > 2 ? path[path.length - 2] : false;
        let componentName = path[path.length - 1].split('.')[0];
        let fullComponentName =
            (componentGroup ? componentGroup.toLowerCase() + '-' : '')
            + camelToKebabCase(componentName);

        return Vue.component(fullComponentName, files(key));
    });


    // Routes

    router.addRoutes([
        {
            name: 'results',
            path: '/results',
            component: ResultOverview,
        },
        {
            name: 'storage-overview',
            path: '/storage-overview',
            component: StorageOverview,
            props: route => {
                return {
                    columns: route.query.columns,
                    page: route.query.page,
                    sampleType: route.query.sampleType,
                }
            },
        }
    ]);
})


function camelToKebabCase(string) {
    return string.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
}
