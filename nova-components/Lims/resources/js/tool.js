Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'lims',
            path: '/lims',
            component: require('./components/Tool'),
        },
    ])
})
