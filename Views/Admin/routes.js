
export var routes = [
    {
        path: 'post',
        name: 'module:post',
        component:  resolve => require(['./components/PostList.vue'],resolve)
    },
    {
        path: 'post/create',
        name: 'module:post:create',
        component:  resolve => require(['./components/PostCreate.vue'],resolve)
    },
    {
        path: 'post/:post_id',
        name: 'module:post:read',
        component:  resolve => require(['./components/PostRead.vue'],resolve)
    }
];

export default {
    routes
}