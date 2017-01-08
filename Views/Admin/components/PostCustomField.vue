<style>
    .post-custom-field .radio-inline span{
        margin-right: 20px;
    }
</style>

<template>
    <div class="post-custom-field">
        <div class="form-group row">
            <div class="col-md-3">
                <h4>Catégorie d'article</h4>
                <span>Choisir une catégorie d'article à afficher ou laisser vide pour tout afficher</span>
            </div>
            <div class="col-md-9">
                <select2 :launch="true"
                         :val="field.data.categories"
                         @updateValue="updateValue" :emptyDefault="false"
                         :contents="categories"
                         :id="'post-category-select-' + line" index="name"
                         label="Catégorie"></select2>
            </div><!--end .col -->
        </div>
    </div>
</template>

<script type="text/babel">

    import Select2 from '../../../../../Blocks/AdminBlock/Front/components/Helper/Select2.vue'
    import {mapActions} from 'vuex'
    import {post_category_api} from '../api'

    export default{
        name: 'post-custom-field',
        components: {Select2},
        props: {
            field: {
                type: Object,
                required: true
            },
            line: {
                default: '0'
            }
        },
        data(){
            return {
                website_id: this.$route.params.website_id,
                categories: []
            }
        },
        methods: {
            ...mapActions([
               'read'
            ]),
            updateValue(value){
                this.$set(this.field.data, 'categories', value);
            }
        },
        created(){
            this.read({api: post_category_api.list_names + this.website_id}).then((response) => {
                if('resource' in response.data)
                    this.categories = response.data.resource;
            })
        },
        mounted(){
            if (!('categories' in this.field.data)) {
                this.field.data = {
                    categories: []
                };
            }
        }
    }
</script>