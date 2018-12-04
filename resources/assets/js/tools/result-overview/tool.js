Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'result-overview',
            path: '/result-overview',
            component: require('./components/Tool'),
        },
    ]);
})
