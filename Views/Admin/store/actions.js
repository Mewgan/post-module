export const updatePostState = ({commit}, {resource, state, ids}) => {
    return Vue.http.put(ADMIN_DOMAIN + '/theme/change-state/', {ids, state}).then((response) => {
        commit(types.SET_RESPONSE, {
            response: response.data
        });
        if (response.data.status == 'success')
            ids.forEach((id) => {
                commit(types.UPDATE_RESOURCE_VALUE, {
                    resource, id,
                    key: 'state',
                    value: state
                });
            });
        return response;
    });
};