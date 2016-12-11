
export var routes = [
    {
        path: 'post',
        name: 'module:post',
        component:  resolve => require(['./components/PostList.vue'],resolve)
    },
    {
        path: 'post/:post_id',
        name: 'module:post:action',
        component:  resolve => require(['./components/PostAction.vue'],resolve)
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