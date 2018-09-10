Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'Lims',
            path: '/Lims',
            component: require('./components/Tool'),
        },
    ])
})
