import * as actions from './actions'

export const state = {
    posts : {}
};

export const mutations = {
    ADD_POST (state,context,auth){
        state.auth = auth;
        context.$http.get('/admin/auth/login',{
            email: auth.email,
            password: auth.password,
            remember: auth.remember
        }).then(function(response){
            if(response.data.status == 'success'){
                window.location.href = window.location.protocol+'//'+window.location.host+response.data.target;
            }else{
                state.response = response.data.message;
            }
        })
    }
};

export default {
    state, mutations,actions
}
