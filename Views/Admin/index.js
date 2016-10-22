
export var routes = [
    {
        path: '/post',
        component:  resolve => require(['./components/PostList.vue'],resolve)
    },
    {
        path: '/post/create',
        component:  resolve => require(['./components/PostCreate.vue'],resolve)
    },
    {
        path: '/post/:id',
        component:  resolve => require(['./components/PostRead.vue'],resolve)
    }
];

export default {
    routes
}