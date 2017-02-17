
export var routes = [
    {
        path: 'post-list/:category?',
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
    listPost:  (resolve) => { require(['./components/Module/ListPostModule.vue'],resolve) },
    singlePost: (resolve) => { require(['./components/Module/SinglePostModule.vue'],resolve) },
    userListPost: (resolve) => { require(['./components/Module/UserListPostModule.vue'],resolve) }
};

export var field_routes = {
    postRenderCustomField: (resolve) => { require(['./components/CustomField/PostRenderCustomField.vue'],resolve) },
    postCustomField: (resolve) => { require(['./components/CustomField/PostCustomField.vue'],resolve) }
};

export default {
    routes,
    content_routes,
    field_routes
}