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
                <p>Choisir une catégorie d'article à afficher ou laisser vide pour tout afficher</p>
            </div>
            <div class="col-md-9">
                <select2 v-if="categories.length > 0"
                         :val="field.data.categories"
                         @updateValue="updateValue" :emptyDefault="false"
                         :contents="categories"
                         :id="'post-category-select-' + line" index="name"
                         label="Catégorie"></select2>
                <span v-else>Aucuns contenus</span>
            </div><!--end .col -->
        </div>
    </div>
</template>

<script type="text/babel">

    import {mapActions} from 'vuex'
    import {post_category_api} from '../../api'

    export default{
        name: 'post-custom-field',
        components: {
            Select2: resolve => require(['../../../../../../Blocks/AdminBlock/Front/components/Helper/Select2.vue'], resolve),
        },
        props: {
            field: {
                type: Object,
                required: true
            },
            line: {
                default: 'default'
            }
        },
        data(){
            return {
                website_id: this.$route.params.website_id,
                categories: []
            }
        },
        methods: {
            ...mapActions(['read']),
            updateValue(value){
                this.$set(this.field.data, 'categories', value);
            }
        },
        created(){
            this.read({api: post_category_api.list_names + this.website_id}).then((response) => {
                if (response.data.resource !== undefined)
                    this.categories = response.data.resource;
            })
        },
        mounted(){
            if (this.field.data.categories === undefined) {
                this.field.data = {
                    categories: []
                };
            }
        }
    }
</script>