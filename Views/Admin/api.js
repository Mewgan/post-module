export const post_api = {
    all: ADMIN_DOMAIN + '/module/post/all/',
    read: ADMIN_DOMAIN + '/module/post/read/',
    get_single_post_route: ADMIN_DOMAIN + '/module/post/get-single-post-route/',
    update_or_create: ADMIN_DOMAIN + '/module/post/update-or-create/',
    destroy: ADMIN_DOMAIN + '/module/post/delete/',
    change_state: ADMIN_DOMAIN + '/module/post/change-state/',
    list_table_values: ADMIN_DOMAIN + '/module/post/list-table-values/',
    list_names: ADMIN_DOMAIN + '/module/post/list-names/',
    emit_post_event: ADMIN_DOMAIN + '/module/post/emit-post-event/',
};

export const post_category_api = {
    list_names: ADMIN_DOMAIN + '/module/post-category/list-names/',
    create: ADMIN_DOMAIN + '/module/post-category/create/',
    update: ADMIN_DOMAIN + '/module/post-category/update/',
    destroy: ADMIN_DOMAIN + '/module/post-category/delete/'
};

