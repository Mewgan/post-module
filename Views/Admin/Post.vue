<template>
    <div class="container">
        <router-view></router-view>
    </div>
</template>

<script  type="text/babel">
    import store from './store/store.js'
    export default {
        store
    }
</script>