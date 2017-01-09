<style>

</style>

<template>
    <select2 v-if="posts.length > 0" :multiple="false" @updateValue="updateValue"
             :contents="posts" :val="[content[content_key]]" :id="'post-select-' + id"
             index="name"
             :label="false"></select2>
</template>

<script type="text/babel">

    import {mapActions} from 'vuex'
    import Select2 from '../../../../../Blocks/AdminBlock/Front/components/Helper/Select2.vue'

    import {post_api} from '../api'

    export default{
        components: {Select2},
        name: 'post-render-custom-field',
        props: {
            field: {
                type: Object,
                required: true
            },
            id: {
                default: 'default'
            },
            content: {
                default: ''
            },
            content_key: {
                default: 'value'
            }
        },
        data () {
            return {
                website_id: this.$route.params.website_id,
                posts: []
            }
        },
        methods: {
            ...mapActions(['read']),
            updateValue(value){
                this.$set(this.content, this.content_key, value);
            }
        },
        created(){
            let categories = [];
            if ('categories' in this.field.data) categories = this.field.data.categories;
            this.read({
                api: post_api.list_names + '/' + this.website_id, options: {
                    params: {categories: categories}
                }
            }).then((response) => {
                if ('resource' in response.data)
                    this.posts = response.data.resource;
            });
        }
    }
</script>