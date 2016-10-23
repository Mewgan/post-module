
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
    },
    {
        path: 'module/content/list-post',
        name: 'module:content:list_post',
        component:  resolve => require(['./components/ListPostModule.vue'],resolve)
    },
    {
        path: 'module/content/single-post',
        name: 'module:content:single_post',
        component:  resolve => require(['./components/SinglePostModule.vue'],resolve)
    }
];


export var content_routes = {
    listPost:  (resolve) => { require(['./components/ListPostModule.vue'],resolve)},
    singlePost: (resolve) => { require(['./components/SinglePostModule.vue'],resolve)}
};


export default {
    routes,
    content_routes
}