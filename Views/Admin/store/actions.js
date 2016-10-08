export const login = function(store,context,auth){
    store.dispatch('LOGIN',context,auth);
}