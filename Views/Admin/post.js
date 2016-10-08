export var routes = [
    {
        path: '/post',
        component: function(resolve){
            require(['./Post.vue'],resolve);
        },
        
        children: [
            {
                path: '/',
                component: function(resolve){
                    require(['./components/PostList.vue'],resolve);
                }
            },
            {
                path: '/create',
                component: function(resolve){
                    require(['./components/PostCreate.vue'],resolve);
                }
            },
            {
                path: '/:id',
                component: function(resolve){
                    require(['./components/PostRead.vue'],resolve);
                }
            }
        ]
    }
];

export default {
    routes
}